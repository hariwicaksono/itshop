<?php

namespace App\Modules\Order\Controllers;

use App\Controllers\BaseController;
use App\Modules\Order\Models\OrderModel;
use App\Modules\Product\Models\ProductModel;
use App\Modules\User\Models\UserModel;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Html2Pdf;
use CodeIgniter\I18n\Time;

class Order extends BaseController
{
    protected $product;
    protected $order;
    protected $user;

    public function __construct()
    {
        //memeriksa session role selain Admin redirect ke /
        if (session()->get('logged_in') == true && session()->get('role') == 2) {header('location:/');exit();}

        //memanggil Model
        $this->product = new ProductModel();
        $this->order = new OrderModel();
        $this->user = new UserModel();
    }

    public function index()
    {
        return view('App\Modules\Order\Views/order', [
            'title' => 'Pesanan',
            'hariini' => date('Y-m-d', strtotime(Time::now())),
			'tujuhHari' => date('Y-m-d', strtotime('-1 week', strtotime(Time::now()))),
			'awalBulan' => date('Y-m-', strtotime(Time::now())) . '01',
            'akhirBulan' => date('Y-m-t', strtotime(Time::now())),
			'awalTahun' => date('Y-', strtotime(Time::now())) . '01-01',
            'akhirTahun' => date('Y-', strtotime(Time::now())) . '12-31',
        ]);
    }
}