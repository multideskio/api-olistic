<?php

namespace App\Controllers\Api\V1;

use OpenApi\Attributes as OA;

use App\Models\Tasks\V1\CreateTasks;
use App\Models\Tasks\V1\DeleteTasks;
use App\Models\Tasks\V1\GetTasks;
use App\Models\Tasks\V1\SearchTasks;
use App\Models\Tasks\V1\UpdateTasks;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class TasksController extends BaseController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */


    
    #[OA\Get(
        path: '/api/v1/tasks',
        summary: 'Listar todas as tarefas',
        description: 'Retorna uma lista de tarefas com paginação',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'sort_by',
                in: 'query',
                required: false,
                description: 'Campo para ordenação dos resultados',
                schema: new OA\Schema(type: 'string', enum: ['id', 'order', 'title'], default: 'id')
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
                description: 'Termo de busca para filtrar as tarefas',
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
                description: 'Lista de tarefas',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'rows', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'pagination', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]

    
    public function index()
    {
        //
        $searchTasks = new SearchTasks();
        $input = $this->request->getGet();

        $data = $searchTasks->listTasks($input);

        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */

     #[OA\Get(
        path: '/api/v1/tasks/{id}',
        summary: 'Obter detalhes de uma tarefa',
        description: 'Retorna os detalhes de uma tarefa específica pelo ID',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da tarefa',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalhes da tarefa retornados com sucesso',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]
    
    public function show($id = null)
    {
        //
        try {
            $getTask = new GetTasks();
            $data = $getTask->getId($id);
            return $this->respond($data);
        } catch (\RuntimeException $e) {
            return $this->failNotFound($e->getMessage());
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
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


     #[OA\Post(
        path: '/api/v1/tasks',
        summary: 'Criar uma nova tarefa',
        description: 'Cria uma nova tarefa com os dados fornecidos no corpo da requisição',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            description: 'Dados da nova tarefa',
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', description: 'Título da tarefa'),
                    new OA\Property(property: 'description', type: 'string', description: 'Descrição da tarefa (opcional)'),
                    new OA\Property(property: 'status', type: 'string', description: 'Status da tarefa (pending/completed)'),
                    new OA\Property(property: 'datetime', type: 'string', format: 'date-time', description: 'Data e hora da tarefa (opcional)')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tarefa criada com sucesso',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 400, description: 'Erro de validação'),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]
    
    public function create()
    {
        //
        try {

            $input = $this->request->getJSON(true);

            $rules = [
                'title'    => 'required'
            ];

            if (!$this->validate($rules)) {
                // Utiliza o método failValidationErrors() do ResponseTrait para retornar erros de validação
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $createTasks = new CreateTasks();
            $data = $createTasks->taskCreate($input);
            return $this->respondCreated($data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
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
    #[OA\Put(
        path: '/api/v1/tasks/{id}',
        summary: 'Atualizar uma tarefa existente',
        description: 'Atualiza os dados de uma tarefa específica pelo ID',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da tarefa',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Dados atualizados da tarefa',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', description: 'Título da tarefa'),
                    new OA\Property(property: 'description', type: 'string', description: 'Descrição da tarefa (opcional)'),
                    new OA\Property(property: 'status', type: 'string', description: 'Status da tarefa (pending/completed)'),
                    new OA\Property(property: 'datetime', type: 'string', format: 'date-time', description: 'Data e hora da tarefa (opcional)')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tarefa atualizada com sucesso',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 400, description: 'Erro de validação'),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]
    
    public function update($id = null)
    {
        //
        try {
            // Check if the ID was provided
            if (is_null($id)) {
                return $this->failValidationErrors('Appointment ID is required.');
            }
            $input = $this->request->getJSON(true);
            $updateTasks = new UpdateTasks();
            $data = $updateTasks->taskUpdate($input, $id);
            return $this->respond($data);
        } catch (\RuntimeException $e) {
            return $this->failNotFound($e->getMessage());
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }


    #[OA\Patch(
        path: '/api/v1/tasks/order',
        summary: 'Atualizar a ordem das tarefas',
        description: 'Atualiza a ordem de exibição das tarefas',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            description: 'IDs e suas novas ordens',
            required: true,
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', description: 'ID da tarefa'),
                        new OA\Property(property: 'order', type: 'integer', description: 'Nova ordem da tarefa')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ordem das tarefas atualizada com sucesso'),
            new OA\Response(response: 400, description: 'Erro de validação'),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]

    
    public function order()
    {
        $input = $this->request->getJSON(true);
        $updateTasks = new UpdateTasks();
        try {
            $data = $updateTasks->taskUpdateOrder($input);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }


    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */

    #[OA\Delete(
        path: '/api/v1/tasks/{id}',
        summary: 'Deletar uma tarefa',
        description: 'Deleta uma tarefa pelo ID',
        tags: ['Tasks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da tarefa',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tarefa deletada com sucesso'),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 404, description: 'Tarefa não encontrada'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]


    #[OA\Schema(
        schema: 'Task',
        type: 'object',
        properties: [
            new OA\Property(property: 'id', type: 'integer', description: 'ID da tarefa'),
            new OA\Property(property: 'title', type: 'string', description: 'Título da tarefa'),
            new OA\Property(property: 'description', type: 'string', description: 'Descrição da tarefa'),
            new OA\Property(property: 'status', type: 'string', description: 'Status da tarefa (pending/completed)'),
            new OA\Property(property: 'datetime', type: 'string', format: 'date-time', description: 'Data e hora da tarefa'),
            new OA\Property(property: 'order', type: 'integer', description: 'Ordem da tarefa')
        ]
    )]    
    
    public function delete($id = null)
    {
        //
        try {
            // Check if the ID was provided
            if (is_null($id)) {
                return $this->failValidationErrors('Appointment ID is required.');
            }

            $deleteTasks = new DeleteTasks();

            $deleteTasks->del((int) $id);

            // Return the success response with status 200 OK
            return $this->respondDeleted(['message' => 'Tasks deleted successfully.']);
        } catch (\InvalidArgumentException $e) {
            // Respond with validation error (422 Unprocessable Entity)
            return $this->failValidationErrors($e->getMessage());
        } catch (\RuntimeException $e) {
            // Respond with execution error (404 Not Found or 403 Forbidden)
            return $this->failNotFound($e->getMessage());
        } catch (\Exception $e) {
            // Respond with internal error (500 Internal Server Error)
            return $this->failServerError('Internal Server Error: ' . $e->getMessage());
        }
    }

    
}
