<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Exceptions\AuthenticationException;

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

    /**
     * List Appointments with filters, sorting, and pagination.
     *
     * @param array $params Query parameters for filtering, sorting, and pagination.
     * @return array Paginated list of appointments.
     * @throws AuthenticationException When user is not authenticated.
     */
    public function listAppointments(array $params): array
    {
        $currentUser = $this->getAuthenticatedUser();

        // Extract and validate parameters
        $searchTerm   = $params['s'] ?? null;
        $currentPage  = $this->validatePageNumber($params['page'] ?? 1);
        $sortBy       = $this->validateSortBy($params['sort_by'] ?? 'id');
        $sortOrder    = $this->validateSortOrder($params['order'] ?? 'ASC');
        $itemsPerPage = $this->validateItemsPerPage($params['limite'] ?? null);
        $dateRange    = $this->getDateRange($params);

        // Build the appointment query
        $this->buildAppointmentQuery($currentUser, $searchTerm, $sortBy, $sortOrder, $dateRange);

        // Paginate results and format response
        return $this->paginateResults($itemsPerPage, $currentPage, $params, $dateRange);
    }

    private function getAuthenticatedUser(): array
    {
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        return $currentUser;
    }

    private function validatePageNumber($page): int
    {
        return (intval($page) > 0) ? intval($page) : 1;
    }

    private function validateSortBy($sortBy): string
    {
        $allowedSortFields = ['id', 'date', 'name', 'status'];
        return in_array($sortBy, $allowedSortFields) ? $sortBy : 'id';
    }

    private function validateSortOrder($order): string
    {
        return strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
    }

    private function getDateRange(array $params): array
    {
        $startDate = $this->validateDate($params['start'] ?? null) ?? date('Y-m-d') . ' 00:00:00';
        $endDate   = $this->validateDate($params['end'] ?? null) ?? date('Y-m-d', strtotime('+1 month', strtotime($startDate))) . ' 00:00:00';

        $maxEndDate = date('Y-m-d', strtotime('+1 month', strtotime($startDate))) . ' 23:59:59';
        if ($endDate > $maxEndDate) {
            $endDate = $maxEndDate;
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    private function buildAppointmentQuery($currentUser, $searchTerm, $sortBy, $sortOrder, $dateRange): void
    {
        if ($currentUser['role'] !== 'SUPERADMIN') {
            $this->where('appointments.id_user', $currentUser['id']);
        }

        $this->join("customers", "appointments.id_customer = customers.id", "left")
            ->orderBy('appointments.' . $sortBy, $sortOrder)
            ->where('date >=', $dateRange['start'])
            ->where('date <=', $dateRange['end']);

        if ($searchTerm) {
            $this->groupStart()
                ->like('customers.name', $searchTerm)
                ->orLike('customers.email', $searchTerm)
                ->orLike('customers.phone', $searchTerm)
                ->groupEnd();
        }
    }

    private function paginateResults($itemsPerPage, $currentPage, array $params, array $dateRange): array
    {
        $totalItems = $this->countAllResults(false);
        $data = $this->paginate($itemsPerPage, '', $currentPage);

        return [
            'rows'  => $data,
            'params' => $params,
            'dateRange' => $dateRange,
            'pagination' => [
                'current_page'   => $currentPage,
                'total_pages'    => ceil($totalItems / $itemsPerPage),
                'total_items'    => $totalItems,
                'items_per_page' => $itemsPerPage,
                'prev_page'      => ($currentPage > 1) ? $currentPage - 1 : null,
                'next_page'      => ($currentPage < ceil($totalItems / $itemsPerPage)) ? $currentPage + 1 : null,
            ],
        ];
    }

    private function validateItemsPerPage($value)
    {
        $itemsPerPage = (isset($value) && is_numeric($value)) ? intval($value) : 15;
        return min(max($itemsPerPage, 1), 500); // Ensures the number is between 1 and 500
    }

    private function validateDate(?string $date): ?string
    {
        if ($date) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
            if ($dateTime && $dateTime->format('Y-m-d') === $date) {
                return $dateTime->format('Y-m-d') . ' 00:00:00';
            }
        }
        return null;
    }
}
