<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;

class Home extends ResourceController
{
    use ResponseTrait;


    #[OA\Get(
        path: '/',
        summary: 'Endpoint de status',
        description: 'Retorna o status da aplicação',
        tags: ['Status'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status da aplicação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Development'),
                        new OA\Property(property: 'version', type: 'string', example: '1.0.0'),
                        new OA\Property(property: 'php', type: 'string', example: '8.1.2')
                    ]
                )
            )
        ]
    )]

    public function index()
    {

        $elapsedTime = microtime(true) - APP_START;

        // Calcula o uso de memória em MB
        $memoryUsage = memory_get_usage() / (1024 * 1024);

        return $this->respond([
            'status' => getenv("CI_ENVIRONMENT"),
            "version" => "0.1",
            "php" => phpversion(),
            "memory" => number_format($memoryUsage, 2) . ' MB', // Formata com 2 casas decimais
            "load"  => number_format($elapsedTime, 4) . ' seconds', // Formata com 4 casas decimais
            "docs" => site_url("docs")
        ]);
    }

    public function teste()
    {
        echo "teste";
    }
}
