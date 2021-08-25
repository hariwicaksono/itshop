<?php namespace App\Controllers;

use App\Models\SlideshowModel;
use \Appkita\CI4Restfull\RestfullApi;
use CodeIgniter\HTTP\IncomingRequest;

class Slideshow extends RestfullApi
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\SlideshowModel';
    protected $auth = ['key'];

	public function index()
	{
        $id=$this->request->getVar('id');

        if ($id == null) {
			$data = $this->model->getSlideshow();
		} else {
			$data = $this->model->getSlideshow($id);
		}
		
        if ($data) {
            $response = [
                'status' => true,
                'message' => 'Berhasil menampilkan semua data',
                'data' => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => []
            ];
            return $this->respond($response, 404);
        }
    }
    
    public function show($id = null)
    {
        $data = [
            'status' => true,
            'message' => 'Berhasil menampilkan data',
            'data' => $this->model->find($id)
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if ($this->request)
        {
            //get request from Vue Js
            if($this->request->getJSON()) {

                $post = $this->request->getJSON();
                $data = [
                    'img_slide' => $post->foto,
                    'text_slide' => $post->text_slide,
                    'created_at' => date("Y-m-d H:i:s")
                ];

                if ($data > 0) {
                    $this->model->save($data);
                    $response = [
                        'status' => true,
                        'message' => 'Berhasil menyimpan data',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal menyimpan data',
                        'data' => []
                    ];
                    return $this->respond($response, 200);
                }
            } /**else {
                $data = [
                    'img_slide' => $this->request->getPost('foto'),
                    'text_slide' => $this->request->getPost('text_slide'),
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
                }**/
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
                    'text_slide' => $input->text_slide,
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
                
            } /**else {
                //get request from PostMan and more
                $input = $this->request->getRawInput();
                $data = [
                    'text_slide' => $input['text_slide'],
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