  
<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Pesanan Baru #<?= $no_order; ?> Siap Dikirim</title>
    <meta name="description" content="Pesanan Baru #<?= $no_order; ?> Siap Dikirim">
    <style type="text/css">
        
    </style>
</head>

<body>
    Hai <?= COMPANY_NAME; ?>,<br /><br />
    Alhamdulillahi rabbil alamin. Pesanan Baru #<?= $no_order; ?> senilai Rp <?= $total; ?> telah masuk tanggal <?= $created_at; ?> dan pembayaran telah dikonfirmasi.<br /><br />
    Mohon segera periksa pada Dashboard dan kirimkan pesanan ke Pembeli <?= $email; ?> / <?= $phone; ?>.<br />
    Catatan: <?= $note; ?>
</body>

</html>