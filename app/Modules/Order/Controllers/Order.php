<?php

namespace App\Modules\Order\Controllers;

use App\Controllers\BaseController;
use App\Modules\Order\Models\OrderModel;
use App\Modules\Product\Models\ProductModel;
use App\Models\UserModel;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Html2Pdf;

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
            'title' => 'Order'
        ]);
    }
}