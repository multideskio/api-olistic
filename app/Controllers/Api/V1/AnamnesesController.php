<?php

namespace App\Controllers\Api\V1;

use App\Models\AnamnesesModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;

class AnamnesesController extends BaseController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $modelAnamnese;

    public function __construct()
    {
        $this->modelAnamnese = new AnamnesesModel();
    }

    #[OA\Get(
        path: '/api/v1/anamneses',
        summary: 'Listar todas as Anamneses',
        description: 'Retorna uma lista de clientes com paginação',
        tags: ['Anamneses'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de anamneses',
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


    #[OA\Post(
        path: '/api/v1/anamneses',
        summary: 'Criar nova Anamnese',
        description: 'Cria uma nova anamnese para o cliente com base nos dados fornecidos.',
        tags: ['Anamneses'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Dados necessários para criar uma anamnese.',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'idCustomer', type: 'integer', description: 'ID do cliente'),
                    new OA\Property(property: 'mentalDesequilibrio', type: 'string', description: 'Desequilíbrio mental', enum: ['sim', 'não']),
                    new OA\Property(property: 'mentalPercentual', type: 'integer', description: 'Percentual de desequilíbrio mental', minimum: 0, maximum: 100),
                    new OA\Property(property: 'emocionalDesequilibrio', type: 'string', description: 'Desequilíbrio emocional', enum: ['sim', 'não']),
                    new OA\Property(property: 'emocionalPercentual', type: 'integer', description: 'Percentual de desequilíbrio emocional', minimum: 0, maximum: 100),
                    new OA\Property(property: 'espiritualDesequilibrio', type: 'string', description: 'Desequilíbrio espiritual', enum: ['sim', 'não']),
                    new OA\Property(property: 'espiritualPercentual', type: 'integer', description: 'Percentual de desequilíbrio espiritual', minimum: 0, maximum: 100),
                    new OA\Property(property: 'fisicoDesequilibrio', type: 'string', description: 'Desequilíbrio físico', enum: ['sim', 'não']),
                    new OA\Property(property: 'fisicoPercentual', type: 'integer', description: 'Percentual de desequilíbrio físico', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraCoronarioDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra coronário', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraCoronarioPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra coronário', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraCoronarioAtividade', type: 'string', description: 'Atividade do chakra coronário', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraCoronarioOrgao', type: 'string', description: 'Órgão afetado pelo chakra coronário', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraFrontalDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra frontal', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraFrontalPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra frontal', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraFrontalAtividade', type: 'string', description: 'Atividade do chakra frontal', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraFrontalOrgao', type: 'string', description: 'Órgão afetado pelo chakra frontal', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraLaringeoDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra laríngeo', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraLaringeoPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra laríngeo', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraLaringeoAtividade', type: 'string', description: 'Atividade do chakra laríngeo', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraLaringeoOrgao', type: 'string', description: 'Órgão afetado pelo chakra laríngeo', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraCardiacoDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra cardíaco', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraCardiacoPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra cardíaco', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraCardiacoAtividade', type: 'string', description: 'Atividade do chakra cardíaco', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraCardiacoOrgao', type: 'string', description: 'Órgão afetado pelo chakra cardíaco', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraPlexoSolarDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra plexo solar', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraPlexoSolarPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra plexo solar', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraPlexoSolarAtividade', type: 'string', description: 'Atividade do chakra plexo solar', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraPlexoSolarOrgao', type: 'string', description: 'Órgão afetado pelo chakra plexo solar', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraSacroDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra sacro', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraSacroPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra sacro', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraSacroAtividade', type: 'string', description: 'Atividade do chakra sacro', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraSacroOrgao', type: 'string', description: 'Órgão afetado pelo chakra sacro', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraBasicoDesequilibrio', type: 'string', description: 'Desequilíbrio do chakra básico', enum: ['sim', 'não']),
                    new OA\Property(property: 'chakraBasicoPercentual', type: 'integer', description: 'Percentual de desequilíbrio do chakra básico', minimum: 0, maximum: 100),
                    new OA\Property(property: 'chakraBasicoAtividade', type: 'string', description: 'Atividade do chakra básico', enum: ['HIPO', 'HIPER']),
                    new OA\Property(property: 'chakraBasicoOrgao', type: 'string', description: 'Órgão afetado pelo chakra básico', enum: ['sim', 'não']),
                    new OA\Property(property: 'tamanhoAura', type: 'integer', description: 'Tamanho da aura', minimum: 0),
                    new OA\Property(property: 'tamanhoAbertura', type: 'integer', description: 'Tamanho da abertura', minimum: 0),
                    new OA\Property(
                        property: 'corFalta',
                        type: 'array',
                        description: 'Cores em falta',
                        items: new OA\Items(type: 'string')
                    ),
                    new OA\Property(
                        property: 'corExcesso',
                        type: 'array',
                        description: 'Cores em excesso',
                        items: new OA\Items(type: 'string')
                    ),
                    new OA\Property(property: 'energia', type: 'integer', description: 'Nível de energia', minimum: 0),
                    new OA\Property(property: 'areasFamiliar', type: 'string', description: 'Área familiar', enum: ['pessimo', 'muito mal', 'mal', 'regular', 'bom', 'muito bom', 'excelente']),
                    new OA\Property(property: 'areasAfetivo', type: 'string', description: 'Área afetiva', enum: ['pessimo', 'muito mal', 'mal', 'regular', 'bom', 'muito bom', 'excelente']),
                    new OA\Property(property: 'areasProfissional', type: 'string', description: 'Área profissional', enum: ['pessimo', 'muito mal', 'mal', 'regular', 'bom', 'muito bom', 'excelente']),
                    new OA\Property(property: 'areasFinanceiro', type: 'string', description: 'Área financeira', enum: ['pessimo', 'muito mal', 'mal', 'regular', 'bom', 'muito bom', 'excelente']),
                    new OA\Property(property: 'areasMissao', type: 'string', description: 'Área de missão', enum: ['pessimo', 'muito mal', 'mal', 'regular', 'bom', 'muito bom', 'excelente'])
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Anamnese criada com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', description: 'Mensagem de sucesso')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Requisição inválida'),
            new OA\Response(response: 422, description: 'Erros de validação'),
            new OA\Response(response: 500, description: 'Erro interno do servidor')
        ]
    )]

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        try {
            // Obtenha os dados da solicitação JSON como um array associativo
            $input = $this->request->getJSON(true); // true garante que o JSON é convertido para array

            // Defina as regras de validação
            $rules = [
                'idCustomer' => 'required|integer',
                'mentalDesequilibrio' => 'required|in_list[sim,não]',
                'mentalPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'emocionalDesequilibrio' => 'required|in_list[sim,não]',
                'emocionalPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'espiritualDesequilibrio' => 'required|in_list[sim,não]',
                'espiritualPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'fisicoDesequilibrio' => 'required|in_list[sim,não]',
                'fisicoPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraCoronarioDesequilibrio' => 'required|in_list[sim,não]',
                'chakraCoronarioPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraCoronarioAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraCoronarioOrgao' => 'required|in_list[sim,não]',
                'chakraFrontalDesequilibrio' => 'required|in_list[sim,não]',
                'chakraFrontalPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraFrontalAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraFrontalOrgao' => 'required|in_list[sim,não]',
                'chakraLaringeoDesequilibrio' => 'required|in_list[sim,não]',
                'chakraLaringeoPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraLaringeoAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraLaringeoOrgao' => 'required|in_list[sim,não]',
                'chakraCardiacoDesequilibrio' => 'required|in_list[sim,não]',
                'chakraCardiacoPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraCardiacoAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraCardiacoOrgao' => 'required|in_list[sim,não]',
                'chakraPlexoSolarDesequilibrio' => 'required|in_list[sim,não]',
                'chakraPlexoSolarPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraPlexoSolarAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraPlexoSolarOrgao' => 'required|in_list[sim,não]',
                'chakraSacroDesequilibrio' => 'required|in_list[sim,não]',
                'chakraSacroPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraSacroAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraSacroOrgao' => 'required|in_list[sim,não]',
                'chakraBasicoDesequilibrio' => 'required|in_list[sim,não]',
                'chakraBasicoPercentual' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
                'chakraBasicoAtividade' => 'required|in_list[HIPO, HIPER]',
                'chakraBasicoOrgao' => 'required|in_list[sim,não]',
                'tamanhoAura' => 'required|integer|greater_than_equal_to[0]',
                'tamanhoAbertura' => 'required|integer|greater_than_equal_to[0]',
                'corFalta' => 'required',
                'corExcesso' => 'required',
                'energia' => 'required|integer|greater_than_equal_to[0]',
                'areasFamiliar' => 'required|in_list[pessimo,muito mal,mal,regular,bom,muito bom,excelente]',
                'areasAfetivo' => 'required|in_list[pessimo,muito mal,mal,regular,bom,muito bom,excelente]',
                'areasProfissional' => 'required|in_list[pessimo,muito mal,mal,regular,bom,muito bom,excelente]',
                'areasFinanceiro' => 'required|in_list[pessimo,muito mal,mal,regular,bom,muito bom,excelente]',
                'areasMissao' => 'required|in_list[pessimo,muito mal,mal,regular,bom,muito bom,excelente]',
            ];

            // Defina as mensagens de erro personalizadas
            $messages = [
                'required' => 'O campo {field} é obrigatório.',
                'in_list' => 'O valor para o campo {field} deve ser um dos seguintes: {param}.',
                'integer' => 'O campo {field} deve ser um número inteiro.',
                'greater_than_equal_to' => 'O campo {field} deve ser maior ou igual a {param}.',
                'less_than_equal_to' => 'O campo {field} deve ser menor ou igual a {param}.',
                'checkArray' => 'O campo {field} deve ser um array.', // Mensagem personalizada para array
            ];

            // Configura a validação
            $validation = \Config\Services::validation();

            // Verifica se a validação falha
            if (!$validation->setRules($rules, $messages)->run($input)) {
                // Obtenha os erros de validação
                $errors = $validation->getErrors();
                // Retorna os erros como uma resposta de erro
                return $this->failValidationErrors([$errors]);
            }



            $data = $this->modelAnamnese->createAnamnese($input);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }


        // Continuação do processamento dos dados, como inserir no banco de dados, etc.
        // ...
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
