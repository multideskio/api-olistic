<?php

namespace App\Controllers\Api\V1;

use App\Config\JwtConfig;
use CodeIgniter\RESTful\ResourceController;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;
use Predis\Client as PredisClient;
use Config\Redis as RedisConfig;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsersController extends ResourceController
{
    use ResponseTrait;
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */

    protected $predis;
    protected $userModel;
    protected $jwtConfig;

    public function __construct()
    {
        // Inicializa o Predis com as configurações do Redis
        $redisConfig = new RedisConfig();
        
        $this->jwtConfig = new JwtConfig();


        $this->predis = new PredisClient($redisConfig->default);

        $this->userModel = new UsersModel();
    }

    public function me()
    {
        try{
            return $this->respond($this->userModel->me());
        }catch(\Exception $e){
            return $this->fail($e->getMessage());
        }
    }


    public function me0()
    {

        try {
            // Obtém o token JWT do cabeçalho da requisição
            $authHeader = $this->request->getServer('HTTP_AUTHORIZATION');
            $token = explode(' ', $authHeader)[1];

            // Decodifica o token JWT já validado pelo filtro
            $decoded = json_decode(base64_decode(explode('.', $token)[1]), true);
            $userId = $decoded['sub'];

            // Usa o ID do usuário como chave de cache
            $cacheKey = 'user_' . $userId;
            $user = $this->predis->get($cacheKey);

            if (!$user) {
                // Busca no banco de dados se não estiver no cache
                $user = $this->userModel->find($userId);

                if (!$user) {
                    return $this->fail('User not found', 404);
                }

                // Serializa os dados do usuário e armazena no Redis com expiração de 5 minutos
                $this->predis->setex($cacheKey, 300, json_encode($user));
            } else {
                // Decodifica os dados do usuário se forem retornados do cache
                $user = json_decode($user, true);
            }

            return $this->respond([
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }


    public function index()
    {
        //
        $data = $this->userModel->select('id as user, name, email')->findAll();

        $cache = service('cache');
        $cacheKey = 'list_users';
        $users = $cache->get($cacheKey);

        if ($users) {
            return $this->respond(['status' => 'cache', 'data' => $data]);
        } else {
            $cache->save($cacheKey, $data, 300);
            return $this->respond(['status' => 'update', 'data' => $data]);
        }
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //

    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
    }
}
