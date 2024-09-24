<?php

namespace App\Controllers\Api\V1;

use App\Models\Tasks\V1\CreateTasks;
use App\Models\Tasks\V1\DeleteTasks;
use App\Models\Tasks\V1\GetTasks;
use App\Models\Tasks\V1\SearchTasks;
use App\Models\Tasks\V1\UpdateTasks;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class TasksController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */


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
    public function show($id = null)
    {
        //
        try{
            $getTask = new GetTasks();
            $data = $getTask->getId($id);
            return $this->respond($data);
        }catch(\RuntimeException $e){
            return $this->failNotFound($e->getMessage());
        }catch(\Exception $e){
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
