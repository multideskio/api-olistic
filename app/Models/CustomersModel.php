<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomersModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['idUser', 'name', 'photo', 'phone', 'email', 'phone', 'birthDate', 'doc', 'generous'];

    protected bool $allowEmptyInserts = false;
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

    public function search(array $params): array
    {
        $response = [];

        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        $currentUserId = $currentUser['id'];

        // Parâmetros de entrada
        $searchTerm = $params['s'] ?? false;
        $currentPage = (isset($params['page']) && intval($params['page']) > 0) ? intval($params['page']) : 1;
        $sortBy = $params['sort_by'] ?? 'id';
        $sortOrder = strtoupper($params['order'] ?? 'ASC');
        $itemsPerPage = $this->validateItemsPerPage($params['limite'] ?? null);

        // Construir a query principal
        $this->select('customers.*, COUNT(anamneses.id) as anamneses_count')
            ->join('anamneses', 'anamneses.id_customer = customers.id', 'left')
            ->where('customers.idUser', $currentUserId)
            ->groupBy('customers.id')
            ->orderBy('customers.' . $sortBy, $sortOrder);

        // Aplicar filtro de busca se o termo for fornecido
        if ($searchTerm) {
            $this->groupStart()
                ->like('customers.name', $searchTerm)
                ->orLike('customers.id', $searchTerm)
                ->orLike('customers.email', $searchTerm)
                ->orLike('customers.birthDate', $searchTerm)
                ->groupEnd();
        }

        // Contar resultados totais para a paginação
        $totalItems = $this->countAllResults(false); // 'false' mantém a query para a paginação

        // Paginação dos resultados
        $customers = $this->paginate($itemsPerPage, '', $currentPage);

        // Preparar mensagem de contagem de resultados
        $itemsOnPage = count($customers);
        if ($searchTerm) {
            $resultMessage = $itemsOnPage === 1 ? "1 resultado encontrado." : "{$itemsOnPage} resultados encontrados.";
        } else {
            $startItem = ($currentPage - 1) * $itemsPerPage + 1;
            $endItem = min($currentPage * $itemsPerPage, $totalItems);
            $resultMessage = "Exibindo resultados {$startItem} a {$endItem} de {$totalItems}.";
        }

        // Calcular links de navegação para paginação
        $totalPages = ceil($totalItems / $itemsPerPage);
        $prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

        // Montar o array de dados a ser retornado
        $response = [
            'rows'  => $customers, // Resultados paginados com contagem de anamneses
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


    public function createCustomer(array $params): array
    {
        // Obter o usuário atual usando o UsersModel ou passando como parâmetro
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        $currentUserId = $currentUser['id'];

        // Validação básica dos parâmetros
        if (empty($params['email']) || empty($params['name']) || empty($params['phone'])) {
            throw new \InvalidArgumentException('Campos obrigatórios não preenchidos.');
        }

        // Verifica se endereço de e-mail está no banco de dados relacionado ao usuário atual
        $row = $this->where([
            'email'  => $params['email'],
            'idUser' => $currentUserId,
        ])->countAllResults();

        if ($row > 0) {
            throw new \RuntimeException('Esse e-mail já está cadastrado. Verifique na sua tabela de clientes.');
        }

        // Dados para cadastro
        $data = [
            'idUser' => $currentUserId,
            'name' => $params['name'],
            'email' => $params['email'],
            'phone' => $params['phone'],
            'photo' => $params['photo'] ?? null,
            'birthDate' => $params['date'] ?? null, // Adicionando fallback para campos opcionais
            'doc' => $params['doc'] ?? null,
            'generous' => $params['genero'] ?? null
        ];

        // Inserção no banco de dados
        if (!$this->insert($data)) {
            // Captura erros da instância correta do Model
            $errors = $this->errors();
            throw new \RuntimeException('Erro ao cadastrar o cliente: ' . implode(', ', $errors));
        }

        $id = $this->getInsertID(); // Usar getInsertID para obter o ID inserido

        return ['id' => $id, 'message' => 'Customer created'];
    }

    public function updateCustomer(array $params, $id): array
    {
        // Obter o usuário atual
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        $currentUserId = $currentUser['id'];

        // Verifica se o customer pertence ao usuário atual
        $customer = $this->where('id', $id)->where('idUser', $currentUserId)->first();

        if (!$customer) {
            throw new \RuntimeException('Customer não encontrado ou você não tem permissão para editá-lo.');
        }

        // Validação básica dos parâmetros
        if (empty($params['email']) || empty($params['name']) || empty($params['phone'])) {
            throw new \InvalidArgumentException('Campos obrigatórios não preenchidos.');
        }

        // Verifica se o endereço de e-mail já existe para outro customer do mesmo usuário
        $row = $this->where('email', $params['email'])
            ->where('idUser', $currentUserId)
            ->where('id !=', $id) // Garante que não estamos comparando com o mesmo registro
            ->countAllResults();

        if ($row > 0) {
            throw new \RuntimeException('Esse e-mail já está cadastrado. Verifique na sua tabela de clientes.');
        }

        // Dados para atualização
        $data = [
            'name' => $params['name'],
            'email' => $params['email'],
            'phone' => $params['phone'],
            'photo' => $params['photo'] ?? null,
            'birthDate' => $params['birthDate'] ?? null, // Fallback para campos opcionais
            'doc' => $params['doc'] ?? null,
            'generous' => $params['generous'] ?? null
        ];

        // Atualiza o registro no banco de dados
        if (!$this->update($id, $data)) {
            // Captura erros da instância correta do Model
            $errors = $this->errors();
            throw new \RuntimeException('Erro ao atualizar o cliente: ' . implode(', ', $errors));
        }

        return ['id' => $id, 'message' => 'Customer updated'];
    }

    public function showCustomer(int $id): array
    {
        // Obter o usuário atual
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        // Verificar se o usuário está autenticado
        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        $currentUserId = $currentUser['id'];

        // Realizar a busca do customer com JOIN para incluir anamneses
        $this->select('customers.*, COUNT(anamneses.id) as anamneses_count')
            ->join('anamneses', 'anamneses.id_customer = customers.id', 'left')
            ->where('customers.id', $id)
            ->where('customers.idUser', $currentUserId)
            ->groupBy('customers.id');

        $customer = $this->first();

        // Verifica se o customer foi encontrado
        if (!$customer) {
            throw new \RuntimeException('Customer não encontrado ou você não tem permissão para visualizá-lo.');
        }

        // Buscar detalhes das anamneses associadas ao customer
        $anamneses = $this->db->table('anamneses')
            ->where('id_customer', $id)
            ->get()
            ->getResultArray();

        // Retornar os dados do customer com as anamneses
        return [
            'id' => $customer['id'],
            'name' => $customer['name'],
            'photo' => $customer['photo'],
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'doc' => $customer['doc'],
            'generous' => $customer['generous'],
            'birthDate' => $customer['birthDate'],
            'anamneses_count' => $customer['anamneses_count'],
            'anamneses' => $anamneses, // Lista de anamneses associadas
        ];
    }


    public function deleteCustomer(int $id): void
    {
        // Obter o usuário atual
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        // Verificar se o usuário está autenticado
        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        $currentUserId = $currentUser['id'];

        // Verificar se o customer pertence ao usuário atual
        $customer = $this->where('id', $id)
            ->where('idUser', $currentUserId)
            ->first();

        // Verifica se o customer foi encontrado
        if (!$customer) {
            throw new \RuntimeException('Customer não encontrado ou você não tem permissão para excluí-lo.');
        }

        // Exclui o registro do customer
        if (!$this->delete($id)) {
            // Captura erros da instância correta do Model, se houver
            $errors = $this->errors();
            throw new \RuntimeException('Erro ao excluir o cliente: ' . implode(', ', $errors));
        }
    }
}
