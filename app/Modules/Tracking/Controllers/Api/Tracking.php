<?php

namespace App\Modules\Tracking\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Tracking\Models\TrackingModel;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Cart\Models\CartModel;
use App\Modules\Payment\Models\PaymentModel;
use App\Modules\User\Models\UserModel;
use App\Libraries\Settings;
use CodeIgniter\I18n\Time;

class Tracking extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = TrackingModel::class;
    protected $product;
    protected $cart;
    protected $user;
    protected $payment;
    protected $setting;

    public function __construct()
    {
       $this->setting = new Settings();
    }

    public function index($id = null)
    {
        $data = $this->model->showTrackingOrder($id);
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
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
}
