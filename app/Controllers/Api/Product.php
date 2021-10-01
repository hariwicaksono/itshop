<?php namespace App\Controllers;

use App\Models\ProductModel;
use \Appkita\CI4Restfull\RestfullApi;

class Product extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\ProductModel';
    protected $auth = ['key'];

	public function index()
	{
        $count = $this->model->count_product();
        $id=$this->request->getVar('id');

        if ($id == null) {
			$data = $this->model->getProduct();
		} else {
			$data = $this->model->getProduct($id);
		}
		
        if ($data) {
            $response = [
                'status' => true,
                'message' => 'Berhasil menampilkan semua data',
                'data' => $data,
                'jumlah' => $count
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }
    
    public function show($id = null)
    {
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $this->model->getProduct($id)
            //'data' => $this->model->find($id)
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'category_id' => $input->category_id,
                    'user_id' => $input->user_id,
                    'title' => $input->title,
                    'summary' => $input->summary,
                    'body' => $input->body,
                    'price' => $input->price,
                    'unit' => $input->unit,
                    'post_image' => $input->foto,
                    'date' => $input->date,
                    'time' => $input->time,
                    'created_at' => date("Y-m-d H:i:s")
                ];

                if ($data > 0) {
                    $this->model->save($data);
                    $response = [
                        'status' => true,
                        'message' => 'Berhasil menyimpan data',
                        'data' => []
                    ];
                    return $this->respond($response, 201);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal menyimpan data',
                        'data' => []
                    ];
                    return $this->respond($response, 422);
                }

            }
            else {  
                $response = [
                    'status' => false,
                    'message' => 'Metode tidak diizinkan',
                    'data' => []
                ];
                return $this->respond($response, 200);
                }
             /**$data = [
                    'category_id' => $this->request->getPost('category_id'),
                    'user_id' => $this->request->getPost('user_id'),
                    'title' => $this->request->getPost('title'),
                    'summary' => $this->request->getPost('summary'),
                    'body' => $this->request->getPost('body'),
                    'price' => $this->request->getPost('price'),
                    'unit' => $this->request->getPost('unit'),
                    'post_image' => $this->request->getPost('foto'),
                    'date' => $this->request->getPost('date'),
                    'time' => $this->request->getPost('time'),
                    'created_at' => date("Y-m-d H:i:s")
                ];

                if ($data > 0) {
                    $this->model->save($data);
                    $response = [
                        'status' => '201',
                        'data' => 'Success Post Data'
                    ];
                    return $this->respond($response, 201);
                } else {
                    $response = [
                        'status' => '422',
                        'data' => 'Failed Post Data'
                    ];
                    return $this->respond($response, 422);
                }
                **/
        }

    }

    public function update($id = null)
    {
        if ($this->request)
        {
            //get request from Reactjs
            if($this->request->getJSON()) {
                $input = $this->request->getJSON();
                $data = [
                    'title' => $input->title,
                    'summary' => $input->summary,
                    'body' => $input->body,
                    'price' => $input->price,
                    'unit' => $input->unit,
                    'date' => $input->date,
                    'time' => $input->time,
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                if ($data > 0) {
                    $this->model->update($input->id, $data);

                    $response = [
                        'status' => true,
                        'message' => 'Berhasil memperbarui data',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal memperbarui data',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                }
                
            } 
            else { 
                $response = [
                    'status' => false,
                    'message' => 'Metode tidak diizinkan',
                    'data' => []
                ];
                return $this->respond($response, 200);
                }
            /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'title' => $input['title'],
                    'summary' => $input['summary'],
                    'body' => $input['body'],
                    'price' => $input['price'],
                    'unit' => $input['unit'],
                    'date' => $input['date'],
                    'time' => $input['time'],
                    'updated_at' => date("Y-m-d H:i:s")
                ];
    
                if ($data > 0) {
                    $this->model->update($id, $data);
    
                    $response = [
                        'status' => '200',
                        'data' => 'Success Update data'
                    ];
                    return $this->respond($response, 200);
                } else {
                    $response = [
                        'status' => '404',
                        'data' => 'Failed Update Data'
                    ];
                    return $this->respond($response, 404);
                }      
            }**/
        }

    }

    public function delete($id = null)
    {
        $id = $this->model->find($id);
        if ($id) {
                $this->model->delete($id);
                $response = [
                    'status' => true,
                    'message' => 'Berhasil menghapus data',
                    'data' => []
                ];
                return $this->respond($response, 200);
        }  else {
                $response = [
                    'status' => false,
                    'message' => 'Gagal menghapus data',
                    'data' => []
                ];
                return $this->respond($response, 200);
        }  
    }
    
}