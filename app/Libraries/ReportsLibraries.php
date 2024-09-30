<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\AnamnesesModel;
use App\Models\AppointmentsModel;

class ReportsLibraries
{
    protected $mAnanmanese;
    protected $mAppointments;

    public function __construct()
    {
        $this->mAnanmanese  = new AnamnesesModel();
        $this->mAppointments = new AppointmentsModel();
    }

    public function resultReports() {}

    public function mensal(int $ano = null)
    {
        // Define o ano atual, se não for passado como parâmetro
        $ano = $ano ?? date('Y');

        // Definir o ID do usuário (este é um exemplo fixo, pode ser substituído por autenticação real)
        $user = $this->mAppointments->getAuthenticatedUser();
        $idUser = $user['id'];

        // Definir o número de meses (12)
        $totalMes = 12;
        $result = [];

        for ($i = 1; $i <= $totalMes; $i++) {
            // Garantir que o mês tenha dois dígitos
            $mes = str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            // Formatar a data como "YYYY-MM"
            $data = "{$ano}-{$mes}";

            // Contar os compromissos para o usuário no mês atual
            $numAppointments = $this->mAppointments
                ->where('id_user', $idUser)
                ->where('status !=', 'cancelled')
                ->like('created_at', $data)
                ->countAllResults();

            $numCancelleds = $this->mAppointments
                ->where('id_user', $idUser)
                ->where('status', 'cancelled')
                ->like('created_at', $data)
                ->countAllResults();

            $numReturn = $this->mAppointments
                ->where('id_user', $idUser)
                ->where('type', 'return')
                ->like('created_at', $data)
                ->countAllResults();

            $numAnamneses = $this->mAnanmanese
                ->where('id_user', $idUser)
                ->like('created_at', $data)
                ->countAllResults();

            // Adicionar o resultado ao array
            $result[] = [
                'date' => $data,
                'appointments' => $numAppointments,
                'cancelled' => $numCancelleds,
                'anamneses' => $numAnamneses,
                'return' => $numReturn
            ];
        }

        // Retornar o resultado como um array de relatórios mensais
        return $result;
    }
}
