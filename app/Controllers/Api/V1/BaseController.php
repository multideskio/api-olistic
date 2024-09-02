<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;


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
