<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JwtConfig;
use App\Models\BlacklistModel; // Adiciona o modelo BlacklistModel

class JwtAuth implements FilterInterface
{
    protected $jwtConfig;
    protected $newToken = null; // Para armazenar o novo token, se renovado
    protected $blacklistModel; // Adiciona a propriedade do modelo BlacklistModel

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
        $this->blacklistModel = new BlacklistModel(); // Instancia o modelo BlacklistModel
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $this->unauthorizedResponse('Authorization header not found or malformed');
        }

        $token = $matches[1];

        // Verifica se o token está na blacklist
        if ($this->isTokenBlacklisted($token)) {
            return $this->unauthorizedResponse('Token is blacklisted');
        }

        try {
            // Decodifica o token JWT e valida a assinatura
            $decoded = JWT::decode($token, new Key($this->jwtConfig->jwtSecret, 'HS256'));

            // Verifica se a role do usuário está dentro das permitidas
            $role = $decoded->role ?? null;

            if (!$role || !in_array($role, $arguments)) {
                return $this->forbiddenResponse('Access denied for your role');
            }

            // Acessa o ID do usuário dentro de 'data'
            $uid = $decoded->data->id ?? null;

            // Verifica se o UID (ID do usuário) está presente no token decodificado
            if (!$uid) {
                log_message('error', 'UID não encontrado no token decodificado.');
                return $this->unauthorizedResponse('Token inválido: UID não encontrado');
            }

            // Calcula o tempo restante para expiração do token
            $currentTime = time();
            $timeRemaining = $decoded->exp - $currentTime;

            // Se o tempo restante for menor que o limite (ex: 600 segundos), renova o token
            if ($timeRemaining < 600) { // 10 minutos
                $this->newToken = $this->renewToken($decoded);
            }

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->unauthorizedResponse('Invalid token signature');
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->unauthorizedResponse('Token has expired');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token: ' . $e->getMessage());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Adiciona o novo token no cabeçalho da resposta, se houver
        if ($this->newToken) {
            $response->setHeader('X-Renewed-Token', $this->newToken);
        }
    }

    private function renewToken($decoded)
    {
        // Gera um novo payload com o tempo de expiração estendido
        $newPayload = [
            'iat' => time(),
            'exp' => time() + $this->jwtConfig->tokenExpiration, // Novo tempo de expiração
            'role' => $decoded->role,
            'data' => [
                'id' => $decoded->data->id, // Acessa o ID dentro de 'data'
                'email' => $decoded->data->email,
                'name' => $decoded->data->name
            ]
        ];

        // Gera o novo token com o payload atualizado
        return JWT::encode($newPayload, $this->jwtConfig->jwtSecret, 'HS256');
    }

    private function unauthorizedResponse($message)
    {
        return \Config\Services::response()
            ->setStatusCode(401)
            ->setJSON(['message' => $message]);
    }

    private function forbiddenResponse($message)
    {
        return \Config\Services::response()
            ->setStatusCode(403)
            ->setJSON(['message' => $message]);
    }

    private function isTokenBlacklisted($token)
    {
        log_message('info', 'Verificando token.');

        // Consulta o modelo BlacklistModel para verificar se o token está na blacklist
        $blacklistedToken = $this->blacklistModel->where('token', $token)->first();

        // Se encontrar o token na blacklist, retorna true
        return $blacklistedToken !== null;
    }
}
