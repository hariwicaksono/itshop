<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'product';
    protected $primaryKey           = 'product_id';
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

    public function getProduct($page = false, $limit = false)
    {
        $offset = ($page - 1) * $limit;
        $this->select("{$this->table}.*, m.media_path");
        $this->join("media m", "m.media_id = {$this->table}.product_image", "left");
        $this->orderBy("{$this->table}.product_id", "DESC");
        $query = $this->findAll($limit, $offset);
        return $query;
    }

    public function showProduct($id)
    {
        $this->select("{$this->table}.*, m.media_path");
        $this->join("media m", "m.media_id = {$this->table}.product_image", "left");
        $this->where("{$this->table}.product_id", $id);
        //$this->orderBy("{$this->table}.product_id", "ASC");
        $query = $this->first();
        return $query;
    }

    public function countProduct($status = null)
    {
        return $this->db->table('product')
            ->where('active', $status)
            ->countAllResults();
    }
}
