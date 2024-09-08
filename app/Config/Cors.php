<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins' => ["*"],
        'allowedOriginsPatterns' => ["*"],
        'supportsCredentials' => true,
        'allowedHeaders' => ["*"],
        'exposedHeaders' => ["*"],
        'allowedMethods' => ["*"],
        'maxAge' => 7200,
    ];

    public array $api = [
        'allowedOrigins' => ["*"],
        'allowedOriginsPatterns' => ["*"],
        'supportsCredentials' => true,
        'allowedHeaders' => [],
        'exposedHeaders' => [],
        'allowedMethods' => [
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS'
        ],
        'maxAge' => 7200,
    ];

    public array $apiAdmin = [
        // Replicando a configuração para uso em APIs específicas
        'allowedOrigins' => ['http://localhost:8000', 'https://terapia.conect.app', 'http://5.161.224.69:8800'],
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
            'DELETE',
            'OPTIONS'
        ],
        'maxAge' => 7200,
    ];
}
