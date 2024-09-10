<?php

namespace App\Controllers\Api\V1;

use App\Config\JwtConfig;
use App\Models\UsersModel;
use OpenApi\Attributes as OA;

class UsersController extends BaseController
{
    
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $userModel;
    protected $jwtConfig;

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
        $this->userModel = new UsersModel();
    }

    #[OA\Get(
        path: "/api/v1/user/me",
        summary: "Obter informações do usuário autenticado",
        description: "Retorna as informações do usuário autenticado usando JWT",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Informações do usuário",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                        new OA\Property(property: "email", type: "string", example: "john.doe@example.com"),
                        new OA\Property(property: "role", type: "string", example: "PROFISSIONAL"),
                        new OA\Property(property: "type", type: "string", example: "cache")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autorizado")
        ]
    )]

    public function me()
    {
        try {
            return $this->respond($this->userModel->me());
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    #[OA\Get(
        path: "/api/v1/users",
        summary: "Lista de usuários - ROTA ADMIN",
        description: "Retorna uma lista de usuários.",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'sort_by',
                in: 'query',
                required: false,
                description: 'Campo para ordenação dos resultados',
                schema: new OA\Schema(type: 'string', enum: ['id', 'update'], default: 'id')
            ),
            new OA\Parameter(
                name: 'order',
                in: 'query',
                required: false,
                description: 'Ordem de ordenação (ASC ou DESC)',
                schema: new OA\Schema(type: 'string', enum: ['ASC', 'DESC'], default: 'ASC')
            ),
            new OA\Parameter(
                name: 's',
                in: 'query',
                required: false,
                description: 'Termo de busca para filtrar os clientes',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'limite',
                in: 'query',
                required: false,
                description: 'Número de itens por página',
                schema: new OA\Schema(type: 'integer', default: 15, maximum: 200)
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Número da página para paginação',
                schema: new OA\Schema(type: 'integer', default: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de usuários",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "cache"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "user", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "email", type: "string", example: "john.doe@example.com")
                            ]
                        ))
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 403, description: 'Usuário sem permissão'),
        ]
    )]

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
        return $this->respondNoContent();
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
        return $this->respondNoContent();
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
        return $this->respondNoContent();
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
        return $this->respondNoContent();
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
        return $this->respondNoContent();
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
        return $this->respondNoContent();
    }
}
