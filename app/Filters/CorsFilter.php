<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        // Set CORS headers
        header('Access-Control-Allow-Origin: *'); // Use '*' para testes, especifique domínios para produção
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');

        // Handle OPTIONS method
        if ($request->getMethod() === 'options') {
            // Responde imediatamente para requisições OPTIONS
            return $response->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Optional: Pode adicionar cabeçalhos CORS na resposta aqui, se necessário
    }
}
