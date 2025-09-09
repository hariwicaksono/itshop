<?php

namespace App\Modules\Order\Models;

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

    public function getOrders($start = false, $end = false)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment as payment_name, sh.shipment, u.username, u.email, u.phone");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment_id");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment_id");
        if ($start != "" && $end != "") :
            $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        endif;
        $this->orderBy("{$this->table}.created_at", 'DESC');
        $query = $this->findAll();
        return $query;
    }

    public function showOrder($id)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment as payment_name, py.account, py.number, sh.shipment, u.username, u.email, u.phone");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment_id");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment_id");
        $this->where("{$this->table}.order_id", $id);
        $query = $this->findAll();
        return $query;
    }

    public function findOrders($userid)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment as payment_name, sh.shipment, u.username, u.email, u.phone");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment_id");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment_id");
        $this->where("{$this->table}.user_id", $userid);
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function findUserOrder($userid, $status)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment as payment_name, sh.shipment, u.username, u.email, u.phone");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment_id");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment_id");
        $this->where("{$this->table}.user_id", $userid);
        $this->where("{$this->table}.status", $status);
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function checkoutOrder($orderid, $userid)
    {
        $this->select("{$this->table}.*, py.payment_id, py.payment as payment_name, py.account, py.number, sh.shipment, u.username, u.email, u.phone");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("payment py", "py.payment_id = {$this->table}.payment_id");
        $this->join("shipment sh", "sh.shipment_id = {$this->table}.shipment_id");
        $this->where("{$this->table}.order_id", $orderid);
        $this->where("{$this->table}.user_id", $userid);
        $query = $this->findAll();
        return $query;
    }

    public function getChart1()
    {
        return $this->db->query("SELECT periode as tahun, COUNT(order_id) as jumlah FROM `{$this->table}` GROUP BY periode")->getResultArray();
    }

    public function chartHarian($date)
    {
        $this->like('created_at', $date, 'after');
        return count($this->get()->getResultArray());
    }

    public function chartTransaksi($date)
    {
        $this->like('created_at', $date, 'after');
        return count($this->get()->getResultArray());
    }

    public function countOrder($userid, $status = false)
    {
        $query = $this->where('user_id', $userid)->where('status', $status)->countAllResults();
        return $query;
    }

    public function countNewOrder()
    {
        $query = $this->where('status', 0)->orWhere('status', 1)->countAllResults();
        return $query;
    }

    public function countUserOrder($userid)
    {
        $query = $this->where('user_id', $userid)->where('status', 0)->orWhere('status', 1)->countAllResults();
        return $query;
    }
	
	public function getNewOrder()
    {
        $this->select("{$this->table}.*, p.product_name, u.first_name, u.last_name");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("carts c", "c.order_id = {$this->table}.order_id");
        $this->join("products p", "p.product_id = c.product_id");
        $this->where("{$this->table}.created_at >=", '(NOW() + INTERVAL -7 DAY)', false);
		$this->where("{$this->table}.status", '2');
        $this->orderBy("{$this->table}.created_at", 'DESC');
        $query = $this->findAll();
        return $query;
    }
}
