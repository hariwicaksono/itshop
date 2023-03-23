<?php

namespace App\Modules\Log\Models;

/*
IT Shop Purwokerto (Tokopedia, Shopee & Bukalapak)
Dibuat oleh: Hari Wicaksono, S.Kom
03-2023
*/

use CodeIgniter\Model;

class LogUserModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users_log';
    protected $primaryKey       = 'id_user_log';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

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
