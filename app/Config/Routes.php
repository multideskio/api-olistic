<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Config/Routes.php
$routes->options('(:any)', function() {
    return service('response')
        ->setStatusCode(200)// Garante que o status é 200 OK
        //->setHeader('Access-Control-Allow-Origin', 'http://localhost:8000')  // Ajuste para a origem específica necessária
        //->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
        //->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
        ;
});

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

    $routes->get('anamneses/comparation', 'Api\V1\AnamnesesController::comparation');
    $routes->resource('anamneses', ['controller' => 'Api\V1\AnamnesesController']);


    $routes->put('tasks/order', 'Api\V1\TasksController::order');
    $routes->resource('tasks',  ['controller' => 'Api\V1\TasksController']);
    

    
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
    $routes->resource('appointments', ['controller' => 'Api\V1\AppointmentsController']);
});


$routes->post('api/v1/login', 'Api\V1\AuthController::login', ['filter' => 'throttle:10,hour']);
$routes->post('api/v1/magiclink', 'Api\V1\AuthController::magiclink', ['filter' => 'throttle:5,hour']);

// Rota de login sem filtro JWT
$routes->options('api/v1/login', 'Api\V1\AuthController::login');

//$routes->post('api/v1/login', 'Api\V1\AuthController::login', ['filter' => 'throttle:1,hour']);
$routes->get('api/v1/login', 'Api\V1\AuthController::aviso', ['filter' => 'throttle:100,hour']);

//recupera senha
$routes->post('api/v1/recover', 'Api\V1\AuthController::recover');

$routes->get('api/v1/logout', 'Api\V1\AuthController::logout');
$routes->get('api/v1/google', 'Api\V1\AuthController::googleLogin');
$routes->get('api/v1/auth/google/callback', 'Api\V1\AuthController::googleCallback');

//webhook greem
$routes->post('api/v1/webhook/greem', 'Api\V1\WebhookController::greem');
