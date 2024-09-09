<?php

namespace App\Models;

use App\Config\JwtConfig;
use CodeIgniter\Model;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['platformId', 'name', 'photo', 'email', 'phone', 'password', 'token', 'checked', 'admin'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeData'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['beforeData'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected $jwtConfig;

    public function __construct()
    {
        parent::__construct();
        $this->jwtConfig = new JwtConfig;
    }

    protected function beforeData(array $data): array{

        if (array_key_exists("password", $data["data"])) {
            $data["data"]["password"] = password_hash($data["data"]["password"], PASSWORD_BCRYPT);
        }

        return $data;
    }

    public function login($email, $password)
    {
        try {
            // Verifica o login (e-mail e senha)
            log_message('info', 'Tentativa de login para o email: ' . $email);
            $user = $this->verifyLogin($email, $password);
            
            if (!$user) {
                throw new \RuntimeException('Credenciais inválidas');
            }

            if($user['admin'] == 0){
                // Verifica se o usuário possui uma inscrição ativa
                log_message('info', 'Verificando inscrição ativa para o usuário ID: ' . $user['id']);
                $subscription = $this->verifySubscription($user['id']);
                if (!$subscription) {
                    throw new \RuntimeException('Usuário sem inscrição ativa');
                }

                // Verifica se o usuário possui um plano ativo
                log_message('info', 'Verificando plano ativo para o plano ID: ' . $subscription['idPlan']);
                $plan = $this->verifyPlan($subscription['idPlan']);
                if (!$plan) {
                    throw new \RuntimeException('Plano do usuário não encontrado');
                }

                // Determina permissão com base no plano
                $permission = $this->determinePermission($plan['permissionUser']);
                if (!$permission) {
                    throw new \RuntimeException('Usuário sem permissão de acesso');
                }
            }else{
                $permission = 'SUPERADMIN';
            }

            // Gera o payload do token JWT com base nos dados do plano
            $payload = $this->generateJwtPayload($user, $permission);

            // Gera o token JWT
            log_message('info', 'Gerando token JWT para o usuário ID: ' . $user['id']);
            $token = JWT::encode($payload, $this->jwtConfig->jwtSecret, 'HS256');

            // Loga o sucesso da autenticação
            log_message('info', 'Autenticação bem-sucedida para o usuário ID: ' . $user['id']);

            // Gerencia informações adicionais do cliente
            $this->manageCustomer($user);

            return $token;
        } catch (\Exception $e) {
            log_message('error', 'Erro durante o login: ' . $e->getMessage());
            throw $e; // Ou retorne um erro adequado
        }
    }

    // Métodos auxiliares para melhorar a clareza e modularização
    protected function determinePermission($permissionUser)
    {
        switch ($permissionUser) {
            case 1:
                log_message('info', 'PROFISSIONAL');
                return "PROFISSIONAL";
            case 2:
                log_message('info', 'TERAPEUTA_SI');
                return "TERAPEUTA_SI";
            case 3:
                log_message('info', 'SUPERADMIN');
                return "SUPERADMIN";
            default:
                return null;
        }
    }

    protected function generateJwtPayload($user, $permission)
    {
        return [
            'iss' => $this->jwtConfig->issuer,
            'aud' => $this->jwtConfig->audience,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $this->jwtConfig->tokenExpiration,
            'role' => $permission,
            'data' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name']
            ]
        ];
    }


    /**
     * Verifica o login do usuário.
     *
     * @param string $email E-mail do usuário.
     * @param string $pass Senha do usuário.
     * @return array Dados do usuário.
     * @throws Exception Se o e-mail ou a senha forem inválidos.
     */
    private function verifyLogin($email, $pass)
    {
        $rowLogin = $this->where('email', $email)->first();
        if (!$rowLogin) {
            throw new Exception('E-mail não encontrado');
        }
        if (!password_verify($pass, $rowLogin['password'])) {
            throw new Exception('Senha inválida');
        }


        return $rowLogin;
    }

    /**
     * Verifica se o usuário possui uma inscrição ativa.
     *
     * @param int $userId ID do usuário.
     * @throws Exception Se o usuário não tiver uma inscrição ativa.
     */
    private function verifySubscription($userId)
    {
        $modelSubscription = new SubscriptionsModel();
        $rowSubscription = $modelSubscription->where('idUser', $userId)->first();
        if (!$rowSubscription) {
            throw new Exception('Você não tem uma inscrição ativa.');
        }

        return $rowSubscription;
    }

    /**
     * Verifica se o usuário possui um plano ativo.
     *
     * @param int $userId ID do usuário.
     * @return array Dados do plano.
     * @throws Exception Se o usuário não tiver um plano ativo.
     */
    private function verifyPlan($userId)
    {
        $modelSubscription = new SubscriptionsModel();
        $rowSubscription   = $modelSubscription->where('idUser', $userId)->first();
        $modelPlan         = new PlansModel();
        $rowPlan           = $modelPlan->where('id', $rowSubscription['idPlan'])->first();
        if (!$rowPlan) {
            throw new Exception('O plano não está ativo.');
        }
        return $rowPlan;
    }

    private function manageCustomer($rowLogin)
    {
        $modelCustomers = new CustomersModel();
        $rowCustomers = $modelCustomers->where(['idUser' => $rowLogin['id'], 'email' => $rowLogin['email']])->first();
        if ($rowCustomers) {
            return $rowCustomers['id'];
        } else {
            $dataCustomers = [
                'idUser' => $rowLogin['id'],
                'name'   => $rowLogin['name'],
                'email'  => $rowLogin['email'],
                'photo'  => $rowLogin['photo'],
                'phone'  => $rowLogin['phone'],
            ];

            $idCustomers = $modelCustomers->insert($dataCustomers);

            $modelTime = new TimeLinesModel();
            $modelTime->insert(
                [
                    'idUser' => $rowLogin['id'],
                    'idCustomer' => $idCustomers,
                    'type' => 'create_customer'
                ]
            );
            return $idCustomers;
        }
    }

    public function me()
    {
        try {
            $request    = service('request');
            $authHeader = $request->getServer('HTTP_AUTHORIZATION');

            // Obtém o token JWT do cabeçalho da requisição
            if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                throw new \RuntimeException('Token not provided or invalid');
            }

            $token = $matches[1];

            // Decodifica o token JWT usando a chave secreta configurada
            $decoded = $this->decodeToken($token);
            $userData = $decoded->data ?? null; // Extrai o campo 'data' que contém as informações do usuário

            if (!$userData || !isset($userData->id)) {
                throw new \RuntimeException('User data not found in token');
            }

            $userId = $userData->id;

            // Usa o sistema de cache do CI4 com a chave baseada no ID do usuário
            $cacheKey = 'user_' . $userId;
            $user     = cache()->get($cacheKey);

            // Acessa a role diretamente de $decoded
            $role = $decoded->role ?? 'Role not specified';

            if ($user) {
                return [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $role,
                    'type'  => 'cache'
                ];
            } else {
                // Busca no banco de dados se não estiver no cache
                $user = $this->find($userId);
                if (!$user) {
                    throw new \RuntimeException('User not found');
                }
                // Armazena os dados do usuário no cache com expiração de 5 minutos
                cache()->save($cacheKey, $user, 600); // 300 segundos = 5 minutos
                return [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $role,
                    'type'  => 'update'
                ];
            }
            // Retorna a resposta com os dados do usuário
        } catch (\RuntimeException $e) {
            // Loga o erro para auditoria e retorna uma resposta amigável
            log_message('error', 'Erro ao obter dados do usuário: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // Tratamento genérico de erros inesperados
            log_message('error', 'Erro inesperado ao obter dados do usuário: ' . $e->getMessage());
            throw new \RuntimeException('An unexpected error occurred', 500);
        }
    }

    // Método separado para decodificação do JWT
    protected function decodeToken($token)
    {
        try {
            // Decodifica o token JWT usando a chave secreta e valida a assinatura
            $decoded = JWT::decode($token, new Key($this->jwtConfig->jwtSecret, 'HS256'));
            return $decoded; // Retorna o objeto decodificado
        } catch (\Exception $e) {
            throw new \RuntimeException('Invalid or expired token: ' . $e->getMessage(), 401);
        }
    }
}
