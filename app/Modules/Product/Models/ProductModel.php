<?php

namespace App\Modules\Product\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'products';
    protected $primaryKey           = 'product_id';
    protected $useAutoIncrement     = false;
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

    public function getProduct($page = false, $limit = false, $where = false, $orderBy = false)
    {
        $offset = ($page - 1) * $limit;
        $this->select("{$this->table}.*, m.media_path, m1.media_path as media_path1,  m2.media_path as media_path2, m3.media_path as media_path3,  m4.media_path as media_path4, c.category_name, c.category_slug");
        $this->join("media m", "m.media_id = {$this->table}.product_image", "left");
        $this->join("media m1", "m1.media_id = {$this->table}.product_image1", "left");
        $this->join("media m2", "m2.media_id = {$this->table}.product_image2", "left");
        $this->join("media m3", "m3.media_id = {$this->table}.product_image3", "left");
        $this->join("media m4", "m4.media_id = {$this->table}.product_image4", "left");
        $this->join("category c", "c.category_id = {$this->table}.category_id");
        if ($where != '') :
            $groups = explode(",", $where);
            $this->whereIn("{$this->table}.category_id", $groups);
        /*  $multiple = explode(",", $where);
            if (count($multiple) > 1) {
                $this->where("{$this->table}.category_id", $multiple[0]);
                $this->orWhere("{$this->table}.category_id", $multiple[1]);
            } */
        endif;
        if ($orderBy == 'created_old') {
            $this->orderBy("{$this->table}.created_at", "ASC");
        } else if ($orderBy == 'price_asc') {
            $this->orderBy("{$this->table}.product_price", "ASC");
        } else if ($orderBy == 'price_desc') {
            $this->orderBy("{$this->table}.product_price", "DESC");
        } else {
            $this->orderBy("{$this->table}.created_at", "DESC");
        }
        $query = $this->findAll($limit, $offset);
        return $query;
    }

    public function showProduct($id)
    {
        $this->select("{$this->table}.*, m.media_path, m1.media_path as media_path1,  m2.media_path as media_path2, m3.media_path as media_path3,  m4.media_path as media_path4, c.category_name, c.category_slug");
        $this->join("media m", "m.media_id = {$this->table}.product_image", "left");
        $this->join("media m1", "m1.media_id = {$this->table}.product_image1", "left");
        $this->join("media m2", "m2.media_id = {$this->table}.product_image2", "left");
        $this->join("media m3", "m3.media_id = {$this->table}.product_image3", "left");
        $this->join("media m4", "m4.media_id = {$this->table}.product_image4", "left");
        $this->join("category c", "c.category_id = {$this->table}.category_id");
        $this->where("{$this->table}.product_id", $id);
        $query = $this->first();
        return $query;
    }

    public function countProduct($status = null)
    {
        $this->where('active', $status);
        return $this->countAllResults();
    }

    public function countProductSold($id, $status = null)
    {
        $this->select("{$this->table}.product_id");
        $this->join("carts c", "c.product_id = {$this->table}.product_id");
        $this->join("orders o", "o.order_id = c.order_id");
        $this->where("{$this->table}.product_id", $id);
        $this->where("o.status", $status);
        return $this->countAllResults();
    }

    public function bestSeller()
    {
        $db = \Config\Database::connect();
        $db->simpleQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $builder = $db->table('carts n');
        $builder->select("p.product_code, p.product_name, p.product_price, p.stock, sum(n.qty) qty");
        $builder->join("products p", "p.product_id = n.product_id");
        $builder->join("orders o", "o.order_id = n.order_id");
        $builder->where("o.status", 2);
        $builder->groupBy("n.product_id");
        $builder->orderBy("n.qty", "DESC");
        $builder->limit("5");
        $query = $builder->get()->getResultArray();
        return $query;
    }

    public function searchData($keyword = false)
    {
        $this->select("{$this->table}.*, m.media_path, m1.media_path as media_path1,  m2.media_path as media_path2, m3.media_path as media_path3,  m4.media_path as media_path4, c.category_name, c.category_slug");
        $this->join("media m", "m.media_id = {$this->table}.product_image", "left");
        $this->join("media m1", "m1.media_id = {$this->table}.product_image1", "left");
        $this->join("media m2", "m2.media_id = {$this->table}.product_image2", "left");
        $this->join("media m3", "m3.media_id = {$this->table}.product_image3", "left");
        $this->join("media m4", "m4.media_id = {$this->table}.product_image4", "left");
        $this->join("category c", "c.category_id = {$this->table}.category_id");
        $this->groupStart();
        $this->like("{$this->table}.product_name", $keyword);
        $this->orLike("{$this->table}.product_code", $keyword);
        $this->groupEnd();
        return $this->findAll();
    }
}
