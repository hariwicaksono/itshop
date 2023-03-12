<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>

<template>
    <v-container>
        <v-card class="mx-auto" max-width="600px">
            <v-card-title><h1 class="text-center"><v-icon color="success" large>mdi-check</v-icon> <?= lang('App.orderSuccess');?></h1></v-card-title>
            <v-divider></v-divider>
            <v-card-text>
            <?php foreach ($order as $row) : ?>
                <?= lang('App.noOrder');?>: <?= $row['no_order']; ?><br/>
                <?= lang('App.date');?>: <?= $row['created_at'] ?><br/>
                <h3 class="text-center"><?= lang('App.pleaseTransfer');?>:</h3>
                <h1 class="text-center py-3">Rp.<?= $row['total']; ?></h1>
                <h3 class="text-center"><?= lang('App.to');?>:</h3>
                BANK: <?= $row['payment']; ?><br/>
                <?= lang('App.account');?>: <?= $row['account']; ?><br/>
                <?= lang('App.number');?>: <?= $row['number']; ?><br/>
            <?php endforeach; ?>
            <br/>
            Thank You
            </v-card-text>
        </v-card>
    </v-container>
</template>

<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    computedVue = {
        ...computedVue,
    }
    createdVue = function() {

    }
    watchVue = {
        ...watchVue,

    }
    dataVue = {
        ...dataVue,
        dialog: false,
        note: null,
        tbheader: [{
                text: 'Nama',
                value: 'nama_produk'
            },
            {
                text: 'Qty',
                value: 'qty'
            },
            {
                text: 'Total',
                value: 'total'
            },
            {
                text: '',
                value: 'actions'
            },
        ],
        idorder: "",
        total: "",
        grandtotal: "",
    }
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    }
    methodsVue = {
        ...methodsVue,

    }
</script>

<?php $this->endSection("js") ?>