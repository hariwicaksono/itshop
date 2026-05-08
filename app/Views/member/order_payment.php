<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="mt-5">
        <v-card class="mx-auto" max-width="700">
            <!-- HEADER -->
            <v-card-title class="justify-center">
                <v-icon color="warning" large class="mr-2">mdi-timer-sand</v-icon>
                <span class="text-h5 font-weight-bold">
                    <?= lang('App.payment'); ?> #<?= $noOrder ?>
                </span>
            </v-card-title>

            <v-divider></v-divider>

            <!-- STATUS -->
            <v-alert
                type="warning"
                border="left"
                colored-border
                icon="mdi-alert"
                prominent
                class="ma-4"
                dense>
                <?= lang('App.orderPending'); ?><br />
                Pesanan sedang menunggu pembayaran ke Payment Gateway.
            </v-alert>

            <!-- ORDER INFO -->
            <v-card-text>
                <v-simple-table>
                    <tbody>
                        <tr>
                            <td>No Order:</td>
                            <td class="font-weight-bold"><?= $order['no_order']; ?></td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td>
                                <v-chip small color="orange" text-color="white">
                                    PENDING
                                </v-chip>
                            </td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td class="font-weight-bold">
                                Rp <?= number_format($order['total'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Catatan:</td>
                            <td><?= esc($order['note']); ?></td>
                        </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>

            <v-divider></v-divider>

            <!-- CART LIST -->
            <v-card-text>
                <div class="text-subtitle-1 font-weight-bold mb-3">
                    Detail Produk
                </div>

                <v-list>
                    <?php foreach ($cart as $item): ?>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">
                                    <?= esc($item['product_name']); ?>
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Kategori: <?= esc($item['category_name']); ?><br>
                                    Qty: <?= $item['qty']; ?> ×
                                    Rp <?= number_format($item['price'], 0, ',', '.'); ?>
                                </v-list-item-subtitle>
                            </v-list-item-content>

                            <v-list-item-action>
                                <span class="font-weight-bold">
                                    Rp <?= number_format($item['total'], 0, ',', '.'); ?>
                                </span>
                            </v-list-item-action>
                        </v-list-item>
                        <v-divider></v-divider>
                    <?php endforeach; ?>
                </v-list>
            </v-card-text>

            <!-- ACTION -->
            <v-card-actions class="justify-center pb-5">
                <v-btn
                    color="success"
                    large
                    rounded
                    :loading="loading3"
                    @click="processPayment">
                    <v-icon left>mdi-shield-lock</v-icon>
                    <?= lang('App.processPayment') ?>
                </v-btn>
            </v-card-actions>

            <v-divider></v-divider>

            <!-- FOOTER -->
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text color="primary" href="<?= base_url(); ?>">Home</v-btn>
                <v-btn text color="primary" href="<?= base_url('member'); ?>">Member</v-btn>
                <v-btn text color="primary" href="<?= base_url('member/order-list'); ?>">
                    Order List
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>

        </v-card>
    </v-container>
</template>
<br />
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?= $clientKey; ?>"></script>
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    }

    dataVue = {
        ...dataVue,
        no_order: "<?= $noOrder; ?>",
        total: "<?= $total; ?>",
    }

    methodsVue = {
        ...methodsVue,
        processPayment: function() {
            // SnapToken acquired from previous step
            snap.pay('<?= $SnapToken ?>', {
                // Optional
                onSuccess: function(result) {
                    console.log(result.status_message);
                    console.log(JSON.stringify(result));
                    if (result.transaction_status == 'settlement') {
                        window.location.href = '<?= base_url('/checkout/success/finish'); ?>';
                    }
                },
                // Optional
                onPending: function(result) {
                    console.log(result.status_message);
                    console.log(JSON.stringify(result));
                    if (result.transaction_status == 'pending') {
                        setTimeout(() => window.location.href = '<?= base_url('cart'); ?>', 3000);
                    }
                },
                // Optional
                onError: function(result) {
                    console.log(result.status_message);
                    console.log(JSON.stringify(result));
                }
            });
        },
    }
</script>

<?php $this->endSection("js") ?>