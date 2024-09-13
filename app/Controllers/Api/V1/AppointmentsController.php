<?php

namespace App\Controllers\Api\V1;

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


    #[OA\Get(
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
                                new OA\Property(property: "id", type: "integer", example: 101),
                                new OA\Property(property: "id_user", type: "integer", example: 5),
                                new OA\Property(property: "id_customer", type: "integer", example: 8),
                                new OA\Property(property: "date", type: "string", format: "date-time", example: "2024-09-13T15:30:00Z"),
                                new OA\Property(property: "status", type: "string", example: "confirmado"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-08-20T09:45:00Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-09-10T10:20:00Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", example: null, nullable: true),
                                new OA\Property(property: "idUser", type: "integer", example: 5),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "photo", type: "string", example: "https://example.com/photos/johndoe.jpg", nullable: true),
                                new OA\Property(property: "email", type: "string", example: "johndoe@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "+55 (21) 9 9988-7766"),
                                new OA\Property(property: "doc", type: "string", example: "123.456.789-00", nullable: true),
                                new OA\Property(property: "generous", type: "string", example: "masculino", nullable: true),
                                new OA\Property(property: "birthDate", type: "string", format: "date", example: "1985-05-15", nullable: true)
                            ]

                        )),
                        new OA\Property(
                            property: "pagination",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "total_pages", type: "integer", example: 1),
                                new OA\Property(property: "total_items", type: "integer", example: 1),
                                new OA\Property(property: "items_per_page", type: "integer", example: 10),
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
    )]

    public function index()
    {
        //
        try {


            $input = $this->request->getVar();
            $data = $this->modelAppointments->listAppointments($input);
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
