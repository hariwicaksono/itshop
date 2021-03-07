<?php  
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class UserController extends REST_Controller{
	
	public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('MasterModel','Model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
    }

	public function index_get()
	{
		$id = $this->get('id');
		if ($id == null) {
			$user = $this->Model->get_user();
		} else {
			$user = $this->Model->get_user($id);
		}
 
		if ($user) {
			$this->response([
				'status' => 1,
				'data' => $user
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'NOT FOUND'
			],REST_Controller::HTTP_NOT_FOUND);
		}

	}


	public function index_post()
	{
		$data = [
			'email' => $this->post('email'),
			'username' => $this->post('username'),
			'password' => $this->post('password'),
			'name' => $this->post('name'),
			'status_user' => $this->post('status_user'),
			'status_active' =>$this->post('status_active'),
			'created_at' =>date("Y-m-d H:i:s")
		];

		if ($this->Model->post_user($data) > 0) {
			$this->response([
				'status' => 1,
				'data' => 'Success Post Data'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Failed Post'
			],REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function index_delete()
	{
		$id = $_GET['id'];
		if ($id == null) {
			$this->response([
				'status' => 0,
				'data' => 'Id Null'
			],REST_Controller::HTTP_BAD_REQUEST);
		} else {
			if ($this->Model->delete_user($id)) {
				$this->response([
					'status' => 1,
					'data' => 'Success Delete'
				],REST_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => 0,
					'data' => 'Failed DELETE'
				],REST_Controller::HTTP_NOT_FOUND);
			}
		}
	}

	public function index_put()
	{
		$id = $this->put('id');
		$data = [
			'email' => $this->put('email'),
			'name' => $this->put('name'),
			'updated_at' => date("Y-m-d H:i:s")
		];

		

		if ($this->Model->put_user($id,$data) > 0 ) {
			$this->response([
				'status' => 1,
				'data' => 'Success Update Data'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Failed Update'
			],REST_Controller::HTTP_NOT_FOUND);
		}

	}


}