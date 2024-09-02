<?php

namespace App\Libraries;

use App\Models\LogsModel;
use App\Models\PlansModel;
use App\Models\SubscriptionsModel;
use App\Models\UsersModel;
use Exception;

class WebhookLibraries
{
    protected $modelUser;
    protected $modelPlan;
    protected $modelSubscriptions;

    public function __construct()
    {
        helper('auxiliar');

        $this->modelUser = new UsersModel();
        $this->modelPlan = new PlansModel();
        $this->modelSubscriptions = new SubscriptionsModel();

        log_message('info', 'WebhookLibraries initialized successfully.');
    }

    /**
     * Processa a transação baseada no status atual.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request Requisição contendo os dados do webhook.
     * @return array Resultado da execução.
     */
    public function processTransaction($request): array
    {
        $request = service('request');
        $currentStatus = $request->getJsonVar('currentStatus');

        log_message('info', "Processing transaction with status: {$currentStatus}");

        try {
            if ($currentStatus == 'paid') {
                $user = $this->getUserData($request);
                return $this->processPaidTransaction($request, $user);
            }

            if ($currentStatus == 'refunded') {
                $user = $this->getUserData($request);
                return $this->processRefundedTransaction($request, $user);
            }

            if ($currentStatus == 'chargedback') {
                $user = $this->getUserData($request);
                return $this->processChargebackTransaction($request, $user);
            }

            throw new Exception("Unknown status: {$currentStatus}");
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            throw $e;
        }
    }

    protected function processPaidTransaction($request, array $user): array
    {
        log_message('info', 'Processing paid transaction.');

        $modelLogs = new LogsModel();
        $resp = [];
        $modelPlan = new PlansModel();
        $idProduto = $request->getJsonVar('product.id') == 0 ? "" : $request->getJsonVar('product.id');
        $rowPlan = $modelPlan->where('idPlan', $idProduto)->first();

        if (empty($rowPlan)) {
            log_message('error', 'Plan not found for paid transaction.');
            throw new Exception('O plano não foi encontrado');
        }

        $searchUpdate = $this->modelSubscriptions->where(['idUser' => $user['id']])->findAll();

        if (count($searchUpdate)) {
            foreach ($searchUpdate as $row) {
                $this->modelSubscriptions->delete($row['id']);
            }
            $this->modelSubscriptions->insert([
                'idPlan' => $rowPlan['id'],
                'idUser' => $user['id']
            ]);
            $resp = ['message' => 'Inscrição atualizada'];
            $modelLogs->insert([
                'platformId' => 1,
                'idUser' => $user['id'],
                'type' => 'subscription_updated',
                'description' => 'Inscrição atualizada'
            ]);

            log_message('info', 'Subscription updated for user: ' . $user['id']);
        } else {
            $this->modelSubscriptions->insert([
                'idPlan' => $rowPlan['id'],
                'idUser' => $user['id']
            ]);
            $modelLogs->insert([
                'platformId' => 1,
                'idUser' => $user['id'],
                'type' => 'subscription_created',
                'description' => 'Inscrição criada'
            ]);
            $resp = ['message' => 'Inscrição criada'];

            log_message('info', 'Subscription created for user: ' . $user['id']);
        }

        $email = new EmailsLibraries;
        $email->send($user['email'], 'Seu acesso chegou', view('emails/subscription', $user));

        log_message('info', 'Email sent to user: ' . $user['email']);
        return $resp;
    }

    protected function processRefundedTransaction($request, array $user): array
    {
        log_message('info', 'Processing refunded transaction for user: ' . $user['id']);

        $searchUpdate = $this->modelSubscriptions->select('id')->where(['idUser' => $user['id']])->findAll();
        foreach ($searchUpdate as $row) {
            $this->modelSubscriptions->delete($row['id']);
        }

        log_message('info', 'Subscription cancelled for refunded transaction.');
        return ['status' => 'Inscrição cancelada'];
    }

    protected function processChargebackTransaction($request, array $user): array
    {
        log_message('info', 'Processing chargeback transaction for user: ' . $user['id']);

        $searchUpdate = $this->modelSubscriptions->select('id')->where(['idUser' => $user['id']])->findAll();
        foreach ($searchUpdate as $row) {
            $this->modelSubscriptions->delete($row['id']);
        }

        log_message('info', 'Subscription cancelled due to chargeback.');
        return ['status' => 'Inscrição cancelada por extorno'];
    }

    protected function getUserData($request): array
    {
        $request = service('request');
        $email = $request->getJsonVar('client.email');
        $rowUser = $this->modelUser->where('email', $email)->first();

        log_message('info', "Fetching user data for email: {$email}");

        if ($rowUser) {
            log_message('info', 'User found: ' . $rowUser['id']);
            return $rowUser;
        } else {
            $data = [
                'platformId' => 1,
                'name'     => $request->getJsonVar('client.name'),
                'email'    => $email,
                'password' => 'mudar@123',
                'token'    => gera_token()
            ];

            $this->modelUser->insert($data);
            $newUser = $this->modelUser->where('email', $email)->first();

            if ($newUser) {
                $modelLogs = new LogsModel();
                $modelLogs->insert([
                    'platformId' => 1,
                    'idUser' => $newUser['id'],
                    'type' => 'user_created',
                    'description' => 'Criou uma conta através de uma assinatura.'
                ]);

                log_message('info', 'New user created: ' . $newUser['id']);
                return $newUser;
            } else {
                log_message('error', 'Error creating new user for email: ' . $email);
                throw new Exception('Erro ao criar novo usuário');
            }
        }
    }
}
