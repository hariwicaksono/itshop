<?php

namespace App\Modules\Payment\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Payment\Models\PaymentConfirmModel;
use App\Modules\Payment\Models\PaymentModel;
use App\Libraries\Settings;
use App\Modules\Order\Models\OrderModel;
use App\Modules\Log\Models\LogModel;
use App\Modules\Tracking\Models\TrackingModel;
use CodeIgniter\I18n\Time;

class Payment extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = PaymentModel::class;
    protected $paymentconfirm;
    protected $setting;
    protected $order;
    protected $tracking;
    protected $log;

    public function __construct()
    {
        $this->paymentconfirm = new PaymentConfirmModel();
        $this->setting = new Settings();
        $this->order = new OrderModel();
        $this->tracking = new TrackingModel();
        $this->log = new LogModel();
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
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->paymentconfirm->where(['order_id' => $id])->findAll()], 200);
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
            $orderId = $json->order_id;
            $bank = $json->bank;
            $nama = $json->nama;
            $norekening = $json->norekening;
            $nominal = $json->nominal;
            $data = [
                'payment_id' => $json->payment_id,
                'order_id' => $orderId,
                'bank' => $bank,
                'nama' => $nama,
                'norekening' => $norekening,
                'nominal' => $nominal,
            ];
        } else {
            $input = $this->request->getPost();
            $orderId =  $input['order_id'];
            $bank =  $input['bank'];
            $nama =  $input['nama'];
            $norekening =  $input['norekening'];
            $nominal =  $input['nominal'];
            $data = [
                'payment_id' => $input['payment_id'],
                'order_id' => $orderId,
                'bank' => $bank,
                'nama' => $nama,
                'norekening' => $norekening,
                'nominal' => $nominal,
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

            $order = $this->order->find($orderId);
            $noOrder = $order['no_order'];

            //Simpan data Tracking
            $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Konfirmasi  pembayaran berhasil dikirimkan mohon tunggu verifikasi dari admin"]);
           
            //Simpan data Log
            $this->log->save(['keterangan' => session('first_name') . ' ' . session('last_name') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Create Konfirmasi Pembayaran Pesanan ID: ' . $orderId]);

            //Send Email New Confirm
            helper('email');
            $email = $this->setting->info['company_email2'];
            $dataEmail = [
                'no_order' => $noOrder,
                'created_at' => date('Y-m-d H:i:s'),
                'bank' => $bank,
                'nama' => $nama,
                'norekening' => $norekening,
                'nominal' => $nominal,
            ];
            sendEmail("Konfirmasi Transfer Manual Pesanan #$noOrder", $email, view('App\Modules\Payment\Views\email/payment_confirm', $dataEmail));

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }
}
