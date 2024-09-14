<?php

namespace App\Models\Appointments\V1;

use App\Models\AppointmentsModel;
use App\Models\UsersModel;

/**
 * Classe listAppointments
 *
 * Extende AppointmentsModel para listar compromissos com base nos parâmetros fornecidos.
 */
class listAppointments extends AppointmentsModel
{
    /**
     * Lista compromissos com base nos parâmetros fornecidos.
     *
     * @param array $params Parâmetros para filtrar, ordenar e paginar os compromissos.
     * @return array Resultados paginados dos compromissos.
     */
    public function listAppointments(array $params): array
    {
        $currentUser = $this->getAuthenticatedUser();

        // Extrai e valida parâmetros
        $searchTerm   = $params['s'] ?? null;
        $currentPage  = $this->validatePageNumber($params['page'] ?? 1);
        $sortBy       = $this->validateSortBy($params['sort_by'] ?? 'id');
        $sortOrder    = $this->validateSortOrder($params['order'] ?? 'ASC');
        $status       = $this->validateStatus($params['status'] ?? null);
        $itemsPerPage = $this->validateItemsPerPage($params['limite'] ?? null);
        $dateRange    = $this->getDateRange($params);

        // Constrói a consulta dos compromissos
        $this->buildAppointmentQuery($currentUser, $searchTerm, $sortBy, $sortOrder, $dateRange, $status);

        // Pagina os resultados e formata a resposta
        return $this->paginateResults($itemsPerPage, $currentPage, $params, $dateRange);
    }

    /**
     * Obtém o usuário autenticado.
     *
     * @return array Dados do usuário autenticado.
     * @throws \RuntimeException Se o usuário não estiver autenticado.
     */
    private function getAuthenticatedUser(): array
    {
        $userModel = new UsersModel();
        $currentUser = $userModel->me();

        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        return $currentUser;
    }

    /**
     * Valida o número da página.
     *
     * @param mixed $page Número da página.
     * @return int Número da página validado.
     */
    private function validatePageNumber($page): int
    {
        return (intval($page) > 0) ? intval($page) : 1;
    }

    /**
     * Valida o campo de ordenação.
     *
     * @param mixed $sortBy Campo de ordenação.
     * @return string Campo de ordenação validado.
     */
    private function validateSortBy($sortBy): string
    {
        $allowedSortFields = ['id', 'date', 'name', 'status'];
        return in_array($sortBy, $allowedSortFields) ? $sortBy : 'id';
    }

    /**
     * Valida a ordem de ordenação.
     *
     * @param mixed $order Ordem de ordenação.
     * @return string Ordem de ordenação validada (ASC ou DESC).
     */
    private function validateSortOrder($order): string
    {
        return strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
    }

    /**
     * Valida o status do compromisso.
     *
     * @param mixed $status Status do compromisso.
     * @return string|null Status validado ou null se inválido.
     */
    private function validateStatus($status)
    {
        $allowedSortFields = ['pending', 'completed', 'cancelled'];
        return in_array($status, $allowedSortFields) ? $status : null;
    }

    /**
     * Obtém o intervalo de datas com base nos parâmetros fornecidos.
     *
     * @param array $params Parâmetros de data.
     * @return array Intervalo de datas com 'start' e 'end'.
     */
    private function getDateRange(array $params): array
    {
        $startDate = $this->validateDate($params['start'] ?? null) ?? date('Y-m-d') . ' 00:00:00';
        $endDate   = $this->validateDate($params['end'] ?? null) ?? date('Y-m-d', strtotime('+1 month', strtotime($startDate))) . ' 23:59:59';

        // Limita o intervalo máximo para um mês
        $maxEndDate = date('Y-m-d', strtotime('+1 month', strtotime($startDate))) . ' 23:59:59';
        if ($endDate > $maxEndDate) {
            $endDate = $maxEndDate;
        }

        return ['start' => $startDate, 'end' => $endDate];
    }

    /**
     * Constrói a consulta para listar compromissos.
     *
     * @param array $currentUser Usuário atual autenticado.
     * @param string|null $searchTerm Termo de busca.
     * @param string $sortBy Campo de ordenação.
     * @param string $sortOrder Ordem de ordenação.
     * @param array $dateRange Intervalo de datas.
     * @param string|null $status Status do compromisso.
     */
    private function buildAppointmentQuery($currentUser, $searchTerm, $sortBy, $sortOrder, $dateRange, $status): void
    {
        // Filtra compromissos por usuário, se não for SUPERADMIN
        if ($currentUser['role'] !== 'SUPERADMIN') {
            $this->where('appointments.id_user', $currentUser['id']);
        }

        $this->select("appointments.id As id_appointment, appointments.date As appointment, appointments.status As status")
            ->select("customers.id As id_customer, customers.name As name_customer")
            ->select("users.id As id_user, users.name As name_user")
            ->join("users", "appointments.id_user = users.id")
            ->join("customers", "appointments.id_customer = customers.id", "left")
            ->orderBy('appointments.' . $sortBy, $sortOrder)
            ->where('date >=', $dateRange['start'])
            ->where('date <=', $dateRange['end']);

        // Filtra por status, se especificado
        if ($status) {
            $this->where('status', $status);
        }

        // Adiciona termos de busca
        if ($searchTerm) {
            $this->groupStart()
                ->like('customers.name', $searchTerm)
                ->orLike('customers.email', $searchTerm)
                ->orLike('customers.phone', $searchTerm)
                ->groupEnd();
        }
    }

    /**
     * Pagina os resultados dos compromissos.
     *
     * @param int $itemsPerPage Itens por página.
     * @param int $currentPage Página atual.
     * @param array $params Parâmetros adicionais.
     * @param array $dateRange Intervalo de datas.
     * @return array Resultados paginados com informações de paginação.
     */
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

    /**
     * Valida o número de itens por página.
     *
     * @param mixed $value Número de itens por página.
     * @return int Número validado de itens por página, entre 1 e 500.
     */
    private function validateItemsPerPage($value)
    {
        $itemsPerPage = (isset($value) && is_numeric($value)) ? intval($value) : 15;
        return min(max($itemsPerPage, 1), 500);
    }

    /**
     * Valida a data no formato 'Y-m-d'.
     *
     * @param string|null $date Data a ser validada.
     * @return string|null Data validada no formato 'Y-m-d H:i:s' ou null se inválida.
     */
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
