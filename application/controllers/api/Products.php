<?php  
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
class Products extends REST_Controller{

	public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('MasterModel','Model');
        header('Access-Control-Allow-Origin: *');
       header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	   header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT ,DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
		}
		
    }

	public function index_get()
	{
		$count = $this->Model->count_product();
		$id = $this->get('id');
		if ($id == null) {
			$posts = $this->Model->get_product();
		} else {
			$posts = $this->Model->get_product($id);
		}

		if ($posts) {
			$this->response([
				'status' => '1',
				'data' => $posts,
				'allCount' => $count
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => '0',
				'data' => 'Data Not Found'
			],REST_Controller::HTTP_NOT_FOUND);
		}
		 

	} 

	public function index_post()
	{
		$data = [
			'category_id' => $this->post('category_id'),
			'user_id' => $this->post('user_id'),
			'title' => $this->post('title'),
			'summary' => $this->post('summary'),
			'body' => $this->post('body'),
			'post_image' => $this->post('foto'),
			'date' => $this->post('date'),
			'time' => $this->post('time'),
			'created_at' => date("Y-m-d H:i:s")
		];

		if ($this->Model->post_blog($data) > 0 ) {
			$this->response([
				'status' => 1,
				'data' => 'Succes Post data'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Failed Post Data'
			],REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function index_put()
	{
		$id = $this->put('id');
		$data = [
			'title' => $this->put('title'),
			'summary' => $this->put('summary'),
			'body' => $this->put('body'),
			'date' => $this->put('date'),
			'time' => $this->put('time'),
			'updated_at' => date("Y-m-d H:i:s")
		];

		if ($this->Model->put_blog($id,$data) > 0) {
			$this->response([
				'status' => 1,
				'data' => 'Succes Update data'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Failed Update Data'
			],REST_Controller::HTTP_NOT_FOUND);
		}

	}

	public function index_delete()
	{
		$id = $_GET['id'];
		if ($id == null) {
			$this->response([
				'status' => 404,
				'data' => 'id_not found'
			],REST_Controller::HTTP_BAD_REQUEST);
		} else {
			if($this->Model->delete_produk($id) > 0){
					$this->response([
					'status' => 1,
					'data' => 'Succes Delete data'
				],REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => 0,
					'data' => 'Failed Delete Data'
				],REST_Controller::HTTP_NOT_FOUND);
			}
		} 
	}

}