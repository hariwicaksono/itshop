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
            padding: 30px;
            background: #fff;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 10px;
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

        .company-tagline {
            font-size: 13px;
            color: #666;
            font-style: italic;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ddd;
        }

        .company-details {
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .invoice-meta {
            font-size: 14px;
            color: #444;
            line-height: 1.8;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            display: block;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 60px;
            height: 60px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            color: #999;
            font-size: 10px;
        }

        hr {
            border: 0;
            border-top: 1px solid #999;
            margin-top: 12px;
        }

        h3 {
            margin-top: 0;
            font-size: 16px;
            color: #444;
        }

        /* Status Badge */
        .status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            padding-bottom: 10px;
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
            margin: 15px 0;
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
            font-size: 14px;
            color: #333;
        }

        .info-box strong {
            color: #555;
            min-width: 80px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        thead {
            background: #007bff;
            color: #fff;
        }

        th {
            padding: 10px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 8px 5px;
            text-align: left;
            font-size: 14px;
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

        /* Tax Section */
        .tax-section {
            display: table;
            width: 100%;
            margin: 0;
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
            font-weight: bold;
            background: #f8f9fa;
        }

        /* Notes Section */
        .note {
            margin-top: 20px;
            padding: 10px;
            background: #f8f8f8;
            border-left: 3px solid #007bff;
            font-size: 14px;
        }

        /* Payment Info */
        .payment-info {
            margin: 14px 0;
            padding: 14px;
            border: 2px solid #007bff;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .payment-info h4 {
            margin: 0;
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

        .footer {
            font-size: 12px;
            color: #444;
            text-align: center;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-left">
            <!-- <?php
                    $logoFile = $company['img_logo'] ?? '';
                    if (!empty($logoFile) && file_exists(FCPATH . 'images/' . $logoFile)):
                        $logoPath = FCPATH . 'images/' . $logoFile;
                        $logoData = base64_encode(file_get_contents($logoPath));
                        $logoMime = getimagesize($logoPath)['mime'] ?? 'image/png';
                    ?>
                    <div class="logo">
                        <img src="data:<?= $logoMime ?>;base64,<?= $logoData ?>" alt="Logo">
                    </div>
                <?php endif; ?> -->
            <h1><?= $company['company_name'] ?? 'ITSHOP.biz.id' ?></h1>
            <div class="company-tagline"><?= $company['title_home'] ?? 'Solusi Digital untuk Bisnis Anda' ?></div>
            <div class="company-details">
                <p>Hari Wicaksono, S.Kom. | NIB: 0292012072068</p>
                <p><?= $company['company_alamat'] ?? 'Alamat Toko' ?></p>
                <p>Telp: <?= $company['company_telepon'] ?? '-' ?> | Web: https://itshop.biz.id | Email: <?= $company['company_email1'] ?? '-' ?></p>
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <h2>#<?= $order['no_order'] ?></h2>
            <div class="invoice-meta">
                <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
                <?php if ($order['status_payment'] != 'settlement'): ?>
                    <p><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($order['created_at'] . ' +3 days')) ?></p>
                <?php endif; ?>
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

    <!-- Customer Info -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-box">
                <h3>Kepada:</h3>
                <p>
                    <strong>Nama Lengkap:</strong> <?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?>
                    <?php if ($user['company'] != '-'): ?>
                        - <strong>Perusahaan:</strong> <?= esc($user['company']) ?>
                    <?php endif; ?>
                </p>
                <p><strong>Telepon:</strong> <?= $user['phone'] ?? '-' ?></p>
                <p><strong>Email:</strong> <?= $user['email'] ?></p>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%; text-align: left;">Produk</th>
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
                <tr class="grand-total">
                    <td style="text-align: right;font-size: 16px !important;">Total:</td>
                    <td style="text-align: right;font-size: 16px !important;">Rp<?= number_format($grandtotal, 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="note">
        <strong>Catatan:</strong> <?= $order['note'] ?? '-' ?>
    </div>

    <!-- Payment Information -->
    <div class="payment-info">
        <h4>Informasi Pembayaran</h4>
        <div class="payment-details">
            <?php if ($order['status'] == 0): ?>
                <p><strong>Status:</strong> <span style="color: #ff9800; font-weight: bold;">Menunggu Pembayaran</span></p>
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

    <div class="footer">
        <p><strong>Invoice ini sah dan dihasilkan oleh sistem, tidak memerlukan tanda tangan atau cap basah.</strong></p>
        <p>Dicetak: <?= date('d/m/Y H:i:s') ?> WIB | © <?= date('Y') ?> ITSHOP.biz.id</p>
    </div>
</body>

</html>