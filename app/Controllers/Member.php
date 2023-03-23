<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Modules\Order\Models\OrderModel;

class Member extends BaseController
{
    protected $cart;
    protected $order;

    public function __construct()
    {
        //memeriksa session role selain Admin redirect ke /
        if (session()->get('logged_in') == true && session()->get('role') == 1) {
            header('location:/admin');
            exit();
        }
        helper('text');
        $this->cart = new CartModel();
        $this->order = new OrderModel();
    }

    public function index()
    {
        $order = $this->order->where(['user_id' => session()->get('id')])->countAllResults();
        return view('member/index', [
            'jmlOrder' => $order,
        ]);
    }

    public function cart()
    {
        return view('member/cart', [
            'title' => lang('App.cart'),
        ]);
    }

    public function checkoutProcess()
    {
        
        $userid = $this->session->id;
        $cart = $this->cart->getUserCart($userid);
        $total = $this->cart->sumUserCart($userid);

        //$query = $this->order->select("max(order_id) as last");
        //$hasil = $query->get()->getRowArray();
        //$last = $hasil['last'] + 1;
        //$no_order = sprintf('%04s', $last);
       
        $no_order = date('ymd') . strtoupper(random_string('alnum', 7));
        //var_dump($total);die;

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => $no_order,
                'gross_amount' => $total,
            ),
            'customer_details' => array(
                'first_name' => $this->session->first_name,
                'last_name' => $this->session->last_name,
                'email' => $this->session->email,
                'phone' => $this->session->phone,
            ),
        );
        
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return view('member/checkout_process', [
            'title' => lang('App.checkout'),
            'clientKey' => $clientKey,
            'total' => $total,
            'SnapToken' => $snapToken,
            'noOrder' => $no_order
        ]);
    }

    public function checkoutPGSuccess()
    {
        //$input = $this->request->getVar();
        //if ($this->session->id != $input['iduser']) return redirect()->to(base_url('/'));
        //$rules = [
            //'idorder' => [
                //'rules'  => 'required',
                //'errors' => []
            //],
            //'iduser' => [
                //'rules'  => 'required',
                //'errors' => []
            //],
        //];
        //if (!$this->validate($rules)) {
            //return redirect()->to(base_url());
        //}
        $data = [
            'title' => 'Checkout Success',
            //'order' => $this->order->checkoutOrder($input['idorder'], $input['iduser'])
        ];
        return view('member/checkout_pg', $data);
    }

    public function checkoutTFSuccess()
    {
        $input = $this->request->getVar();
        if ($this->session->id != $input['user_id']) return redirect()->to(base_url('/'));
        $rules = [
            'order_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'user_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];
        if (!$this->validate($rules)) {
            return redirect()->to(base_url());
        }
        $data = [
            'title' => 'Checkout Success',
            'order' => $this->order->checkoutOrder($input['order_id'], $input['user_id'])
        ];
        return view('member/checkout_tf', $data);
    }

    public function profile()
    {
        return view('member/profile');
    }

    public function order()
    {
        return view('member/order');
    }
}
