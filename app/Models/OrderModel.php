<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'orders';
    protected $primaryKey           = 'order_id';
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

    public function getOrders()
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment, sh.shipment, u.username, u.email");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment");
        $query = $this->findAll();
        return $query;
    }

    public function showOrder($id)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment, py.account, py.number, sh.shipment, u.username, u.email");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment");
        $this->where("{$this->table}.order_id", $id);
        $query = $this->findAll();
        return $query;
    }

    public function findOrders($userid)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment, sh.shipment, u.username, u.email");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment");
        $this->where("{$this->table}.user_id", $userid);
        $query = $this->findAll();
        return $query;
    }

    public function findUserOrder($userid, $status)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment, sh.shipment, u.username, u.email");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment");
        $this->where("{$this->table}.user_id", $userid);
        $this->where("{$this->table}.status", $status);
        $query = $this->findAll();
        return $query;
    }

    public function checkoutOrder($orderid, $userid)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment, py.account, py.number, sh.shipment, u.username, u.email");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment");
        $this->where("{$this->table}.order_id", $orderid);
        $this->where("{$this->table}.user_id", $userid);
        $query = $this->findAll();
        return $query;
    }

    public function getChart1()
    {
        return $this->db->query("SELECT periode as tahun, COUNT(order_id) as jumlah FROM `{$this->table}` GROUP BY periode")->getResultArray();
    }
}
