<?php  
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Settings extends REST_Controller{

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
		$id = '1';
		$setting = $this->Model->get_setting($id);
		

		if ($setting) {
			$this->response([
				'status' => 1,
				'data' => $setting
			],REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => 0,
				'data' => 'Data Not Found'
			],REST_Controller::HTTP_NOT_FOUND);
		}
		 

	} 

	public function index_put()
	{
		$id = $this->put('id');
		$data = [
			'company' => $this->put('company'),
			'website' => $this->put('website'),
			'phone' => $this->put('phone'),
			'email' => $this->put('email'),
		];

		if ($this->Model->put_setting($id,$data) > 0) {
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

}