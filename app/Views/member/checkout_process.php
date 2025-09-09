<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container>
        <v-card>
            <v-card-title>
                <?= $title; ?>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text class="py-5">
                <h3 class="font-weight-medium"><?= lang('App.yourOrder'); ?></h3>
                <v-row class="my-1" v-for="item in carts" :key="item.cart_id">
                    <v-col cols="12">
                        <v-avatar size="80px" rounded class="float-left me-3">
                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url(); ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                        </v-avatar>
                        <h2 class="font-weight-regular"><strong>{{item.product_name}}</strong> | Qty: {{item.qty}}
                            <br />
                            <?= lang('App.price'); ?>: <span v-if="item.discount > 0">
                                {{ RibuanLocale(item.product_price) }}
                            </span>
                            <span v-else>{{ RibuanLocale(item.product_price) }}</span>

                            <span v-show="item.discount > 0">
                                <p class="text-body-2 mb-0"><span class="text-decoration-line-through">{{ RibuanLocale(item.product_price_normal) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.discount_percent}}%</v-chip></p>
                            </span>
                        </h2>
                    </v-col>
                </v-row>
                <h4 class="float-end">Total {{RibuanLocale(total)}}</h4>

                <h3 class="font-weight-medium mb-3"><?= lang('App.chooseShipment'); ?></h3>
                <v-select v-model="select_shipment" :items="list_shipment" item-text="shipment" item-value="shipment_id" label="<?= lang('App.shipment'); ?>" :eager="true" :rules="[rules.required]" outlined></v-select>

                <h3 class="font-weight-medium mb-3"><?= lang('App.choosePayment'); ?></h3>
                <v-select v-model="select_payment" :items="list_payment" item-text="payment" item-value="payment_id" label="<?= lang('App.payment'); ?>" :eager="true" :rules="[rules.required]" outlined></v-select>

                <h3 class="font-weight-medium"><?= lang('App.yourNote'); ?></h3>
                <v-text-field label="<?= lang('App.note') ?> Order *" v-model="note" class="mt-3  mr-3" hint="Cantumkan disini alamat email Gmail Anda" persistent-hint :disabled="select_payment == null"></v-text-field>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions>
                <h3 class="font-weight-regular pe-5"><?= lang('App.totalPrice'); ?><br /><strong>{{RibuanLocale(grandtotal)}}</strong></h3>
                <v-spacer></v-spacer>
                <v-btn color="success" large @click="saveOrder" :loading="loading3" :disabled="note == ''">
                    <v-icon>mdi-shield-lock</v-icon> <?= lang('App.processPayment') ?>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<br /><br />
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $clientKey; ?>"></script>
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    }

    computedVue = {
        ...computedVue,
    }

    dataVue = {
        ...dataVue,
        dialog: false,
        carts: [],
        note: "",
        tbheader: [{
                text: '<?= lang('App.productName'); ?>',
                value: 'product_name'
            },
            {
                text: 'Qty',
                value: 'qty'
            },
            {
                text: 'Subtotal',
                value: 'total'
            },
            {
                text: '',
                value: 'actions',
                sortable: false
            },
        ],
        no_order: "<?= $noOrder; ?>",
        total: "<?= $total; ?>",
        grandtotal: "",
        list_shipment: [],
        select_shipment: null,
        list_payment: [],
        select_payment: null,
    }

    createdVue = function() {
        this.getUserCart();
        this.getPayment();
        this.getShipment();
    }

    watchVue = {
        ...watchVue,
        select_payment: function() {
            //let RandNumber = Math.floor(Math.random() * 100);
            this.grandtotal = this.total;
        },
    }

    methodsVue = {
        ...methodsVue,
        getShipment: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/shipment`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_shipment = data.data;
                    this.loading = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        getPayment: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/payment`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_payment = data.data;
                    this.loading = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get User Cart
        getUserCart: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/cart/usercart`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.carts = data.data;
                        const itemcart = this.carts.map((row) => (
                            [row.cart_id, row.product_id, row.price, row.stock, row.qty]
                        ));
                        this.itemcart = itemcart;
                        //console.log(this.itemcart)
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.carts = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        sumTotal(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.carts.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        // Save Order
        saveOrder: function() {
            this.loading3 = true;
            const data = this.itemcart;
            //console.log(data);
            axios.post(`<?= base_url() ?>api/order/save`, {
                    data: data,
                    total: this.grandtotal,
                    no_order: this.no_order,
                    user_id: '<?= session()->get('id'); ?>',
                    note: this.note,
                    payment: this.select_payment,
                    shipment: this.select_shipment
                }, options)
                .then(res => {
                    // handle success
                    this.loading3 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        if (this.select_payment == 1) {
                            this.processPayment();
                        } else {
                            setTimeout(() => window.location.href = data.data.url, 1000);
                        }
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                    this.getOrderCount();
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

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