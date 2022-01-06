<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h2 class="mb-2">Export PDF</h2>

<v-card class="mb-3" outlined elevation="1">
    <v-card-title>TCPDF</v-card-title>
    <v-card-text>
        TCPDF adalah kelas PHP perangkat lunak sumber bebas dan terbuka untuk menghasilkan dokumen PDF. TCPDF adalah satu-satunya pustaka berbasis PHP yang menyertakan dukungan lengkap untuk UTF-8 Unicode dan bahasa kanan-ke-kiri, termasuk algoritma dua arah.
    </v-card-text>
    <v-card-actions>
        <v-btn color="primary" href="<?= base_url('admin/export-tcpdf') ?>">
            Lihat PDF
        </v-btn>
        <v-spacer></v-spacer>

        <v-btn text @click="show = !show">
            Lihat Kode <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
        </v-btn>
    </v-card-actions>
    <v-expand-transition>
        <div v-show="show">
            <v-divider></v-divider>

            <v-card-text>
                PHP Controller<br />
                <pre>
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
                        </pre>
                <hr />
                HTML View<br />
                <pre>
                        &lt;!doctype html&gt;
                        &lt;html lang="en"&gt;

                        &lt;head&gt;
                            &lt;!-- Required meta tags --&gt;
                            &lt;meta charset="utf-8"&gt;
                            &lt;meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"&gt;
                            &lt;link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet"&gt;  
                            &lt;title&gt;Print PDF&lt;/title&gt;
                            &lt;style&gt;
                                @media print {
                                    .table {
                                        font-family: sans-serif;
                                        color: #232323;
                                        border-collapse: collapse;
                                    }

                                    .table,
                                    th,
                                    td {
                                        border: 1px solid #BDBDBD;
                                    }
                                }
                            &lt;/style&gt;
                        &lt;/head&gt;

                        &lt;body&gt;

                            &lt;div class="container mt-5"&gt;
                                &lt;h1 align="center"&gt;Sample TCPDF&lt;/h1&gt;
                                &lt;table class="table"&gt;
                                    &lt;tr&gt;
                                        &lt;th&gt;No&lt;/th&gt;
                                        &lt;th&gt;Nama Produk&lt;/th&gt;
                                        &lt;th&gt;Harga&lt;/th&gt;
                                        &lt;th&gt;Deskripsi Produk&lt;/th&gt;
                                        &lt;th&gt;Aktif&lt;/th&gt;
                                    &lt;/tr&gt;
                                    &lt;?php $no = 1; ?&gt;
                                    &lt;?php foreach ($product as $row) : ?&gt;
                                        &lt;tr&gt;
                                            &lt;td&gt;&lt;?= $no++; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;&lt;?= $row['product_name']; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;Rp.&lt;?= $row['product_price']; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;&lt;?= $row['product_description']; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;&lt;?= $row['active']; ?&gt;&lt;/td&gt;
                                        &lt;/tr&gt;
                                    &lt;?php endforeach; ?&gt;
                                &lt;/table&gt;
                            &lt;/div&gt;

                        &lt;/body&gt;

                        &lt;/html&gt;
                        </pre>
            </v-card-text>
        </div>
    </v-expand-transition>
</v-card>

