<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;
//use Predis\Client as PredisClient;
//use Config\Redis as RedisConfig;

#[OA\OpenApi(
    info: new OA\Info(
        title: "API Terapia Holistica",
        version: "0.1",
        description: "API para demonstrar endpoints básicos",
        contact: new OA\Contact(email: "multidesk.io@gmail.com")
    ),
    servers: [
        new OA\Server(
            url: "http://localhost:8000",
            description: "Servidor local"
        ),
        new OA\Server(
            url: "https://terapia.conect.app",
            description: "Servidor online"
        ),
        new OA\Server(
            url: "http://5.161.224.69:8800",
            description: "Servidor online"
        )
    ],
    tags: [
        new OA\Tag(name: "Status", description: ""),
        new OA\Tag(name: "Autenticação", description: "Operações relacionadas à autenticação de usuários"),
        new OA\Tag(name: "Usuários", description: "Gerenciamento de usuários"),
        new OA\Tag(name: "Clientes", description: "Gerenciamento de clientes"),
        new OA\Tag(name: "Anamneses", description: "Gerenciamento de Anamneses"),
        new OA\Tag(name: "Webhooks", description: "Gerenciamento de Webhooks"),
        // Outras tags podem ser adicionadas aqui
    ]
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]

class BaseController extends ResourceController
{
    use ResponseTrait;

    // Esta classe pode ser utilizada para definir anotações gerais e ser estendida pelos controladores específicos.
    protected $request;
    protected $validation;

    //protected $predis;

    public function __construct()
    {
        //$redisConfig = new RedisConfig();
        //$this->predis = new PredisClient($redisConfig->default);

        $this->validation = \Config\Services::validation();

        $this->request = service('request');

        helper('auxiliar');
    }
}
