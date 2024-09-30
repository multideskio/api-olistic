<?php

namespace App\Controllers\Api\V1;

use OpenApi\Attributes as OA;
use App\Models\Supports\V1\CreateSupports;
use CodeIgniter\HTTP\ResponseInterface;

class SupportController extends BaseController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
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


    #[OA\Post(
        path: '/api/v1/support',
        summary: 'Criar novo chamado de suporte',
        description: 'Este endpoint cria um novo chamado de suporte para um cliente. O ID do cliente é identificado internamente.',
        tags: ['Suporte'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        description: 'Nome do cliente'
                    ),
                    new OA\Property(
                        property: 'subject',
                        type: 'string',
                        description: 'Assunto do chamado de suporte'
                    ),
                    new OA\Property(
                        property: 'type',
                        type: 'string',
                        description: 'Tipo do suporte (e.g., técnico, financeiro)'
                    ),
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        description: 'Mensagem detalhada do suporte'
                    ),
                    new OA\Property(
                        property: 'channel',
                        type: 'string',
                        description: 'Canal de origem do suporte (e.g., form, webhook)',
                        default: 'form'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Chamado de suporte criado com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', description: 'ID do chamado criado'),
                        new OA\Property(property: 'protocol', type: 'string', description: 'Protocolo do chamado')
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Conflito: Chamado de suporte já existe'
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação'
            ),
            new OA\Response(
                response: 500,
                description: 'Erro interno do servidor'
            )
        ]
    )]

    public function create()
    {
        $input = $this->request->getJSON(true);

        // Verifica se a entrada é válida antes de prosseguir
        if (!$input || empty($input['name']) || empty($input['subject']) || empty($input['type']) || empty($input['message'])) {
            return $this->fail('Os campos name, subject, type e message são obrigatórios.', 422);
        }

        try {
            $modelSupport = new CreateSupports();
            $response = $modelSupport->createSupportSystem($input);
            return $this->respondCreated($response); // Usando 'respondCreated' para retornar 201
        } catch (\InvalidArgumentException $e) {
            // Captura exceções específicas de validação
            return $this->fail($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            // Captura exceções de runtime específicas
            return $this->fail($e->getMessage(), 500);
        } catch (\Exception $e) {
            // Captura exceções gerais não tratadas
            return $this->fail($e->getMessage(), 500);
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

    public function webhookCrispFirstChat()
    {
        $input = $this->request->getVar(TRUE);
        return $this->respond($input);
    }
}
