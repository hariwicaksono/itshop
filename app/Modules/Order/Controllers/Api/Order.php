<?php

namespace App\Modules\Order\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Order\Models\OrderModel;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Cart\Models\CartModel;
use App\Modules\Payment\Models\PaymentModel;
use App\Modules\User\Models\UserModel;
use App\Libraries\Settings;

class Order extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = OrderModel::class;
    protected $product;
    protected $cart;
    protected $user;
    protected $payment;
    protected $setting;

    public function __construct()
    {
        $this->product = new ProductModel();
        $this->cart = new CartModel();
        $this->user = new UserModel();
        $this->payment = new PaymentModel();
        $this->setting = new Settings();
    }

    public function index()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->getOrders()], 200);
    }

    public function show($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->showOrder($id)], 200);
    }

    public function create()
    {
        $rules = [
            'user_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $no_order = $json->no_order;
            $user_id = $json->user_id;
            $total = $json->total;
            $note = $json->note;
            $payment = $json->payment;
            $shipment = $json->shipment;
        } else {
            $no_order = $this->request->getPost('no_order');
            $user_id = $this->request->getPost('user_id');
            $total = $this->request->getPost('total');
            $note = $this->request->getPost('note');
            $payment = $this->request->getPost('payment');
            $shipment = $this->request->getPost('shipment');
        }

        $cek = $this->payment->where(['payment_id' => $payment])->first();
        if ($cek['payment_id'] == 1 || $cek['cod'] == 1) {
            $grandtotal = $total;
        } else {
            $grandtotal = $total + rand(1, 100);
        }

        $user = $this->user->find($user_id);
        $userEmail = $user['email'];
        $userPhone = $user['phone'];

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $input = $this->request->getVar('data');

            foreach ($input as $value) {
                $product_id[] = $value[1];
                $stock[] = $value[3];
                $qty[] = $value[4];
            }

            $total_product = count($product_id);

            $dataOrder = [
                'no_order' => strtoupper($no_order),
                'user_id' => $user_id,
                'qty' => $total_product,
                'total' => $grandtotal,
                'payment' => $payment,
                'shipment' => $shipment,
                'note' => $note,
                'status' => 0,
                'status_payment' => 'pending',
                'periode' => date('m-Y'),
            ];
            $this->model->save($dataOrder);
            $order_id = $this->model->getInsertID();

            $order = $this->model->find($order_id);
            $orderCreated = $order['created_at'];

            $arrCart = array();
            foreach ($input as $key => $value) {
                $cart_id = $value[0];
                $product_id = $value[1];
                $price = $value[2];
                $stock = $value[3];
                $qty = $value[4];

                $cart = array(
                    'cart_id' => $cart_id,
                    'order_id' => $order_id,
                );
                array_push($arrCart, $cart);
            }
            $dataCart = $arrCart;
            $this->cart->updateBatch($dataCart, 'cart_id');

            $arrStock = array();
            foreach ($input as $key => $value) {
                $cart_id = $value[0];
                $product_id = $value[1];
                $price = $value[2];
                $stock = $value[3];
                $qty = $value[4];

                $stock = array(
                    'product_id' => $product_id,
                    'stock' => $stock - $qty,
                );
                array_push($arrStock, $stock);
            }
            $dataStock = $arrStock;
            $this->product->updateBatch($dataStock, 'product_id');

            if ($cek['payment_id'] == 2) {
                //Send Email New Order
                helper('email');
                $email = $this->setting->info['company_email2'];
                $dataEmail = [
                    'no_order' => $no_order,
                    'created_at' => $orderCreated,
                    'email' => $userEmail,
                    'phone' => $userPhone,
                    'qty' => $total_product,
                    'total' => $grandtotal,
                    'note' => $note,
                ];
                sendEmail("Pesanan Baru #$no_order Siap Dikirim", $email, view('App\Modules\Order\Views\email/order_new_tf', $dataEmail));
            }

            $response = [
                'status' => true,
                'message' => lang('App.orderSuccess'),
                'data' => ["url" => "/checkout/success/pending?order_id=$order_id&user_id=$user_id"],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'id_produk' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'id_produk' => $json->id_produk,
                'id_member' => $json->id_member,
                'jumlah' => $json->jumlah,
                'total' => $json->total,
                'periode' => $json->periode,
            ];
        } else {
            $data = $this->request->getRawInput();
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.reqFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $simpan = $this->model->update($id, $data);
            if ($simpan) {
                $response = [
                    'status' => true,
                    'message' => lang('App.productUpdated'),
                    'data' => [],
                ];
                return $this->respond($response, 200);
            }
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);
        if ($hapus) {
            $this->model->delete($id);
            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function setStatus($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'status' => $json->status
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'status' => $input['status']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setStatusPayment($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'status_payment' => $json->status_payment
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'status_payment' => $input['status_payment']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function chart1()
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->getChart1()], 200);
    }

    public function getUserOrder($id = null)
    {
        return $this->respond([
            "status" => true, 
            "message" => lang("App.getSuccess"), 
            "data" => $this->model->findOrders($id)
        ], 200);
    }

    public function getUserOrderPending($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 0)], 200);
    }

    public function getUserOrderProcessed($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 1)], 200);
    }

    public function getUserOrderDelivered($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 2)], 200);
    }

    public function getUserOrderCanceled($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 3)], 200);
    }

    public function countUserOrder($userid = null)
    {
        $userid = $this->session->id;
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->countUserOrder($userid)], 200);
    }
}
