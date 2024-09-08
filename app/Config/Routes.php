<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('teste', 'Home::teste');
$routes->get('docs', 'DocsController::index');

/**
 * 
 */
$routes->get('anamnese/(:any)', 'Api\V1\AnamnesesController::slug/$1');



$routes->get('teste', function () {
    echo password_hash("123456", PASSWORD_BCRYPT);
});



// Rotas acessíveis para múltiplas roles
$routes->group('api/v1', ['filter' => 'jwt:PROFISSIONAL,TERAPEUTA_SI,SUPERADMIN'], function ($routes) {
    $routes->resource('anamneses', ['controller' => 'Api\V1\AnamnesesController']);
    //A busca pelo cliente está aberta para todos os usuários buscarem de acordo com seu próprio ID
    $routes->get('customers', 'Api\V1\CustomerController::index');
    //dados do usuário
    $routes->get('user/me', 'Api\V1\UsersController::me');
});

// Rotas acessíveis para múltiplas roles
$routes->group('api/v1', ['filter' => 'jwt:SUPERADMIN'], function ($routes) {
    //rota protegida para criação de usuários, porém precisa da lógica para adicionar um usuário a um plano
    $routes->resource('users', ['controller' => 'Api\V1\UsersController']);
});

//Rotas liberadas apenas para os profissionais e superadmins
$routes->group('api/v1', ['filter' => 'jwt:PROFISSIONAL,SUPERADMIN'], function ($routes) {
    $routes->resource('customers', ['controller' => 'Api\V1\CustomerController']);
});

// Rota de login sem filtro JWT
$routes->match(['post', 'options'],'api/v1/login', 'Api\V1\AuthController::login');
$routes->post('api/v1/login', 'Api\V1\AuthController::login');
$routes->get('api/v1/login', 'Api\V1\AuthController::aviso');
$routes->get('api/v1/logout', 'Api\V1\AuthController::logout');
$routes->get('api/v1/google', 'Api\V1\AuthController::googleLogin');
$routes->get('api/v1/auth/google/callback', 'Api\V1\AuthController::googleCallback');
$routes->post('api/v1/webhook/greem', 'Api\V1\WebhookController::greem');
