<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: "OLISTC API",
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
        new OA\Tag(name: "Customers", description: "Gerenciamento de clientes"),
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
    // Esta classe pode ser utilizada para definir anotações gerais e ser estendida pelos controladores específicos.
}
