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
}
