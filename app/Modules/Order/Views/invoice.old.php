<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice <?= $order['no_order'] ?></title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            color: #222;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
            color: #555;
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 10px;
            display: block;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 70px;
            height: 70px;
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

        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
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

        .info {
            margin-bottom: 15px;
        }

        .info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        thead {
            background: #f2f2f2;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px 6px;
            text-align: left;
            font-size: 13px;
        }

        th {
            text-align: center;
            font-weight: 600;
        }

        tfoot th {
            text-align: right;
            background: #f9f9f9;
        }

        .total {
            font-weight: bold;
            color: #000;
        }

        .note {
            margin-top: 20px;
            padding: 10px;
            background: #f8f8f8;
            border-left: 3px solid #007bff;
            font-size: 12px;
        }

        .payment-info {
            margin-top: 25px;
            padding: 12px;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        .payment-info h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #333;
        }

        .payment-info p {
            margin: 3px 0;
            font-size: 13px;
        }

        .small-note {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <?php
        $logoFile = $company['img_logo'] ?? '';
        if (!empty($logoFile) && file_exists(FCPATH . 'images/' . $logoFile)):
            $logoPath = FCPATH . 'images/' . $logoFile;
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoMime = getimagesize($logoPath)['mime'] ?? 'image/png';
        ?>
            <div class="logo">
                <img src="data:<?= $logoMime ?>;base64,<?= $logoData ?>" alt="Logo">
            </div>
        <?php else: ?>
            <div class="logo-placeholder">LOGO</div>
        <?php endif; ?>
        <h2><?= $company['company_name'] ?? 'ITSHOP.biz.id' ?></h2>
        <p>Hari Wicaksono, S.Kom | NIB: 0292012072068</p>
        <p><?= $company['company_alamat'] ?? 'Alamat Toko' ?></p>
        <p>Web: https://itshop.biz.id | Telp: <?= $company['company_telepon'] ?? '' ?> | Email: <?= $company['company_email1'] ?? '' ?></p>
        <hr>
    </div>

    <h2>
        Invoice #<?= $order['no_order'] ?>
        <?php if ($order['status_payment'] == 'settlement'): ?>
            <span class="status lunas">LUNAS</span>
        <?php elseif ($order['status_payment'] == 'pending'): ?>
            <span class="status pending">MENUNGGU PEMBAYARAN</span>
        <?php else: ?>
            <span class="status failed">GAGAL</span>
        <?php endif; ?>
    </h2>

    <div class="info">
        <p><strong>No. Invoice:</strong> <?= $order['no_order'] ?></p>
        <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
        <p><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($order['created_at'] . ' +7 days')) ?></p>
        <p>
            <strong>Kepada:</strong>
            <?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?>
            <?php if (!empty($user['company'])): ?>
                (<?= esc($user['company']) ?>)
            <?php endif; ?>
        </p>
        <p><strong>Email:</strong> <?= $user['email'] ?></p>
        <p><strong>Telepon:</strong> <?= $user['phone'] ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
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
                    <td style="text-align:center;"><?= $no++ ?></td>
                    <td><?= $item['product_name'] ?></td>
                    <td style="text-align:center;"><?= $item['qty'] ?></td>
                    <td style="text-align:right;">Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td style="text-align:right;">Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Subtotal</th>
                <th class="total">Rp<?= number_format($grandtotal, 0, ',', '.') ?></th>
            </tr>
            <!-- <tr>
                <th colspan="4">PPN (11%)</th>
                <th class="total">Rp<?= number_format($grandtotal * 0.11, 0, ',', '.') ?></th>
            </tr> -->
            <tr>
                <th colspan="4" style="font-size: 14px;">Total</th>
                <th class="total" style="font-size: 14px;">Rp<?= number_format($grandtotal, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="note">
        <strong>Catatan:</strong> <?= $order['note'] ?? '-' ?>
    </div>

    <div class="payment-info">
        <h4>Informasi Pembayaran:</h4>
        <?php if ($order['status'] == 0) { ?>
            Silahkan melakukan transfer pembayaran ke rekening dibawah ini:<br />
            <?php foreach ($payment as $row) { ?>
                <p><strong>Bank:</strong> <?= $row['payment'] ?? '-' ?></p>
                <p><strong>No. Rekening:</strong> <?= $row['number'] ?? '-' ?></p>
                <p><strong>Atas Nama:</strong> <?= $row['account'] ?? '-' ?></p>
                <hr />
            <?php } ?>
        <?php } else { ?>
            <p><strong>Metode:</strong> <?= $order['payment_name'] ?? '-' ?></p>
            <p><strong>No. Rekening:</strong> <?= $order['number'] ?? '-' ?></p>
            <p><strong>Atas Nama:</strong> <?= $order['account'] ?? '-' ?></p>
        <?php } ?>
        <p class="small-note">Mohon abaikan jika sudah Lunas.</p>
    </div>

    <div class="footer">
        <p><strong>Invoice ini sah dan dihasilkan oleh sistem, tidak memerlukan tanda tangan atau cap basah.</strong></p>
        <p>Dicetak: <?= date('d/m/Y H:i:s') ?> WIB | © <?= date('Y') ?> ITSHOP.biz.id</p>
    </div>

</body>

</html>