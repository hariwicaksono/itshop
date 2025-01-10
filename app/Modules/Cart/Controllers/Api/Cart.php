<?php

namespace App\Modules\Cart\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Cart\Models\CartModel;
use App\Modules\Product\Models\ProductModel;

class Cart extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = CartModel::class;
    protected $product;

    public function __construct()
    {
        $this->product = new ProductModel();
    }

    public function index()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->getCart()], 200);
    }

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->find($id)], 200);
    }

    public function create()
    {
        $rules = [
            'product_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'user_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $product_id = $json->product_id;
            $qty = $json->qty;
            $user_id = $json->user_id;

            $data = $this->product->where(['product_id' => $product_id])->first();
            $price = $data['product_price'];
            $discount = $data['discount'];
            $discountPercent = $data['discount_percent'];
            if ($discount > 0) {
                $subTotal = $price - $discount;
            } else {
                $subTotal = $price;
            }
            $total = $subTotal * $qty;
            $data = [
                'product_id' => $product_id,
                'user_id' => $user_id,
                'price' => $json->price,
                'discount' => $discount,
                'discount_percent' => $discountPercent,
                'stock' => $json->stock,
                'qty' => $qty,
                'total' => $total,
            ];
        } else {
            $product_id = $this->request->getPost('product_id');
            $qty = $this->request->getPost('qty');
            $user_id = $this->request->getPost('user_id');

            $data = $this->product->where(['product_id' => $product_id])->first();
            $price = $data['product_price'];
            $discount = $data['discount'];
            $discountPercent = $data['discount_percent'];
            if ($discount > 0) {
                $subTotal = $price - $discount;
            } else {
                $subTotal = $price;
            }
            $total = $subTotal * $qty;
            $data = [
                'product_id' => $product_id,
                'user_id' => $user_id,
                'price' => $this->request->getPost('price'),
                'discount' => $discount,
                'discount_percent' => $discountPercent,
                'stock' => $this->request->getPost('stock'),
                'qty' => $qty,
                'total' => $total,
            ];
        }

        if (empty($user_id)) :
            $response = [
                'status' => false,
                'message' => lang('App.pleaseLogin'),
                'data' => ["url" => base_url('/login')],
            ];
            return $this->respond($response, 200);
        endif;

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            //cari cart apakah barang sudah ada di keranjang tetapi order_id = null
            $searchCart = $this->model->where(['product_id' => $product_id, 'order_id' => null])->first();
            if ($searchCart) {
                $idCart = $searchCart['cart_id'];
                $price = $searchCart['price'];
                $discount = $searchCart['discount'];
                $discountPercent = $searchCart['discount_percent'];
                $cartQty = $searchCart['qty'] + $qty;
                if ($discount > 0) {
                    $subTotal = $price - $discount;
                } else {
                    $subTotal = $price;
                }
                $total = $subTotal * $cartQty;
                $update = [
                    'qty' => $cartQty,
                    'total' => $total,
                ];

                $product = $this->product->where(['product_id' => $product_id])->first();
                $stock = $product['stock'];

                if ($qty >= $stock) {
                    $response = [
                        'status' => false,
                        'message' => 'Out of Stock, Please try again.',
                        'data' => [],
                    ];
                    return $this->respond($response, 200);
                } else {
                    //lalu update qty nya
                    $this->model->update($idCart, $update);
                }
            } else {
                $this->model->save($data);
            }

            $response = [
                'status' => true,
                'message' => lang('App.productSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $id = $this->model->find($id);
        $input = $this->getRequestInput();
        $product_id = $input['product_id'];
        $qty = $input['qty'];

        $product = $this->product->where(['product_id' => $product_id])->first();
        $price = $product['product_price'];
        $discount = $product['discount'];
        $discountPercent = $product['discount_percent'];
        if ($discount > 0) {
            $subTotal = $price - $discount;
        } else {
            $subTotal = $price;
        }
        if ($product['stock'] > $qty) {
            $price = $price;
            $total = $subTotal * $qty;
            $data = [
                'qty' => $qty,
                'total' => $total,
            ];
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
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

    public function truncate()
    {
        if ($this->model->truncate()) {
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

    public function getUserCart($userid = null)
    {
        $userid = $this->session->id;
        $isNull = true;
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->getUserCart($userid, $isNull)], 200);
    }

    public function countUserCart($userid = null)
    {
        $userid = $this->session->id;
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->countUserCart($userid)], 200);
    }

    public function findItem($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findOrderItem($id)], 200);
    }

    public function getOrderItem($userid = null)
    {
        $userid = $this->session->id;
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->getUserCart($userid)], 200);
    }
}
