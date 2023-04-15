<?php

namespace App\Modules\Tracking\Models;

use CodeIgniter\Model;

class TrackingModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'orders_tracking';
    protected $primaryKey           = 'tracking_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function showTrackingOrder($id)
    {
        $this->select("{$this->table}.*, o.no_order");
        $this->join("orders o", "o.order_id = {$this->table}.order_id");
        $this->where("{$this->table}.order_id", $id);
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

}
