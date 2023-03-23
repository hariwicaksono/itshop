<?php

namespace App\Modules\Log\Controllers;

use App\Controllers\BaseController;
use App\Modules\Log\Models\LogModel;
use App\Modules\Log\Models\LogDataModel;
use CodeIgniter\I18n\Time;
use Config\Services;

class Log extends BaseController
{
	protected $log;

	public function __construct()
	{
		$this->log = new LogModel();
	}

	public function index()
	{
		return view('App\Modules\Log\Views/view_log', [
			'title' => 'Log Aktivitas',
		]);
	}

	public function listData()
	{
		$request = Services::request();
		$datamodel = new LogDataModel($request);
		if ($request->getMethod(true) == 'POST') {
			$lists = $datamodel->get_datatables();
			$csrfName = csrf_token();
			$csrfHash = csrf_hash();
			$data = [];
			$no = $request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];

				$row[] = '<div class="text-center">' . $list->id_log . '</div>';
				$row[] = $list->keterangan;
				$row[] = date('d/m/Y H:i', strtotime($list->created_at));
				$data[] = $row;
			}
			$output = [
				"draw" => $request->getPost('draw'),
				"recordsTotal" => $datamodel->count_all(),
				"recordsFiltered" => $datamodel->count_filtered(),
				"data" => $data
			];
			$output[$csrfName] = $csrfHash;
			echo json_encode($output);
		}
	}

	public function refresh()
	{
		if ($this->request->isAjax()) {
			$csrfName = csrf_token();
			$csrfHash = csrf_hash();

			$msg = [
				'data' => view('App\Modules\Log\Views/data_log', ['title' => 'Log Aktivitas', 'csrf_token' => $csrfHash])
			];

			return $this->response->setJSON($msg);
		} else {
			return redirect()->back();
		}
	}

	//--------------------------------------------------------------------

}
