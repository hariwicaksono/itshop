<?php  
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
class Slideshow extends REST_Controller{

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
			$slide = $this->Model->get_slideshow();
		} else {
			$slide = $this->Model->get_slideshow($id);
		}

		if ($slide) {
			$this->response([
				'status' => 1,
				'data' => $slide
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Data Not Found'
			],REST_Controller::HTTP_NOT_FOUND);
		}

	} 

	public function index_post()
	{
		$data = [
			'img_slide' => $this->post('foto'),
			'text_slide' => $this->post('text_slide')
		];

		if ($this->Model->post_slideshow($data) > 0 ) {
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
			'text_slide' => $this->put('text_slide')
		];

		if ($this->Model->put_slideshow($id,$data) > 0) {
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
			if($this->Model->delete_slideshow($id) > 0){
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