<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>

<template>
    <v-container class="mt-5">
        <v-card class="mx-auto" max-width="600px">
            <v-card-title>
                <h2 class="text-center"><v-icon color="success" large>mdi-check</v-icon> Checkout <?= lang('App.orderSuccess'); ?></h2>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="py-5">
                <?= lang('App.orderSuccess'); ?>, Pesanan telah berhasil dikonfirmasi oleh pihak Payment Gateway.<br />Mohon tunggu pesanan dikirimkan.<br /><br />
                Terima Kasih<br />
                <?= $companyName . ' (' . COMPANY_NAME . ')'; ?>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn large color="dark" rounded link href="<?= base_url(); ?>" elevation="1">Home</v-btn>
                <v-btn large color="primary" rounded link href="<?= base_url('member'); ?>" elevation="1">Member Area</v-btn>
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