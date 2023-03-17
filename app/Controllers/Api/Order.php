<?php

namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\CartModel;
use App\Models\PaymentModel;

class Order extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = OrderModel::class;
    protected $product;
    protected $cart;

    public function __construct()
    {
        $this->product = new ProductModel();
        $this->cart = new CartModel();
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
            $userid = $json->user_id;
            $total = $json->total;
            $note = $json->note;
            $payment = $json->payment;
            $shipment = $json->shipment;
        } else {
            $userid = $this->request->getPost('user_id');
            $total = $this->request->getPost('total');
            $note = $this->request->getPost('note');
            $payment = $this->request->getPost('payment');
            $shipment = $this->request->getPost('shipment');
        }

        $pymodel = new PaymentModel();
        $cek = $pymodel->where(['payment_id' => $payment])->first();
        if ($cek['cod'] == 0) {
            $grandtotal = $total + rand(1, 100);
        } else {
            $grandtotal = $total;
        }

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
                $product_id[] = $value[0];
                $stock[] = $value[1];
                $qty[] = $value[2];
            }

            $total_product = count($product_id);

            $query = $this->model->select("max(order_id) as last");
            $hasil = $query->get()->getRowArray();
            $last = $hasil['last'] + 1;
            $no_order = sprintf('%04s', $last);

            $dataOrder = [
                'no_order' => 'INV' . date('Ymd') . $no_order,
                'user_id' => $userid,
                'qty' => $total_product,
                'total' => $grandtotal,
                'payment' => $payment,
                'shipment' => $shipment,
                'note' => $note,
                'status' => 0,
                'periode' => date('m-Y'),
            ];
            $this->model->save($dataOrder);
            $idorder = $this->model->getInsertID();

            $arrCart = array();
            foreach ($input as $key => $value) {
                $product_id = $value[0];
                $price = $value[1];
                $stock = $value[2];
                $qty = $value[3];

                $cart = array(
                    'user_id' => $userid,
                    'order' => $idorder,
                );
                array_push($arrCart, $cart);
            }
            $dataCart = $arrCart;
            $this->cart->updateBatch($dataCart, 'user_id');

            $arrStock = array();
            foreach ($input as $key => $value) {
                $product_id = $value[0];
                $price = $value[1];
                $stock = $value[2];
                $qty = $value[3];

                $stock = array(
                    'product_id' => $product_id,
                    'stock' => $stock - $qty,
                );
                array_push($arrStock, $stock);
            }
            $dataStock = $arrStock;
            $this->product->updateBatch($dataStock, 'product_id');

            $response = [
                'status' => true,
                'message' => lang('App.orderSuccess'),
                'data' => ["url" => "/checkout-success?idorder=$idorder&iduser={$this->session->id}"],
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

    public function chart1()
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->getChart1()], 200);
    }

    public function getUserOrder($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findOrders($id)], 200);
    }

    public function getUserOrderPending($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 0)], 200);
    }

    public function getUserOrderDelivered($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 1)], 200);
    }

    public function getUserOrderCanceled($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findUserOrder($id, 2)], 200);
    }
}
