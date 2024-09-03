<?php

namespace App\Models;

use CodeIgniter\Model;

class AnamnesesModel extends Model
{
    protected $table            = 'anamneses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_user', 'id_customer', 'slug', 'mental_imbalance', 'mental_percentage', 'emotional_imbalance', 'emotional_percentage', 'spiritual_imbalance', 'spiritual_percentage', 'physical_imbalance', 'physical_percentage', 'coronary_chakra_imbalance', 'coronary_chakra_percentage', 'coronary_chakra_activity', 'coronary_chakra_affects_organ', 'frontal_chakra_imbalance', 'frontal_chakra_percentage', 'frontal_chakra_activity', 'frontal_chakra_affects_organ', 'laryngeal_chakra_imbalance', 'laryngeal_chakra_percentage', 'laryngeal_chakra_activity', 'laryngeal_chakra_affects_organ', 'cardiac_chakra_imbalance', 'cardiac_chakra_percentage', 'cardiac_chakra_activity', 'cardiac_chakra_affects_organ', 'solar_plexus_chakra_imbalance', 'solar_plexus_chakra_percentage', 'solar_plexus_chakra_activity', 'solar_plexus_chakra_affects_organ', 'sacral_chakra_imbalance', 'sacral_chakra_percentage', 'sacral_chakra_activity', 'sacral_chakra_affects_organ', 'base_chakra_imbalance', 'base_chakra_percentage', 'base_chakra_activity', 'base_chakra_affects_organ', 'aura_size', 'aura_size_comments', 'opening_size', 'opening_size_comments', 'color_lack', 'color_excess', 'health_energy', 'energy_comments', 'family_area', 'affective_area', 'professional_area', 'financial_area', 'mission_area'];

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


    public function createAnamnese(array $params){
        // Obter o usuário atual
        $userModel   = new UsersModel();
        $userCutomer = new CustomersModel();

        $currentUser = $userModel->me();

        if (!isset($currentUser['id'])) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        $currentUserId = $currentUser['id'];

        // Verifica se o customer pertence ao usuário atual
        $customer = $userCutomer->where('id', $params['idCustomer'])->where('idUser', $currentUserId)->first();

        if(!$customer){
            throw new \RuntimeException('Customer não encontrado ou você não tem permissão para criar a anamnese.');
        }


//return $customer;


        helper('auxiliar');
        $slug = generateSlug();

        // Prepara os dados para inserção
        $data = [
            'id_user' => intval($currentUserId),
            'id_customer' => $params['idCustomer'],
            'slug' => $slug,
            'mental_imbalance' => $params['mentalDesequilibrio'],
            'mental_percentage' => $params['mentalPercentual'],
            'emotional_imbalance' => $params['emocionalDesequilibrio'],
            'emotional_percentage' => $params['emocionalPercentual'],
            'spiritual_imbalance' => $params['espiritualDesequilibrio'],
            'spiritual_percentage' => $params['espiritualPercentual'],
            'physical_imbalance' => $params['fisicoDesequilibrio'],
            'physical_percentage' => $params['fisicoPercentual'],
            'coronary_chakra_imbalance' => $params['chakraCoronarioDesequilibrio'],
            'coronary_chakra_percentage' => $params['chakraCoronarioPercentual'],
            'coronary_chakra_activity' => $params['chakraCoronarioAtividade'],
            'coronary_chakra_affects_organ' => $params['chakraCoronarioOrgao'],
            'frontal_chakra_imbalance' => $params['chakraFrontalDesequilibrio'],
            'frontal_chakra_percentage' => $params['chakraFrontalPercentual'],
            'frontal_chakra_activity' => $params['chakraFrontalAtividade'],
            'frontal_chakra_affects_organ' => $params['chakraFrontalOrgao'],
            'laryngeal_chakra_imbalance' => $params['chakraLaringeoDesequilibrio'],
            'laryngeal_chakra_percentage' => $params['chakraLaringeoPercentual'],
            'laryngeal_chakra_activity' => $params['chakraLaringeoAtividade'],
            'laryngeal_chakra_affects_organ' => $params['chakraLaringeoOrgao'],
            'cardiac_chakra_imbalance' => $params['chakraCardiacoDesequilibrio'],
            'cardiac_chakra_percentage' => $params['chakraCardiacoPercentual'],
            'cardiac_chakra_activity' => $params['chakraCardiacoAtividade'],
            'cardiac_chakra_affects_organ' => $params['chakraCardiacoOrgao'],
            'solar_plexus_chakra_imbalance' => $params['chakraPlexoSolarDesequilibrio'],
            'solar_plexus_chakra_percentage' => $params['chakraPlexoSolarPercentual'],
            'solar_plexus_chakra_activity' => $params['chakraPlexoSolarAtividade'],
            'solar_plexus_chakra_affects_organ' => $params['chakraPlexoSolarOrgao'],
            'sacral_chakra_imbalance' => $params['chakraSacroDesequilibrio'],
            'sacral_chakra_percentage' => $params['chakraSacroPercentual'],
            'sacral_chakra_activity' => $params['chakraSacroAtividade'],
            'sacral_chakra_affects_organ' => $params['chakraSacroOrgao'],
            'base_chakra_imbalance' => $params['chakraBasicoDesequilibrio'],
            'base_chakra_percentage' => $params['chakraBasicoPercentual'],
            'base_chakra_activity' => $params['chakraBasicoAtividade'],
            'base_chakra_affects_organ' => $params['chakraBasicoOrgao'],
            'aura_size' => $params['tamanhoAura'],
            'aura_size_comments' => $params['tamanhoAuraComments'] ?? '',
            'opening_size' => $params['tamanhoAbertura'],
            'opening_size_comments' => $params['tamanhoAberturaComments'] ?? '',
            'color_lack' => implode(', ', $params['corFalta']),
            'color_excess' => implode(', ', $params['corExcesso']),
            'health_energy' => $params['energia'],
            'energy_comments' => $params['energiaComments'] ?? '',
            'family_area' => $params['areasFamiliar'],
            'affective_area' => $params['areasAfetivo'],
            'professional_area' => $params['areasProfissional'],
            'financial_area' => $params['areasFinanceiro'],
            'mission_area' => $params['areasMissao']
        ];

        // Aqui você pode inserir os dados no banco de dados
        $this->insert($data);

        $modelTime = new TimeLinesModel();
        
        $modelTime->insert(
            [
                'idUser'     => intval($currentUserId),
                'idCustomer' => $params['idCustomer'],
                'url'        => base_url("anamnese/{$slug}"),
                'type'       => 'create_anamnese'
            ]
        );

        return ['url' => site_url("anamnese/{$slug}")];
    }
}
