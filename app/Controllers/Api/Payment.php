<?php

namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\PaymentConfirmModel;
use App\Models\PaymentModel;

class Payment extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = PaymentModel::class;
    protected $paymentconfirm;

    public function __construct()
    {
        $this->paymentconfirm = new PaymentConfirmModel();
    }

    public function index()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->where(['active' => 1])->findAll()], 200);
    }

    public function all()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->findAll()], 200);
    }

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->find($id)], 200);
    }

    public function create()
    {
        $rules = [
            'payment' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'payment' => $json->payment,
                'account' => $json->account,
                'number' => $json->number,
                'active' => 1
            ];
        } else {
            $data = [
                'payment' => $this->request->getPost('payment'),
                'account' => $this->request->getPost('account'),
                'number' => $this->request->getPost('number'),
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
                'message' => lang('App.saveSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'payment' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'payment' => $json->payment,
                'account' => $json->account,
                'number' => $json->number,
            ];
        } else {
            $data = $this->request->getRawInput();
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $simpan = $this->model->update($id, $data);
            if ($simpan) {
                $response = [
                    'status' => true,
                    'message' => lang('App.updSuccess'),
                    'data' => [],
                ];
                return $this->respond($response, 200);
            }
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);
        if ($hapus) {
            $this->model->delete($id);
            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
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

    public function setCod($id = NULL)
    {

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'cod' => $json->cod,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'cod' => $input['cod'],
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

    public function getConfirm($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->paymentconfirm->where(['order_id' => $id])->first()], 200);
    }

    public function confirm()
    {
        $rules = [
            'order_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'bank' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'norekening' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nominal' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'payment_id' => $json->payment_id,
                'order_id' => $json->order_id,
                'bank' => $json->bank,
                'nama' => $json->nama,
                'norekening' => $json->norekening,
                'nominal' => $json->nominal,
            ];
        } else {
            $data = [
                'payment_id' => $this->request->getPost('payment_id'),
                'order_id' => $this->request->getPost('order_id'),
                'bank' => $this->request->getPost('bank'),
                'nama' => $this->request->getPost('nama'),
                'norekening' => $this->request->getPost('norekening'),
                'nominal' => $this->request->getPost('nominal'),
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
            $this->paymentconfirm->save($data);
            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }
}
