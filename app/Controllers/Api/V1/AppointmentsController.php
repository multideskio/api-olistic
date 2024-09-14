<?php

namespace App\Controllers\Api\V1;

use App\Models\Appointments\V1\CreateAppointments;
use App\Models\Appointments\V1\listAppointments;
use App\Models\AppointmentsModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use OpenApi\Attributes as OA;


class AppointmentsController extends BaseController
{
    use ResponseTrait;
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $modelAppointments;

    public function __construct()
    {
        $this->modelAppointments = new AppointmentsModel();
    }


    /*#[OA\Get(
        path: "/api/v1/appointments",
        summary: "Lista de agendamentos - última atualização 13/09/2024",
        description: "Retorna todos os agendamentos do usuário logado",
        tags: ["Agendamentos"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: 'sort_by',
                in: 'query',
                required: false,
                description: 'Campo para ordenação dos resultados',
                schema: new OA\Schema(type: 'string', enum: ['id', 'date', 'status'], default: 'id')
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
                description: 'Termo de busca para filtrar os agendamentos',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'limite',
                in: 'query',
                required: false,
                description: 'Número de itens por página',
                schema: new OA\Schema(type: 'integer', default: 15, maximum: 100)
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Número da página para paginação',
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'start',
                in: 'query',
                required: false,
                description: 'Data inicial para filtro de período (inclusive)',
                schema: new OA\Schema(type: 'string', format: 'date-time', example: '2024-09-01 00:00:00')
            ),
            new OA\Parameter(
                name: 'end',
                in: 'query',
                required: false,
                description: 'Data final para filtro de período (inclusive)',
                schema: new OA\Schema(type: 'string', format: 'date-time', example: '2024-09-01 23:59:59')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Filtro de status dos agendamentos',
                schema: new OA\Schema(type: 'string', enum: ['pending', 'completed', 'cancelled'], example: 'pending')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de agendamentos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "rows", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "string", example: "1"),
                                new OA\Property(property: "id_user", type: "string", example: "1"),
                                new OA\Property(property: "id_customer", type: "string", example: "1"),
                                new OA\Property(property: "date", type: "string", format: "date-time", example: "2024-09-13 19:57:06"),
                                new OA\Property(
                                    property: "status",
                                    type: "string",
                                    enum: ['pending', 'completed', 'cancelled'],
                                    example: "pending"
                                ),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-09-13 12:28:01"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-09-13 12:28:01"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", example: null, nullable: true),
                                new OA\Property(property: "idUser", type: "string", example: "1"),
                                new OA\Property(property: "name", type: "string", example: "ADMIN"),
                                new OA\Property(property: "photo", type: "string", example: null, nullable: true),
                                new OA\Property(property: "email", type: "string", example: "adm@conect.app"),
                                new OA\Property(property: "phone", type: "string", example: "+55 (62) 9 8115-4120"),
                                new OA\Property(property: "doc", type: "string", example: null, nullable: true),
                                new OA\Property(
                                    property: "generous",
                                    type: "string",
                                    enum: ["male", "female", "unspecified", "non-binary", "gender fluid", "agender", "other"],
                                    example: "unspecified"
                                ),
                                new OA\Property(property: "birthDate", type: "string", format: "date", example: null, nullable: true)
                            ]
                        )),
                        new OA\Property(
                            property: "params",
                            type: "object",
                            properties: [
                                new OA\Property(property: "s", type: "string", example: ""),
                                new OA\Property(property: "order", type: "string", example: ""),
                                new OA\Property(property: "limite", type: "string", example: ""),
                                new OA\Property(property: "page", type: "string", example: ""),
                                new OA\Property(property: "start", type: "string", format: "date", example: "2024-09-10"),
                                new OA\Property(property: "end", type: "string", format: "date", example: "2024-09-14")
                            ]
                        ),
                        new OA\Property(
                            property: "dateRange",
                            type: "object",
                            properties: [
                                new OA\Property(property: "start", type: "string", format: "date-time", example: "2024-09-10 00:00:00"),
                                new OA\Property(property: "end", type: "string", format: "date-time", example: "2024-09-14 00:00:00")
                            ]
                        ),
                        new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "total_pages", type: "integer", example: 1),
                                new OA\Property(property: "total_items", type: "integer", example: 1),
                                new OA\Property(property: "items_per_page", type: "integer", example: 15),
                                new OA\Property(property: "prev_page", type: "integer", example: null, nullable: true),
                                new OA\Property(property: "next_page", type: "integer", example: null, nullable: true)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token inválido ou ausente'),
            new OA\Response(response: 403, description: 'Usuário sem permissão'),
        ]
    )]*/


    public function index()
    {
        //
        try {
            $input = $this->request->getVar();
            $listAppointments = new listAppointments();
            $data = $listAppointments->listAppointments($input);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
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
        try {
            $createAppointments = new CreateAppointments();
            $input = $this->request->getJSON(true);
            $create = $createAppointments->create($input);
            return $this->respondCreated($create); // 201 Created
        } catch (\RuntimeException $e) {
            // Erros específicos capturados na lógica de negócios
            return $this->failValidationErrors($e->getMessage()); // 422 Unprocessable Entity
        } catch (\DomainException $e) {
            // Erro de conflito, por exemplo, agendamento duplicado
            return $this->failResourceExists($e->getMessage()); // 409 Conflict
        } catch (\Exception $e) {
            // Erros genéricos ou inesperados
            log_message('error', $e->getMessage()); // Log para monitoramento
            return $this->failServerError('Erro inesperado, por favor tente novamente mais tarde.'); // 500 Internal Server Error
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
}
