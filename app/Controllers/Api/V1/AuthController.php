<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JwtConfig;
use App\Models\BlacklistModel;
use CodeIgniter\API\ResponseTrait;
use Google\Client as GoogleClient;
use App\Models\UsersModel;
use OpenApi\Attributes as OA;


/**
 * @OA\PathItem(path="/api/v1")
 */
class AuthController extends BaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $jwtConfig;

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
        $this->userModel = new \App\Models\UsersModel();
    }

    
    #[OA\Post(
        path: "/api/v1/oauth",
        summary: "Login do usuário",
        description: "Autentica o usuário e retorna um token JWT",
        tags: ["Autenticação"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "usuario@exemplo.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token JWT gerado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR...")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciais inválidas"),
            new OA\Response(response: 400, description: "Dados de entrada inválidos")
        ]
    )]
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


    #[OA\Get(
        path: "/api/v1/logout",
        summary: "Logout do usuário",
        description: "Realiza logout e invalida o token JWT",
        tags: ["Autenticação"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Logout realizado com sucesso"),
            new OA\Response(response: 401, description: "Token inválido ou ausente")
        ]
    )]

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
