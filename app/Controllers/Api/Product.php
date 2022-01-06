<?php

namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\ProductModel;
use App\Models\MediaModel;

class Product extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = ProductModel::class;

    public function index()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->getProduct()], 200);
    }

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->showProduct($id)], 200);
    }

    public function create()
    {
        $rules = [
            'product_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_price' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_image' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'product_name' => $json->product_name,
                'product_price' => $json->product_price,
                'product_description' => $json->product_description,
                'product_image' => $json->product_image,
                'stock' => 0,
                'active' => 1
            ];
        } else {
            $data = [
                'product_name' => $this->request->getPost('product_name'),
                'product_price' => $this->request->getPost('product_price'),
                'product_description' => $this->request->getPost('product_description'),
                'product_image' => $this->request->getPost('product_image'),
                'stock' => 0,
                'active' => 1
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->save($data);
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
        $rules = [
            'product_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_price' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_image' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'product_name' => $json->product_name,
                'product_price' => $json->product_price,
                'product_description' => $json->product_description,
                'product_image' => $json->product_image
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

        $media = new MediaModel();
        $gambar = $media->find($hapus['product_image']);
        if ($hapus) {
            if (empty($gambar)) {
                $this->model->delete($id);
            } else {
                $this->model->delete($id);
                $media->delete($gambar['media_id']);
                unlink($gambar['media_path']);
            }

            $response = [
                'status' => true,
                'message' => lang('App.productDeleted'),
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

    public function allProduct()
    {
        $input = $this->request->getVar();
        $page = $input['page'];
        $limit = 4;
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->getProduct($page, $limit), "per_page" => $limit, "total_page" => $this->model->countAllResults()], 200);
    }

    public function setPrice($id = NULL)
    {

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'product_price' => $json->product_price,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'product_price' => $input['product_price'],
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);

            $response = [
                'status' => true,
                'message' => lang('App.productUpdated'),
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

    public function setStock($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'stock' => $json->stock,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'stock' => $input['stock'],
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);

            $response = [
                'status' => true,
                'message' => lang('App.productUpdated'),
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

    public function setActive($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'active' => $json->active,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'active' => $input['active'],
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);

            $response = [
                'status' => true,
                'message' => lang('App.productUpdated'),
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
}