<?php

namespace App\Controllers;

use App\Models\OrderModel;

class Member extends BaseController
{
    protected $orderModel;

    public function __construct()
    {
        //memeriksa session role selain Admin redirect ke /
        if (session()->get('logged_in') == true && session()->get('role') == 1) {
            header('location:/admin');
            exit();
        }
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $order = $this->orderModel->where(['user_id' => session()->get('id')])->countAllResults();
        return view('member/index', [
            'jmlOrder' => $order,
        ]);
    }

    public function cart()
    {
        return view('member/cart');
    }

    public function checkout()
    {
        $input = $this->request->getVar();
        if ($this->session->id != $input['iduser']) return redirect()->to(base_url('/'));
        $rules = [
            'idorder' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'iduser' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];
        if (!$this->validate($rules)) {
            return redirect()->to(base_url());
        }
        $data = [
            'order' => $this->orderModel->checkoutOrder($input['idorder'],$input['iduser'])
        ];
        return view('member/checkout', $data);
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
