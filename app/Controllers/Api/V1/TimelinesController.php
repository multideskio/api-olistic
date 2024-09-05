<?php

namespace App\Controllers\Api\V1;

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
}
