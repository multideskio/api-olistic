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
        version: "1.0.0",
        description: "`API para demonstrar endpoints básicos do sistema`<br><br>
        <ul>
            <li>Essa documentação está sendo desenvolvida gradualmente, todos os endpoints estão passando por uma revisão.</li>
            <li>Os endpoints que precisam estar com a autorização estão com um cadeado indicando o uso.</li>
            <li>Para gerar o token, utilize o endpoint login.</li>
            <li>Alguns endpoints estão bloqueados para o usuário `PROFISSIONAL` e `TERAPELTA_DE_SI`, peça a liberação para o desenvolvedor.</li>
        </ul>",
        contact: new OA\Contact(
            name: "Multidesk.io",
            email: "webmaster@multidesk.io",
            url: "https://terapia.conect.app"
        ),
        license: new OA\License(
            name: 'MIT'
        )
    ),
    servers: [
        new OA\Server(
            url: "http://localhost:8000",
            description: "Servidor local"
        ),
        new OA\Server(
            url: "https://terapia.conect.app",
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
