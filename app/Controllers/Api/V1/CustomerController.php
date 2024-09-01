<?php

namespace App\Controllers\Api\V1;

use App\Models\CustomersModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class CustomerController extends ResourceController
{
    use ResponseTrait;
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $modelCustomer;

    public function __construct()
    {
        $this->modelCustomer = new CustomersModel();
    }

    public function index()
    {
        //

        try {
            $input = $this->request->getVar();
            $data = $this->modelCustomer->search($input);
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
        try {
            // Verifica se o ID foi fornecido
            if (is_null($id)) {
                return $this->failValidationErrors('O ID do cliente é obrigatório.');
            }

            // Chama o método showCustomer do model para obter os dados do customer com anamneses
            $customer = $this->modelCustomer->showCustomer((int) $id);

            // Retorna a resposta de sucesso com os dados do customer
            return $this->respond($customer);
        } catch (\InvalidArgumentException $e) {
            // Responde com erro de validação (422 Unprocessable Entity)
            return $this->failValidationErrors($e->getMessage());
        } catch (\RuntimeException $e) {
            // Responde com erro de execução (404 Not Found ou 403 Forbidden)
            return $this->failNotFound($e->getMessage());
        } catch (\Exception $e) {
            // Responde com erro interno (500 Internal Server Error)
            return $this->failServerError('Erro interno do servidor: ' . $e->getMessage());
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
        return $this->respond(['new' => '']);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        try {
            // Obtém os dados de entrada usando getVar para capturar o JSON do corpo da requisição
            $input = $this->request->getVar();

            // Validação básica de campos obrigatórios
            if (empty($input->name) || empty($input->email) || empty($input->phone)) {
                return $this->failValidationErrors('Os campos nome, email e telefone são obrigatórios.');
            }

            // Converte o objeto de entrada para array para passar para o model
            $inputArray = [
                'name' => $input->name,
                'email' => $input->email,
                'phone' => $input->phone,
                'photo' => $input->photo ?? null,
                'date' => $input->birthDate ?? null,  // Adicionando campos opcionais
                'doc' => $input->doc ?? null,
                'genero' => $input->generous ?? null
            ];

            // Chama o método create do model com os dados de entrada
            $create = $this->modelCustomer->createCustomer($inputArray);

            // Retorna a resposta de sucesso com o status 201 Created
            return $this->respondCreated($create);
        } catch (\InvalidArgumentException $e) {
            // Responde com erro de validação (422 Unprocessable Entity)
            return $this->failValidationErrors($e->getMessage());
        } catch (\RuntimeException $e) {
            // Responde com erro de execução (400 Bad Request)
            return $this->fail($e->getMessage(), 400);
        } catch (\Exception $e) {
            // Responde com erro interno (500 Internal Server Error)
            return $this->failServerError('Erro interno do servidor: ' . $e->getMessage());
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
        return $this->respond(['edit' => $id]);
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
        try {
            // Verifica se o ID foi fornecido
            if (is_null($id)) {
                return $this->failValidationErrors('O ID do cliente é obrigatório.');
            }

            // Obtém os dados da requisição (assume-se que os dados estão em JSON)
            $input = $this->request->getVar();

            // Converte o input para um array, se necessário
            if (is_object($input)) {
                $input = (array) $input;
            }

            // Chama o método de atualização do model
            $update = $this->modelCustomer->updateCustomer($input, $id);

            // Retorna a resposta de sucesso com o status 200 OK
            return $this->respond($update);
        } catch (\InvalidArgumentException $e) {
            // Responde com erro de validação (422 Unprocessable Entity)
            return $this->failValidationErrors($e->getMessage());
        } catch (\RuntimeException $e) {
            // Responde com erro de execução (400 Bad Request)
            return $this->fail($e->getMessage(), 400);
        } catch (\Exception $e) {
            // Responde com erro interno (500 Internal Server Error)
            return $this->failServerError('Erro interno do servidor: ' . $e->getMessage());
        }
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
        try {
            // Verifica se o ID foi fornecido
            if (is_null($id)) {
                return $this->failValidationErrors('O ID do cliente é obrigatório.');
            }

            // Chama o método deleteCustomer do model
            $this->modelCustomer->deleteCustomer((int) $id);

            // Retorna a resposta de sucesso com o status 200 OK
            return $this->respondDeleted(['message' => 'Customer deletado com sucesso.']);
        } catch (\InvalidArgumentException $e) {
            // Responde com erro de validação (422 Unprocessable Entity)
            return $this->failValidationErrors($e->getMessage());
        } catch (\RuntimeException $e) {
            // Responde com erro de execução (404 Not Found ou 403 Forbidden)
            return $this->failNotFound($e->getMessage());
        } catch (\Exception $e) {
            // Responde com erro interno (500 Internal Server Error)
            return $this->failServerError('Erro interno do servidor: ' . $e->getMessage());
        }
    }
}
