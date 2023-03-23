<?php
function Ribuan($angka)
{

    $hasil_rupiah = number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}
?>

<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>

<template>
    <v-container>
        <v-card class="mx-auto" max-width="600px">
            <v-card-title>
                <h2 class="text-center"><v-icon color="success" large>mdi-check</v-icon> Checkout <?= lang('App.orderSuccess'); ?></h2>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="py-5">
                <v-card-text>
                    <?php foreach ($order as $row) : ?>
                        <?= lang('App.noOrder'); ?>: <?= $row['no_order']; ?><br />
                        <?= lang('App.date'); ?>: <?= $row['created_at'] ?><br />
                        <h3 class="text-center"><?= lang('App.pleaseTransfer'); ?>:</h3>
                        <h1 class="text-center py-3">Rp.<?= $row['total']; ?></h1>
                        <h3 class="text-center"><?= lang('App.to'); ?>:</h3>
                        BANK: <?= $row['payment']; ?><br />
                        <?= lang('App.account'); ?>: <?= $row['account']; ?><br />
                        <?= lang('App.number'); ?>: <?= $row['number']; ?><br />
                    <?php endforeach; ?>
                    <br />
                    Terima Kasih<br />
                    <?= COMPANY_NAME; ?>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" rounded link href="<?= base_url(); ?>">Home</v-btn>
                    <v-btn color="primary" rounded link href="<?= base_url('member'); ?>">Member Area</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
        </v-card>
    </v-container>
</template>
<br />
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