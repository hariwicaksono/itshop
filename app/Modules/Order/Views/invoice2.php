<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice <?= $order['no_order'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border-radius: 5px;
            color: #999;
            font-size: 11px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 12px;
            color: #555;
            line-height: 1.8;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-meta {
            font-size: 12px;
            color: #555;
            line-height: 1.8;
        }

        .invoice-meta strong {
            color: #333;
            display: inline-block;
            width: 100px;
        }

        /* Status Badge */
        .status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 10px;
            letter-spacing: 1px;
        }

        .status.lunas {
            background: #28a745;
            color: #fff;
        }

        .status.pending {
            background: #ffc107;
            color: #000;
        }

        .status.failed {
            background: #dc3545;
            color: #fff;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin: 25px 0;
        }

        .info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }

        .info-box h3 {
            font-size: 14px;
            color: #007bff;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 13px;
            color: #333;
        }

        .info-box strong {
            color: #555;
            display: inline-block;
            min-width: 80px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }

        thead {
            background: #007bff;
            color: #fff;
        }

        th {
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 10px 8px;
            text-align: left;
            font-size: 13px;
            border-bottom: 1px solid #e0e0e0;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        tfoot {
            background: #f8f9fa;
        }

        tfoot td {
            padding: 12px 8px;
            font-weight: 600;
            border-bottom: none;
        }

        .total-row {
            background: #007bff !important;
            color: #fff;
            font-size: 14px;
        }

        .total-row td {
            font-weight: bold;
            color: #fff;
        }

        /* Tax Section */
        .tax-section {
            display: table;
            width: 100%;
            margin: 15px 0;
        }

        .tax-section .tax-details {
            display: table-cell;
            width: 50%;
        }

        .tax-section .tax-summary {
            display: table-cell;
            width: 50%;
            text-align: right;
        }

        .tax-table {
            width: 100%;
            max-width: 300px;
            margin-left: auto;
        }

        .tax-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e0e0e0;
        }

        .tax-table .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            background: #f8f9fa;
        }

        /* Notes Section */
        .note {
            margin: 20px 0;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
        }

        .note strong {
            color: #856404;
        }

        .note p {
            margin: 5px 0;
            color: #856404;
        }

        /* Payment Info */
        .payment-info {
            margin: 25px 0;
            padding: 20px;
            border: 2px solid #007bff;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .payment-info h4 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #007bff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .payment-details {
            display: table;
            width: 100%;
        }

        .payment-details p {
            margin: 8px 0;
            font-size: 13px;
        }

        .payment-details strong {
            color: #555;
            display: inline-block;
            min-width: 120px;
        }

        .payment-details hr {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 10px 0;
        }

        .small-note {
            font-size: 11px;
            color: #666;
            margin-top: 10px;
            font-style: italic;
        }

        /* Terms Section */
        .terms {
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
            color: #555;
        }

        .terms h4 {
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
        }

        .terms ol {
            margin-left: 20px;
        }

        .terms li {
            margin: 5px 0;
        }

        /* Signature Section */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
            padding-top: 20px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin: 60px auto 10px;
        }

        .signature-label {
            font-size: 12px;
            color: #555;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #007bff;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer strong {
            color: #555;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                max-width: 100%;
                padding: 20px;
            }

            .no-print {
                display: none;
            }
        }

        /* Utility Classes */
        .text-bold {
            font-weight: bold;
        }

        .text-primary {
            color: #007bff;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-logo">LOGO</div>
                <div class="company-name"><?= $company['company_name'] ?? 'ITSHOP.biz.id' ?></div>
                <div class="company-details">
                    <p>Hari Wicaksono, S.Kom</p>
                    <p>NIB: 0292012072068</p>
                    <p><?= $company['company_alamat'] ?? 'Alamat Toko' ?></p>
                    <p>Web: https://itshop.biz.id</p>
                    <p>Telp: <?= $company['company_telepon'] ?? '-' ?> | Email: <?= $company['company_email1'] ?? '-' ?></p>
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <p><strong>No. Invoice:</strong> <?= $order['no_order'] ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
                    <p><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($order['created_at'] . ' +7 days')) ?></p>
                    <?php if ($order['status_payment'] == 'settlement'): ?>
                        <span class="status lunas">LUNAS</span>
                    <?php elseif ($order['status_payment'] == 'pending'): ?>
                        <span class="status pending">MENUNGGU PEMBAYARAN</span>
                    <?php else: ?>
                        <span class="status failed">GAGAL</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Customer & Company Info -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Dari:</h3>
                    <p><strong>Perusahaan:</strong> <?= $company['company_name'] ?? 'ITSHOP.biz.id' ?></p>
                    <p><strong>Alamat:</strong> <?= $company['company_alamat'] ?? '-' ?></p>
                    <p><strong>Telepon:</strong> <?= $company['company_telepon'] ?? '-' ?></p>
                    <p><strong>Email:</strong> <?= $company['company_email1'] ?? '-' ?></p>
                </div>
            </div>
            <div class="info-right">
                <div class="info-box">
                    <h3>Kepada:</h3>
                    <p><strong>Nama:</strong> <?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?></p>
                    <?php if (!empty($user['company'])): ?>
                        <p><strong>Perusahaan:</strong> <?= esc($user['company']) ?></p>
                    <?php endif; ?>
                    <p><strong>Email:</strong> <?= $user['email'] ?></p>
                    <p><strong>Telepon:</strong> <?= $user['phone'] ?? '-' ?></p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Produk</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 20%; text-align: right;">Harga Satuan</th>
                    <th style="width: 20%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $grandtotal = 0;
                foreach ($cart as $item):
                    $subtotal = $item['qty'] * $item['price'];
                    $grandtotal += $subtotal;
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $item['product_name'] ?></td>
                        <td class="text-center"><?= $item['qty'] ?></td>
                        <td class="text-right">Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="text-right">Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tax and Total -->
        <div class="tax-section">
            <div class="tax-details">
                <!-- Empty for layout balance -->
            </div>
            <div class="tax-summary">
                <table class="tax-table">
                    <tr>
                        <td style="text-align: right;">Subtotal:</td>
                        <td style="text-align: right; width: 150px;">Rp<?= number_format($grandtotal, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">PPN (11%):</td>
                        <td style="text-align: right;">Rp<?= number_format($grandtotal * 0.11, 0, ',', '.') ?></td>
                    </tr>
                    <tr class="grand-total">
                        <td style="text-align: right;">Total:</td>
                        <td style="text-align: right;">Rp<?= number_format($grandtotal * 1.11, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <?php if (!empty($order['note']) && $order['note'] != '-'): ?>
            <div class="note">
                <strong>Catatan:</strong>
                <p><?= esc($order['note']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Payment Information -->
        <div class="payment-info">
            <h4>Informasi Pembayaran</h4>
            <div class="payment-details">
                <?php if ($order['status'] == 0): ?>
                    <p><strong>Status:</strong> <span style="color: #ffc107; font-weight: bold;">Menunggu Pembayaran</span></p>
                    <p>Silahkan melakukan transfer pembayaran ke rekening dibawah ini:</p>
                    <?php foreach ($payment as $row): ?>
                        <p><strong>Bank:</strong> <?= $row['payment'] ?? '-' ?></p>
                        <p><strong>No. Rekening:</strong> <?= $row['number'] ?? '-' ?></p>
                        <p><strong>Atas Nama:</strong> <?= $row['account'] ?? '-' ?></p>
                        <hr />
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Sudah Dibayar</span></p>
                    <p><strong>Metode:</strong> <?= $order['payment_name'] ?? '-' ?></p>
                    <p><strong>No. Rekening:</strong> <?= $order['number'] ?? '-' ?></p>
                    <p><strong>Atas Nama:</strong> <?= $order['account'] ?? '-' ?></p>
                <?php endif; ?>
                <p class="small-note">* Mohon abaikan informasi pembayaran jika invoice sudah Lunas.</p>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms">
            <h4>Syarat & Ketentuan:</h4>
            <ol>
                <li>Pembayaran harus dilakukan sesuai dengan jumlah yang tercantum dalam invoice.</li>
                <li>Invoice ini sah dan dihasilkan oleh sistem, tidak memerlukan tanda tangan atau cap basah.</li>
                <li>Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian tertulis.</li>
                <li>Keluhan harus disampaikan maksimal 7 hari setelah penerimaan barang.</li>
                <li>Untuk pertanyaan lebih lanjut, hubungi kami di support@itshop.biz.id</li>
            </ol>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Penerima</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Hari Wicaksono, S.Kom</div>
                <div style="font-size: 11px; color: #777; margin-top: 5px;">Pemilik</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Invoice ini sah dan dihasilkan oleh sistem, tidak memerlukan tanda tangan atau cap basah.</strong></p>
            <p>Dicetak pada: <?= date('d F Y H:i:s') ?> WIB | Dokumen ini dicetak secara elektronik</p>
            <p style="margin-top: 10px; font-size: 10px;">© <?= date('Y') ?> ITSHOP.biz.id - All Rights Reserved</p>
        </div>
    </div>

</body>

</html>