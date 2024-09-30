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
        path: '/api/v1/dashboard/appointments',
        summary: 'Relatório mensal de compromissos',
        description: 'Este endpoint retorna um relatório mensal detalhado dos compromissos, cancelamentos, anamneses e retornos do usuário autenticado.',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]], // Necessita de autenticação via Bearer Token
        responses: [
            new OA\Response(
                response: 200,
                description: 'Relatório mensal gerado com sucesso',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'date',
                                type: 'string',
                                description: 'Mês no formato YYYY-MM'
                            ),
                            new OA\Property(
                                property: 'appointments',
                                type: 'integer',
                                description: 'Número de compromissos no mês'
                            ),
                            new OA\Property(
                                property: 'cancelled',
                                type: 'integer',
                                description: 'Número de compromissos cancelados no mês'
                            ),
                            new OA\Property(
                                property: 'anamneses',
                                type: 'integer',
                                description: 'Número de anamneses no mês'
                            ),
                            new OA\Property(
                                property: 'return',
                                type: 'integer',
                                description: 'Número de retornos no mês'
                            ),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Erro de validação'
            ),
            new OA\Response(
                response: 401,
                description: 'Token inválido ou ausente'
            ),
            new OA\Response(
                response: 500,
                description: 'Erro interno do servidor'
            )
        ]
    )]

    public function reportJson()
    {
        $reportMes = new ReportsLibraries();
        return $this->respond($reportMes->mensal());
    }
}
