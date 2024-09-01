<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins' => [],
        'allowedOriginsPatterns' => [],
        'supportsCredentials' => false,
        'allowedHeaders' => [],
        'exposedHeaders' => [],
        'allowedMethods' => [],
        'maxAge' => 7200,
    ];

    public array $api = [
        // Replicando a configuração para uso em APIs específicas
        'allowedOrigins' => ['http://localhost:8000'],
        'allowedOriginsPatterns' => [],
        'supportsCredentials' => true,
        'allowedHeaders' => [
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'Accept',
            'Origin',
        ],
        'exposedHeaders' => [],
        'allowedMethods' => [
            'GET',
            'POST',
            'PUT',
            'DELETE'
        ],
        'maxAge' => 7200,
    ];
}
