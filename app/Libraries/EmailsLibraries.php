<?php

namespace App\Libraries;

use App\Models\PlatformModel;
use CodeIgniter\Config\Services;
use Exception;

class EmailsLibraries
{
    protected $envio;
    protected $remetente;
    protected $nomeRemetente;
    protected $modelConfig;
    protected $email;
    protected $config;

    public function __construct()
    {
        $this->email = Services::email();

        $data = $this->data();

        if ($data['ativar_smtp']) {
            $this->initializeSMTP($data);
        }

        $this->remetente     = $data['e-remetente'];
        $this->nomeRemetente = $data['n-remetente'];

        log_message('info', 'EmailsLibraries initialized successfully.');
    }

    protected function data(): array
    {
        $modelAdmin = new PlatformModel();
        $data = $modelAdmin->find(1);

        log_message('info', 'Data retrieved from PlatformModel.');

        return [
            'SMTPHost'    => $data['smtpHost'],
            'SMTPUser'    => $data['smtpUser'],
            'SMTPPass'    => $data['smtpPass'],
            'SMTPPort'    => intval($data['smtpPort']),
            'SMTPCrypto'  => $data['smtpCrypto'],
            'e-remetente' => $data['senderEmail'],
            'n-remetente' => $data['senderName'],
            'ativar_smtp' => $data['activeSmtp']
        ];
    }

    protected function initializeSMTP(array $data)
    {
        $config['protocol']   = 'smtp';
        $config['SMTPHost']   = $data['SMTPHost'];
        $config['SMTPUser']   = $data['SMTPUser'];
        $config['SMTPPass']   = $data['SMTPPass'];
        $config['SMTPPort']   = $data['SMTPPort'];
        $config['SMTPCrypto'] = $data['SMTPCrypto'];
        $config['mailType']   = 'html';
        $this->email->initialize($config);

        log_message('info', 'SMTP configuration initialized.');
    }

    public function send(string $email, string $assunto, string $message)
    {
        try {
            log_message('info', 'Preparing to send email.');

            $this->email->setFrom($this->remetente, $this->nomeRemetente);
            $this->email->setTo($email);
            $this->email->setSubject($assunto);
            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Email sent successfully to {$email}.");
            } else {
                $error = $this->email->printDebugger(['headers']);
                log_message('error', 'Email sending failed: ' . $error);
                throw new Exception('Falha ao enviar o email. Detalhes: ' . $error);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            throw $e;
        }
    }

    public function testarEnvioEmail(string $email, string $assunto, string $message): bool
    {
        try {
            log_message('info', 'Preparing to send test email.');

            $this->email->setFrom($this->remetente, $this->nomeRemetente);
            $this->email->setTo($email);
            $this->email->setSubject($assunto);
            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Test email sent successfully to {$email}.");
                return true;
            } else {
                $error = $this->email->printDebugger(['headers']);
                log_message('error', 'Test email sending failed: ' . $error);
                throw new Exception('Falha ao enviar o email de teste. Detalhes: ' . $error);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
}
