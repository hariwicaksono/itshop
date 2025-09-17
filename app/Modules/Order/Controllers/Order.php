<?php

namespace App\Modules\Order\Controllers;

use App\Controllers\BaseController;
use App\Modules\Order\Models\OrderModel;
use App\Modules\Product\Models\ProductModel;
use App\Modules\User\Models\UserModel;
use App\Modules\Cart\Models\CartModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\I18n\Time;
use App\Libraries\Settings;
use CodeIgniter\Exceptions\PageNotFoundException;

class Order extends BaseController
{
    protected $setting;
    protected $product;
    protected $order;
    protected $user;
    protected $cart;

    public function __construct()
    {
        //memeriksa session role selain Admin redirect ke /
        if (session()->get('logged_in') == true && session()->get('role') == 2) {
            header('location:/');
            exit();
        }

        //memanggil Model
        $this->setting = new Settings();
        $this->product = new ProductModel();
        $this->order = new OrderModel();
        $this->user = new UserModel();
        $this->cart = new CartModel();
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

    public function invoice($nomor = null)
    {
        $order = $this->order->showOrderNumber($nomor);

        if (!$order) {
            throw PageNotFoundException::forPageNotFound("Order dengan ID $nomor tidak ditemukan.");
        }

        $user = $this->user->find($order['user_id']);
        $cart = $this->cart
            ->select('carts.*, products.product_name')
            ->join('products', 'products.product_id = carts.product_id')
            ->where('carts.order_id', $order['order_id'])
            ->findAll();

        $data = [
            'order' => $order,
            'user'  => $user,
            'cart'  => $cart,
            'company' => $this->setting->info, // ambil info perusahaan
        ];

        // Load view invoice (HTML)
        $html = view('App\Modules\Order\Views\invoice', $data);

        // Generate PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Bersihkan output buffer sebelum kirim PDF
        if (ob_get_length()) ob_end_clean();

        // Stream ke browser
        $dompdf->stream("invoice-{$order['no_order']}.pdf", ["Attachment" => false]);
        exit;
    }
}
