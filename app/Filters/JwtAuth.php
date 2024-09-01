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

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        if (!$header) {
            return $this->unauthorizedResponse('Authorization header not found');
        }

        $token = explode(' ', $header)[1] ?? '';

        try {
            // Decodifica o token JWT e valida a assinatura
            $decoded = JWT::decode($token, new Key($this->jwtConfig->jwtSecret, 'HS256'));

            // Verifica se a role do usuário está dentro das permitidas
            $role = $decoded->role ?? null;

            // Se não tiver role no token ou role não for permitido, nega o acesso
            if (!$role || !in_array($role, $arguments)) {
                return $this->forbiddenResponse('Access denied for your role');
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
        // Não é necessário implementar nada aqui para autenticação JWT
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
