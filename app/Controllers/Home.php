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
        //
        return $this->respond([
            'status' => "Development",
            "version" => "1.0.0",
            "php" => phpversion()
        ]);
    }

    public function teste()
    {
        echo "teste";
    }
}
