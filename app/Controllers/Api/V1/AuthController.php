<?php
// app/Controllers/Api/V1/AuthController.php
namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JwtConfig;
use App\Models\BlacklistModel;
use CodeIgniter\API\ResponseTrait;
use Google\Client as GoogleClient;
use App\Models\UsersModel;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected $userModel;
    protected $jwtConfig;

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
        $this->userModel = new \App\Models\UsersModel();
    }

    // Método para iniciar o login com Google
    /* public function googleLogin()
    {
        $client = new GoogleClient();
        $client->setClientId('');
        $client->setClientSecret('');
        $client->setRedirectUri('http://localhost:8181/api/v1/auth/google/callback');
        $client->addScope('email');
        $client->addScope('profile');

        // Redireciona para o URL de autenticação do Google
        $authUrl = $client->createAuthUrl();
        return redirect()->to($authUrl);
    }

    // Método de callback após o Google autenticar o usuário
    public function googleCallback()
    {
        try {
            $client = new GoogleClient();
            $client->setClientId('');
            $client->setClientSecret('');
            $client->setRedirectUri('http://localhost:8181/api/v1/auth/google/callback');

            $code = $this->request->getGet('code');

            if ($code) {
                $token = $client->fetchAccessTokenWithAuthCode($code);
                $client->setAccessToken($token);

                // Obtém os dados do perfil do usuário autenticado
                $oauth2 = new \Google\Service\Oauth2($client);
                $googleUser = $oauth2->userinfo->get();

                // Busca ou cria o usuário no banco de dados
                $userModel = new UsersModel();
                $user = $userModel->getUserByEmail($googleUser->email);

                if (!$user) {
                    // Retorna uma mensagem de erro se o email não estiver cadastrado no sistema
                    return $this->fail('Email not registered in the system', 404);
                }

                // Gera o JWT para o usuário autenticado
                $payload = [
                    'iss' => $this->jwtConfig->issuer,
                    'aud' => $this->jwtConfig->audience,
                    'iat' => time(),
                    'exp' => time() + $this->jwtConfig->tokenExpiration,
                    'sub' => $user['id'],
                    'role' => $user['role'],
                ];

                $jwt = JWT::encode($payload, $this->jwtConfig->jwtSecret, 'HS256');

                // Retorna o token JWT para o cliente
                return $this->respond(['token' => $jwt]);
            }
            return $this->fail('Failed to authenticate with Google', 400);
        } catch (\Exception $e) {

            return $this->fail($e->getMessage());
        }
    } */

    public function login()
    {
        try {

            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                // Utiliza o método failValidationErrors() do ResponseTrait para retornar erros de validação
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $email     = $this->request->getVar('email');
            $password  = $this->request->getVar('password');

            // Valida os dados de entrada
            if (empty($email) || empty($password)) {
                log_message('warning', 'Tentativa de login sem email ou senha.');
                return $this->fail('Email and password are required', 400);
            }

            try {
                $token = $this->userModel->login($email, $password);
                // Retorna o token como resposta
                return $this->respond(['token' => $token], 200);
            } catch (\Exception $e) {
                // Loga o erro de autenticação
                log_message('error', 'Erro na autenticação: ' . $e->getMessage());
                return $this->failUnauthorized($e->getMessage());
            }
        } catch (\Exception $e) {

            return $this->fail($e->getMessage());
        }
    }

    // Método para realizar logout e adicionar o token à blacklist
    public function logout()
    {
        $authHeader = $this->request->getServer('HTTP_AUTHORIZATION');

        if (!$authHeader) {
            return $this->fail('Authorization header not found', 401);
        }

        try {
            $token = explode(' ', $authHeader)[1];
            $decoded = JWT::decode($token, new Key($this->jwtConfig->jwtSecret, 'HS256'));

            // Adiciona o token à blacklist
            $blacklistModel = new BlacklistModel();
            $blacklistModel->insert([
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', $decoded->exp),
            ]);

            return $this->respond(['message' => 'Logout successful, token invalidated']);
        } catch (\Exception $e) {
            return $this->fail('Invalid token: ' . $e->getMessage(), 401);
        }
    }
}
