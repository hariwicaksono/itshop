<?php  
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Comments extends REST_Controller{

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
		$id = $this->get('id');
		if ($id == null) {
			$comment = $this->Model->get_comment();
		} else {
			$comment = $this->Model->get_comment($id);
		}

		if ($comment) {
			$this->response([
				'status' => '1',
				'data' => $comment,
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
			'post_id' => $this->post('post_id'),
			'name' => $this->post('name'),
			'email' => $this->post('email'),
			'body' => $this->post('body'),
			'created_at' => date("Y-m-d H:i:s"),
			'active' => ''
		];

		if ($this->Model->post_comment($data) > 0 ) {
			$this->response([
				'status' => 1,
				'data' => 'Success Post data'
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
			'active' => $this->put('active'),
			'updated_at' => date("Y-m-d H:i:s")
		];

		if ($this->Model->put_comment($id,$data) > 0) {
			$this->response([
				'status' => 1,
				'data' => 'Success Update data'
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Failed Update Data'
			],REST_Controller::HTTP_NOT_FOUND);
		}

	}

}