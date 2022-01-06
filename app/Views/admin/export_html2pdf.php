<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
     
    <title>Print PDF</title>
    <style>
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
    </style>
</head>

<body>

    <div class="container mt-5">
        <h1 align="center">Sample HTML2PDF</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama Produk</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Deskripsi Produk</th>
                    <th scope="col">Aktif</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($product as $row) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td width="400"><?= $row['product_name']; ?></td>
                    <td>Rp.<?= $row['product_price']; ?></td>
                    <td><?= $row['product_description']; ?></td>
                    <td><?= $row['active']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>