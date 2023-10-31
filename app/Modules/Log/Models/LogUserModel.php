<?php
/*
PT ITSHOP BISNIS DIGITAL
Toko Online: ITSHOP Purwokerto (Tokopedia.com/itshoppwt, Shopee.co.id/itshoppwt, Bukalapak.com/itshoppwt)
Dibuat oleh: Hari Wicaksono, S.Kom
Created: 11-2021
Modified: 07-2023
*/

namespace App\Modules\Log\Models;

use CodeIgniter\Model;

class LogUserModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users_log';
    protected $primaryKey       = 'user_log_id';
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

    public function getLoginLog($id = false, $limit = false)
    {
        $this->select("{$this->table}.*, users.first_name, users.last_name, users.role");
        $this->join("users", "users.email = {$this->table}.email");
        if ($id) {
            $this->where("{$this->table}.email", $id);
        }
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll($limit);
        return $query;
    }
}
