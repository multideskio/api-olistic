<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JwtConfig;

class JwtAuth implements FilterInterface
{
    protected $jwtConfig;
    protected $newToken = null; // Para armazenar o novo token, se renovado

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $this->unauthorizedResponse('Authorization header not found or malformed');
        }

        $token = $matches[1];

        try {
            // Decodifica o token JWT e valida a assinatura
            $decoded = JWT::decode($token, new Key($this->jwtConfig->jwtSecret, 'HS256'));

            // Verifica se a role do usuário está dentro das permitidas
            $role = $decoded->role ?? null;

            if (!$role || !in_array($role, $arguments)) {
                return $this->forbiddenResponse('Access denied for your role');
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
            'uid' => $decoded->uid // Outros dados que você pode precisar
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
}
