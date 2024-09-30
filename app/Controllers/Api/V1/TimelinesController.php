<?php

namespace App\Controllers\Api\V1;

use OpenApi\Attributes as OA;
use App\Libraries\ReportsLibraries;
use App\Models\TimeLinesModel;
use CodeIgniter\HTTP\ResponseInterface;

class TimelinesController extends BaseController
{
    protected $modelTimeLine;
    public function __construct()
    {
        $this->modelTimeLine = new TimeLinesModel();
    }
    public function index()
    {
        //
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
        /*try {
            // Verifica se o ID foi fornecido
            if (is_null($id)) {
                return $this->failValidationErrors('O ID do cliente é obrigatório.');
            }

            // Chama o método showCustomer do model para obter os dados do customer com anamneses
            $data = $this->modelTimeLine->showTimeLineCustomer((int) $id);

            // Retorna a resposta de sucesso com os dados do customer
            return $this->respond($data);
        } catch (\InvalidArgumentException $e) {
            // Responde com erro de validação (422 Unprocessable Entity)
            return $this->failValidationErrors($e->getMessage());
        } catch (\RuntimeException $e) {
            // Responde com erro de execução (404 Not Found ou 403 Forbidden)
            return $this->failNotFound($e->getMessage());
        } catch (\Exception $e) {
            // Responde com erro interno (500 Internal Server Error)
            return $this->failServerError('Erro interno do servidor: ' . $e->getMessage());
        }*/
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


    #[OA\Get(
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

    public function reportJson(){
        $reportMes = new ReportsLibraries();
        return $this->respond($reportMes->mensal());
    }
}
