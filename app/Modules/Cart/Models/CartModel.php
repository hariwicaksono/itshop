<?php

namespace App\Modules\Cart\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'carts';
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
        $this->select("{$this->table}.*, p.product_code, p.product_name, p.product_price, p.product_price_normal, m.media_path, m1.media_path as media_path1,  m2.media_path as media_path2, m3.media_path as media_path3, m4.media_path as media_path4, p.slug, c.category_name, c.category_slug");
        $this->join("products p", "p.product_id = {$this->table}.product_id");
        $this->join("media m", "m.media_id = p.product_image", "left");
        $this->join("media m1", "m1.media_id = p.product_image1", "left");
        $this->join("media m2", "m2.media_id = p.product_image2", "left");
        $this->join("media m3", "m3.media_id = p.product_image3", "left");
        $this->join("media m4", "m4.media_id = p.product_image4", "left");
        $this->join("category c", "c.category_id = p.category_id");
        $this->orderBy("{$this->table}.cart_id", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function getUserCart($userid = null, $where = false)
    {
        $this->select("{$this->table}.*, p.product_code, p.product_name, p.product_price, p.product_price_normal, m.media_path, m1.media_path as media_path1,  m2.media_path as media_path2, m3.media_path as media_path3,  m4.media_path as media_path4, p.slug, c.category_name, c.category_slug");
        $this->join("products p", "p.product_id = {$this->table}.product_id");
        $this->join("media m", "m.media_id = p.product_image", "left");
        $this->join("media m1", "m1.media_id = p.product_image1", "left");
        $this->join("media m2", "m2.media_id = p.product_image2", "left");
        $this->join("media m3", "m3.media_id = p.product_image3", "left");
        $this->join("media m4", "m4.media_id = p.product_image4", "left");
        $this->join("category c", "c.category_id = p.category_id");
        $this->where("{$this->table}.user_id", $userid);
        if ($where == true) {
            $this->where("{$this->table}.order_id", null);
        }
        $this->orderBy("{$this->table}.cart_id", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function countUserCart($userid = null)
    {
        $query = $this->where(['user_id' => $userid, 'order_id' => null])->countAllResults();
        return $query;
    }

    public function sumUserCart($userid = null)
    {
        $this->select("(sum({$this->table}.total)) as total");
        $this->where("{$this->table}.user_id", $userid);
        $this->where("{$this->table}.order_id", null);
        return $this->get()->getRow()->total;
    }

    public function findOrderItem($id = null)
    {
        $this->select("{$this->table}.*, p.product_code, p.product_name, o.no_order, o.total, o.note, o.status, o.status_payment, py.payment_id, py.payment, sh.shipment, u.username, u.email");
        $this->join("orders o", "o.order_id = {$this->table}.order_id");
        $this->join("products p", "p.product_id = {$this->table}.product_id");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = o.payment_id");
        $this->join("shipment sh", "sh.shipment_id = o.shipment_id");
        $this->where("{$this->table}.order_id", $id);
        $query = $this->findAll();
        return $query;
    }
}
