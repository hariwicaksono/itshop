<?php

namespace App\Controllers;

use App\Modules\Order\Models\OrderModel;
use App\Modules\Product\Models\ProductModel;
use App\Modules\User\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Admin extends BaseController
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
        //memanggil function di model
        $product= $this->product->countProduct(1);
        $order = $this->order->countAllResults();
        $user = $this->user->countAllResults();

        $jam = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '00'];
        $data['jam'] = [];
        foreach ($jam as $j) {
            $date = date('Y-m-d') . ' ' . $j;
            $harian[] = $this->order->chartHarian($date);
        }

        $bln = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $data['transaksi'] = [];
        foreach ($bln as $b) {
            $date = date('Y') . '-' . $b;
            $transaksi[] = $this->order->chartTransaksi($date);
        }

        return view('admin/index', [
            'title' => 'Dashboard',
            'jmlProduct' => $product,
            'jmlUser' => $user,
            'jmlOrder' => $order,
            'harian' => $harian,
            'transaksi' => $transaksi
         ]);
    }

    public function exportExcel()
    {
        $product = $this->product->findAll();

        $spreadsheet = new Spreadsheet();
        // tulis header/nama kolom 
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Produk')
            ->setCellValue('C1', 'Harga')
            ->setCellValue('D1', 'Deskripsi Produk')
            ->setCellValue('E1', 'Aktif');

        $column = 2;
        // tulis data ke cell
        $no = 1;
        foreach ($product as $data) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column, $data['product_name'])
                ->setCellValue('C' . $column, $data['product_price'])
                ->setCellValue('D' . $column, $data['product_description'])
                ->setCellValue('E' . $column, $data['active']);
            $column++;
        }
        // tulis dalam format .xlsx
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Semua Produk';

        // Redirect hasil generate xlsx ke web client
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
    
}
