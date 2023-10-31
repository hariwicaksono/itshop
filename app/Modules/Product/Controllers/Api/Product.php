<?php

namespace App\Modules\Product\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Media\Models\MediaModel;
use Ramsey\Uuid\Uuid;
use ShortUUID\ShortUUID;
use CodeIgniter\I18n\Time;

class Product extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = ProductModel::class;

    /**
     * Update the provided string to a slug-safe format.
     *
     * @param string $string
     * @return string
     */
    function slugify($string)
    {
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities(preg_replace('/[&]/', ' and ', $string), ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }

    public function index()
    {
        $data = $this->model->getProduct();
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

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->showProduct($id)], 200);
    }

    public function create()
    {
        $uuid = Uuid::uuid4();
        $suuid = new ShortUUID();

        $rules = [
            'category_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_code' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_price' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'stock' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $productPrice = (int)$json->product_price;
            $discount = (int)$json->discount;
            $hitung = $productPrice - $discount;
            $persen = $productPrice - $hitung;
            if ($discount != 0) {
                $discountPercent = @($persen / $productPrice) * 100;
            } else {
                $discountPercent = 0;
            }
            $data = [
                'product_id' => strtotime(Time::now()),
                'product_uuid' => $suuid->encode($uuid),
                'category_id' => $json->category_id,
                'product_code' => $json->product_code,
                'product_name' => $json->product_name,
                'product_price' => $productPrice,
                'product_description' => nl2br($json->product_description),
                'product_image' => $json->product_image ?? null,
                'product_image1' => $json->product_image1 ?? null,
                'product_image2' => $json->product_image2 ?? null,
                'product_image3' => $json->product_image3 ?? null,
                'product_image4' => $json->product_image4 ?? null,
                'discount' => $discount,
                'discount_percent' => $discountPercent,
                'stock' => $json->stock,
                'stock_min' => $json->stock_min,
                'active' => 1,
                'user_id' => session()->get('id'),
                'slug' => $this->slugify($json->product_name),
                'link_demo' => $json->link_demo ?? null,
            ];
        } else {
            $productPrice = (int)$this->request->getPost('product_price');
            $discount = (int)$this->request->getPost('discount');
            $hitung = $productPrice - $discount;
            $persen = $productPrice - $hitung;
            if ($discount != 0) {
                $discountPercent = @($persen / $productPrice) * 100;
            } else {
                $discountPercent = 0;
            }
            $data = [
                'product_id' => strtotime(Time::now()),
                'product_uuid' => $suuid->encode($uuid),
                'category_id' => $this->request->getPost('category_id'),
                'product_code' => $this->request->getPost('product_code'),
                'product_name' => $this->request->getPost('product_name'),
                'product_price' => $productPrice,
                'product_description' => nl2br($this->request->getPost('product_description') ?? ""),
                'product_image' => $this->request->getPost('product_image') ?? null,
                'product_image1' => $this->request->getPost('product_image1') ?? null,
                'product_image2' => $this->request->getPost('product_image2') ?? null,
                'product_image3' => $this->request->getPost('product_image3') ?? null,
                'product_image4' => $this->request->getPost('product_image4') ?? null,
                'discount' => $discount,
                'discount_percent' => $discountPercent,
                'stock' => $this->request->getPost('stock'),
                'stock_min' => $this->request->getPost('stock_min'),
                'active' => 1,
                'user_id' => session()->get('id'),
                'slug' => $this->slugify($this->request->getPost('product_name')),
                'link_demo' => $this->request->getPost('link_demo') ?? null,
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
            'product_code' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_name' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'product_price' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $productPrice = (int)$json->product_price;
            $discount = (int)$json->discount;
            $hitung = $productPrice - $discount;
            $persen = $productPrice - $hitung;
            if ($discount != 0) {
                $discountPercent = @($persen / $productPrice) * 100;
            } else {
                $discountPercent = 0;
            }
            $data = [
                'category_id' => $json->category_id,
                'product_code' => $json->product_code,
                'product_name' => $json->product_name,
                'product_price' => $json->product_price,
                'product_description' => $json->product_description,
                'product_image' => $json->product_image,
                'product_image1' => $json->product_image1,
                'product_image2' => $json->product_image2,
                'product_image3' => $json->product_image3,
                'product_image4' => $json->product_image4,
                'discount' => $discount,
                'discount_percent' => $discountPercent,
                'slug' => $this->slugify($json->product_name),
                'link_demo' => $json->link_demo,
                'stock' => $json->stock,
                'stock_min' => $json->stock_min,
                
            ];
        } else {
            $input = $this->request->getRawInput();
            $productPrice = (int)$input['product_price'];
            $discount = (int)$input['discount'];
            $hitung = $productPrice - $discount;
            $persen = $productPrice - $hitung;
            if ($discount != 0) {
                $discountPercent = @($persen / $productPrice) * 100;
            } else {
                $discountPercent = 0;
            }
            $data = [
                'category_id' => $input['category_id'],
                'product_code' => $input['product_code'],
                'product_name' => $input['product_name'],
                'product_price' => $productPrice,
                'product_description' => $input['product_description'],
                'product_image' => $input['product_image'],
                'product_image1' => $input['product_image1'],
                'product_image2' => $input['product_image2'],
                'product_image3' => $input['product_image3'],
                'product_image4' => $input['product_image4'],
                'discount' => $discount,
                'discount_percent' => $discountPercent,
                'slug' => $this->slugify($input['product_name']),
                'link_demo' => $input['link_demo'],
                'stock' => $this->request->getPost('stock'),
                'stock_min' => $this->request->getPost('stock_min'),
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
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.productUpdated'),
                'data' => [],
            ];
            return $this->respond($response, 200);
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
        $limit = $input['limit'];
        $where = $input['category'] ?? "";
        $orderBy = $input['sort_by'];
        $data = $this->model->getProduct($page, $limit, $where, $orderBy);
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "per_page" => $limit, 
                "total_page" => $this->model->countAllResults()
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

    public function bestSeller()
    {
        $data = $this->model->bestSeller();
        //var_dump($this->model->getLastQuery()->getQuery());die;
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

}
