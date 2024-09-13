<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentsModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_user', 'id_customer', 'date', 'status'];

    protected bool $allowEmptyInserts = true;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];



    public function listAppointments(array $params): array
    {
        //define array
        $response = [];

        //busca dados do usuário logado
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        //não está logado gera erro.
        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        //id do usuário logado
        $currentUserId = $currentUser['id'];

        // Parâmetros de entrada
        $searchTerm = $params['s'] ?? false;
        $currentPage = (isset($params['page']) && intval($params['page']) > 0) ? intval($params['page']) : 1;
        $sortBy = $params['sort_by'] ?? 'id';
        $sortOrder = strtoupper($params['order'] ?? 'ASC');
        $itemsPerPage = $this->validateItemsPerPage($params['limite'] ?? null);



        if ($currentUser['role'] != 'SUPERADMIN') {
            $this->where('appointments.id_user', $currentUserId);
        }

        $this->join("customers", "appointments.id_customer = customers.id", "left")
            //->groupBy('appointments.id')
            ->orderBy('appointments.' . $sortBy, $sortOrder);

        // Aplicar filtro de busca se o termo for fornecido
        if ($searchTerm) {
            $this->groupStart()
                ->like('customers.name', $searchTerm)
                //->orLike('appointments.id', $searchTerm)
                ->orLike('customers.email', $searchTerm)
                ->orLike('customers.phone', $searchTerm)
                ->groupEnd();
        }

        // Contar resultados totais para a paginação
        $totalItems = $this->countAllResults(false); // 'false' mantém a query para a paginação

        // Paginação dos resultados
        $data = $this->paginate($itemsPerPage, '', $currentPage);

        // Calcular links de navegação para paginação
        $totalPages = ceil($totalItems / $itemsPerPage);
        $prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

        // Montar o array de dados a ser retornado
        $response = [
            'rows'  => $data, // Resultados paginados com contagem de anamneses
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'items_per_page' => $itemsPerPage,
                'prev_page' => $prevPage,
                'next_page' => $nextPage,
            ],
            //'num'   => $resultMessage
        ];
        return $response;
    }

    private function validateItemsPerPage($value)
    {
        // Verifica se o valor está definido, se é numérico, e tenta converter para inteiro
        $itemsPerPage = (isset($value) && is_numeric($value)) ? intval($value) : 15;

        // Se a conversão falhar, retorna 15
        if (!$itemsPerPage) {
            $itemsPerPage = 15;
        }

        // Verifica o limite máximo de 200
        if ($itemsPerPage > 200) {
            $itemsPerPage = 200;
        }

        return $itemsPerPage;
    }
}
