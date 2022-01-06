<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Html2Pdf;

class Admin extends BaseController
{
    protected $productModel;
    protected $orderModel;
    protected $userModel;

    public function __construct()
    {
        //memeriksa session role selain Admin redirect ke /
        if (session()->get('logged_in') == true && session()->get('role') == 2) {header('location:/');exit();}

        //memanggil Model
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        //memanggil function di model
        $product= $this->productModel->countProduct(1);
        $order = $this->orderModel->countAllResults();
        $user = $this->userModel->countAllResults();

        return view('admin/index', [
            'jmlProduct' => $product,
            'jmlUser' => $user,
            'jmlOrder' => $order,
         ]);
    }

    public function product()
    {
        return view('admin/product');
    }

    public function order()
    {
        return view('admin/order');
    }

    public function payment()
    {
        return view('admin/payment');
    }

    public function shipment()
    {
        return view('admin/shipment');
    }

    public function setting()
    {
        return view('admin/setting');
    }

    public function export()
    {
        return view('admin/export');
    }

    public function exportTcpdf()
    {
        //mengambil result array productModel
        $data = [
            'product' => $this->productModel->findAll()
        ];
        $html = view('admin/export_tcpdf', $data);
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('products.pdf','I');  // display on the browser
    }

    public function exportMpdf()
    {
        //mengambil result array productModel
        $data = [
            'product' => $this->productModel->findAll()
        ];

        $html = view('admin/export_mpdf', $data);

        // create new PDF document
        $pdf = new \Mpdf\Mpdf();

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('products.pdf', 'I');
    }

    public function exportHtml2pdf()
    {
        //mengambil result array productModel
        $data = [
            'product' => $this->productModel->findAll()
        ];

        $html = view('admin/export_html2pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('P', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $file = $_SERVER['DOCUMENT_ROOT'].'files/products.pdf';
        $pdf->Output($file, 'F');
        $attachment = base_url('files/products.pdf');
        if (file_exists($file)) {
            helper('email');
            sendEmailAttachment("Invoice PDF", "hariwicaksono87@gmail.com", "This is test", $attachment);
        }
        $pdf->Output('products.pdf','I');  // display on the browser
    }

    public function exportExcel()
    {
        $product = $this->productModel->findAll();

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

    
    public function user()
    {
        return view('admin/user');
    }
    
}
