<?php

namespace App\Controllers\Api\V1;

use App\Libraries\WebhookLibraries;
use CodeIgniter\HTTP\ResponseInterface;
use OpenApi\Attributes as OA;

class WebhookController extends BaseController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $webhookLibraries;

    public function __construct()
    {
        $this->webhookLibraries = new WebhookLibraries();
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




    #[OA\Post(
        path: '/api/v1/webhook/greem',
        summary: 'Cria um usuário com base nas informações do webhook recebido da GREEM',
        description: "Processa o webhook da GREEM para criar ou atualizar um usuário com base nas informações da transação recebida.\n</br>O produto deverá ter sido cadastrado previamente informando o ID do produto da GREEM",
        tags: ['Webhooks'],
        
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Dados do webhook da GREEM.',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: 'currentStatus',
                        type: 'string',
                        description: 'Status atual da transação',
                        example: 'paid'
                    ),
                    new OA\Property(
                        property: 'client',
                        type: 'object',
                        description: 'Informações do cliente',
                        properties: [
                            new OA\Property(property: 'email', type: 'string', description: 'Email do cliente'),
                            new OA\Property(property: 'name', type: 'string', description: 'Nome do cliente'),
                            new OA\Property(property: 'cellphone', type: 'string', description: 'Telefone do cliente')
                        ]
                    ),
                    new OA\Property(
                        property: 'product',
                        type: 'object',
                        description: 'Informações do produto',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', description: 'ID do produto'),
                            new OA\Property(property: 'name', type: 'integer', description: 'Nome do produto')
                        ]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Transação processada com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', description: 'Mensagem de sucesso')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Requisição inválida'),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 403, description: 'Sem permissão para executar'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]
    public function greem()
    {
        try {
            
            // Ações da class WebhookLibraries
            $webhook = $this->webhookLibraries->processTransaction($this->request);
            
            return $this->respond($webhook, $webhook['code']);


        } catch (\Exception $e) {
            return $this->failForbidden($e->getMessage());
        }
    }
}
