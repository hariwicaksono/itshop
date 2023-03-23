<?php

namespace App\Modules\Page\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Page\Models\PageModel;

class Page extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = PageModel::class;

    public function __construct()
	{
		//memanggil Model
	}

    public function index()
    {
        $data = $this->model->findAll();
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

    public function show($slug = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->where('slug', $slug)->first()], 200);
    }

    public function update($id = NULL)
    {
        $rules = [
            'page_body' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'page_title' => $json->page_title,
                'page_title_en' => $json->page_title_en,
                'page_body' => $json->page_body,
                'page_body_en' => $json->page_body_en,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'page_title' => $input['page_title'],
                'page_title_en' => $input['page_title_en'],
                'page_body' =>  $input['page_body'],
                'page_body_en' =>  $input['page_body_en'],
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
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
                'message' => lang('App.updSuccess'),
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
