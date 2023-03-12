<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'cart';
    protected $primaryKey           = 'cart_id';
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

    public function getCart()
    {
        $this->select("{$this->table}.*, p.product_name, p.product_price, m.media_path");
        $this->join("product p", "p.product_id = {$this->table}.product_id");
        $this->join("media m", "m.media_id = p.product_image", "left");
        $this->orderBy("{$this->table}.cart_id", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function getUserCart($userid = null)
    {
        $this->select("{$this->table}.*, p.product_name, p.product_price, m.media_path");
        $this->join("product p", "p.product_id = {$this->table}.product_id");
        $this->join("media m", "m.media_id = p.product_image", "left");
        $this->where('user_id', $userid);
        $this->where('order', null);
        $this->orderBy("{$this->table}.cart_id", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function countUserCart($userid = null)
    {
        $query = $this->where(['user_id' => $userid, 'order' => null])->countAllResults();
        return $query;
    }

    public function findOrderItem($id = null)
    {
        $this->select("{$this->table}.*, p.product_name, o.no_order, o.total, py.payment_id, py.payment, sh.shipment, u.username, u.email");
        $this->join("orders o", "o.order_id = {$this->table}.order");
        $this->join("product p", "p.product_id = {$this->table}.product_id");
        $this->join("user u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = o.payment");
        $this->join("shipment sh", "sh.shipment_id = o.shipment");
        $this->where("{$this->table}.order", $id);
        $query = $this->findAll();
        return $query;
    }
}