<v-card class="mb-3" outlined elevation="1">
    <v-card-title>mPDF</v-card-title>
    <v-card-text>
        mPDF is a PHP library which generates PDF files from UTF-8 encoded HTML.

        It is based on FPDF and HTML2FPDF with a number of enhancements.

        The original author, Ian Back, wrote mPDF to output PDF files ‘on-the-fly’ from his website, handling different languages.
    </v-card-text>
    <v-card-actions>
        <v-btn color="primary" href="<?= base_url('admin/export-mpdf') ?>">
            Lihat PDF
        </v-btn>
        <v-spacer></v-spacer>

        <v-btn text @click="show1 = !show1">
            Lihat Kode <v-icon>{{ show1 ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
        </v-btn>
    </v-card-actions>
    <v-expand-transition>
        <div v-show="show1">
            <v-divider></v-divider>

            <v-card-text>
                PHP Controller<br />
                <pre>
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
                </pre>
                <hr />
                HTML View<br />
                <pre>
                &lt;!doctype html&gt;
                    &lt;html lang="en"&gt;

                    &lt;head&gt;
                        &lt;!-- Required meta tags --&gt;
                        &lt;meta charset="utf-8"&gt;
                        &lt;meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"&gt;
                        &lt;link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"&gt;
                        &lt;title&gt;Print PDF&lt;/title&gt;
                        &lt;style&gt;
                            table {
                                font-family: sans-serif;
                                border: 1px solid #424242;
                                border-collapse: collapse;
                            }

                            th {
                                background-color: #04AA6D;
                                color: white;
                                padding: 5px;
                            }

                            tr,
                            td {
                                border-bottom: 1px solid #ddd;
                                padding: 10px;
                                text-align: left;
                            }
                        &lt;/style&gt;
                    &lt;/head&gt;

                    &lt;body&gt;

                        &lt;div class="container mt-5"&gt;
                            &lt;h1 align="center" style="font-size: 24px;font-weight: bold"&gt;Sample mPDF&lt;/h1&gt;
                            &lt;table class="table table-striped"&gt;
                                &lt;thead&gt;
                                    &lt;tr&gt;
                                        &lt;th scope="col"&gt;#&lt;/th&gt;
                                        &lt;th scope="col"&gt;Nama Produk&lt;/th&gt;
                                        &lt;th scope="col"&gt;Harga&lt;/th&gt;
                                        &lt;th scope="col"&gt;Deskripsi Produk&lt;/th&gt;
                                        &lt;th scope="col"&gt;Aktif&lt;/th&gt;
                                    &lt;/tr&gt;
                                &lt;/thead&gt;
                                &lt;tbody&gt;
                                    &lt;?php $no = 1; ?&gt;
                                    &lt;?php foreach ($product as $row) : ?&gt;
                                        &lt;tr&gt;
                                            &lt;td&gt;&lt;?= $no++; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;&lt;?= $row['product_name']; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;Rp.&lt;?= $row['product_price']; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;&lt;?= $row['product_description']; ?&gt;&lt;/td&gt;
                                            &lt;td&gt;&lt;?= $row['active']; ?&gt;&lt;/td&gt;
                                        &lt;/tr&gt;
                                    &lt;?php endforeach; ?&gt;
                                &lt;/tbody&gt;
                            &lt;/table&gt;
                        &lt;/div&gt;

                    &lt;/body&gt;

                    &lt;/html&gt;
                </pre>
            </v-card-text>
        </div>
    </v-expand-transition>
</v-card>

<v-card class="mb-3" outlined elevation="1">
    <v-card-title>HTML2PDF</v-card-title>
    <v-card-text>
        Html2Pdf is a HTML to PDF converter written in PHP, and compatible with PHP 5.6 to 7.4. It allows the conversion of valid HTML in PDF format, to generate documents like invoices, documentation. Specific tags have been implemented, to adapt the html standard to a PDF usage
    </v-card-text>
    <v-card-actions>
        <v-btn color="primary" href="<?= base_url('admin/export-html2pdf') ?>">
            <v-icon>mdi-send</v-icon> Lihat &amp; Kirim PDF
        </v-btn>
        <v-spacer></v-spacer>

        <v-btn text @click="show2 = !show2">
            Lihat Kode <v-icon>{{ show2 ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
        </v-btn>
    </v-card-actions>
    <v-expand-transition>
        <div v-show="show2">
            <v-divider></v-divider>

            <v-card-text>
                PHP Controller<br />
                <pre>
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
                    </pre>
                <hr />
                HTML View<br />
                <pre>
                &lt;!doctype html&gt;
                    &lt;html lang="en"&gt;

                    &lt;head&gt;
                        &lt;!-- Required meta tags --&gt;
                        &lt;meta charset="utf-8"&gt;
                        &lt;meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"&gt;
                        &lt;link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"&gt;
                         
                        &lt;title&gt;Print PDF&lt;/title&gt;
                        &lt;style&gt;
                            table {
                                border: 1px solid #424242;
                                border-collapse: collapse;
                                padding: 0 20px;
                            }

                            th {
                                background-color: #04AA6D;
                                color: white;
                                padding: 5px;
                            }

                            tr,
                            td {
                                border-bottom: 1px solid #ddd;
                                padding: 10px;
                                text-align: left;
                            }
                        &lt;/style&gt;
                    &lt;/head&gt;

                    &lt;body&gt;

                        &lt;div class="container mt-5"&gt;
                            &lt;h1 align="center"&gt;Sample HTML2PDF&lt;/h1&gt;
                            &lt;table class="table table-bordered"&gt;
                                &lt;thead&gt;
                                    &lt;tr&gt;
                                        &lt;th scope="col"&gt;No&lt;/th&gt;
                                        &lt;th scope="col"&gt;Nama Produk&lt;/th&gt;
                                        &lt;th scope="col"&gt;Harga&lt;/th&gt;
                                        &lt;th scope="col"&gt;Deskripsi Produk&lt;/th&gt;
                                        &lt;th scope="col"&gt;Aktif&lt;/th&gt;
                                    &lt;/tr&gt;
                                &lt;/thead&gt;
                                &lt;tbody&gt;
                                &lt;?php $no = 1; ?&gt;
                                &lt;?php foreach ($product as $row) : ?&gt;
                                    &lt;tr&gt;
                                        &lt;td&gt;&lt;?= $no++; ?&gt;&lt;/td&gt;
                                        &lt;td width="400"&gt;&lt;?= $row['product_name']; ?&gt;&lt;/td&gt;
                                        &lt;td&gt;Rp.&lt;?= $row['product_price']; ?&gt;&lt;/td&gt;
                                        &lt;td&gt;&lt;?= $row['product_description']; ?&gt;&lt;/td&gt;
                                        &lt;td&gt;&lt;?= $row['active']; ?&gt;&lt;/td&gt;
                                    &lt;/tr&gt;
                                &lt;?php endforeach; ?&gt;
                                &lt;/tbody&gt;
                            &lt;/table&gt;
                        &lt;/div&gt;

                    &lt;/body&gt;

                    &lt;/html&gt;
                </pre>
            </v-card-text>
        </div>
    </v-expand-transition>
</v-card>

<?php $this->endSection("content") ?>

<?php $this->section("js") ?> 
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    dataVue = {
        ...dataVue,

    }
    createdVue = function() {

    }

    methodsVue = {
        ...methodsVue,

    }
</script>
<?php $this->endSection("js") ?>