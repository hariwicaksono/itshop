<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container>
        <h1 class="font-weight-medium mb-3 mt-4"><?= lang('App.cart') ?></h1>
        <v-data-table height="250" :headers="tbheader" :fixed-header="true" :items="carts" item-key="cart_id" :loading="loading">
            <template v-slot:item="{ item }">
                <tr>
                    <td>
                        <v-list-item class="ma-n3 pa-n3" two-line>
                            <v-list-item-avatar size="80" rounded>
                                <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                                <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                            </v-list-item-avatar>
                            <v-list-item-content>
                                <p class="text-subtitle-1">
                                    <a link :href="'<?= base_url(); ?>' + item.category_slug + '/' + item.slug" class="font-weight-regular text-decoration-none" title="" alt="">{{ item.product_name }}</a>
                                    <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>" v-show="item.discount > 0"><?= lang('App.discount'); ?> {{item.discount_percent}}%</v-chip>
                                </p>
                                <p class="mb-0">Code: {{item.product_code ?? "-"}}</p>
                            </v-list-item-content>
                        </v-list-item>
                    </td>
                    <td width="200">
                        <v-text-field v-model="item.qty" type="number" single-line prepend-icon="mdi-minus" append-outer-icon="mdi-plus" @click:append-outer="increment(item)" @click:prepend="decrement(item)" @input="setQuantity(item)" min="1" hide-details></v-text-field>
                    </td>
                    <td width="200" class="text-subtitle-1">{{RibuanLocale(item.total)}}</td>
                    <td>
                        <v-btn icon @click="removeItem(item)">
                            <v-icon color="red">
                                mdi-delete
                            </v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
           <!--  <template slot="footer.prepend">
                <v-text-field label="<?= lang('App.note') ?> Order *" v-model="note" class="mt-3  mr-3" hint="Cantumkan disini alamat email Gmail Anda" persistent-hint></v-text-field>
            </template> -->
        </v-data-table>
        <br />
        <v-row>
            <v-col cols="12" md="9">
                <h1 class="font-weight-regular float-end"><?= lang('App.totalPrice'); ?> <strong>{{ RibuanLocale(sumTotal('total')) }}</strong></h1>
            </v-col>
            <v-col cols="12" md="3">
                <v-btn color="success" block large :loading="loading2" @click="goCheckout" :disabled="carts == '' ? true:false">
                    <v-icon>mdi-cart</v-icon> <?= lang('App.buy') ?>
                </v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>

<!-- Modal Checkout -->
<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" persistent width="900px">
            <v-card>
                <v-card-title>
                    <?= lang('App.checkout') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalCheckoutClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <h3 class="font-weight-medium"><?= lang('App.yourOrder'); ?></h3>
                    <v-row class="my-1" v-for="item in carts" :key="item.cart_id">
                        <v-col cols="2">
                            <v-avatar size="80px" rounded><img v-bind:src="'<?= base_url(); ?>' + item.media_path" /></v-avatar>
                        </v-col>
                        <v-col cols="10">
                            <h3>{{item.product_name}} - Qty: {{item.qty}} - <?= lang('app.price'); ?>: {{RibuanLocale(item.product_price)}}</h3>
                        </v-col>
                    </v-row>
                    <h4 class="float-end">Total {{RibuanLocale(total)}}</h4>

                    <h3 class="font-weight-medium mb-3"><?= lang('App.chooseShipment'); ?></h3>
                    <v-select v-model="select_shipment" :items="list_shipment" item-text="shipment" item-value="shipment_id" label="<?= lang('App.shipment'); ?>" :eager="true" :rules="[rules.required]" outlined></v-select>

                    <h3 class="font-weight-medium mb-3"><?= lang('App.choosePayment'); ?></h3>
                    <v-select v-model="select_payment" :items="list_payment" item-text="payment" item-value="payment_id" label="<?= lang('App.payment'); ?>" :eager="true" :rules="[rules.required]" outlined></v-select>

                    <h3 class="font-weight-medium"><?= lang('App.yourNote'); ?></h3>
                    {{note??'-'}}
                </v-card-text>

                <v-divider></v-divider>
                <v-card-actions>
                    <h3 class="font-weight-regular pe-5"><?= lang('App.totalPrice'); ?><br /><strong>{{RibuanLocale(grandtotal)}}</strong></h3>
                    <v-spacer></v-spacer>
                    <v-btn color="success" dark large @click="saveOrder" :loading="loading3">
                        <?= lang('App.processPayment') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal -->
<br /><br />
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    computedVue = {
        ...computedVue,
    }
    createdVue = function() {
        this.getUserCart();
    }
    watchVue = {
        ...watchVue,
        select_payment: function() {
            //let RandNumber = Math.floor(Math.random() * 100);
            this.grandtotal = this.total;
        },
    }
    dataVue = {
        ...dataVue,
        dialog: false,
        carts: [],
        note: null,
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
        idorder: "",
        total: "",
        grandtotal: "",
        list_shipment: [],
        select_shipment: null,
        list_payment: [],
        select_payment: null,
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
        goCheckout: function() {
            setTimeout(() => window.location.href = '<?= base_url('checkout'); ?>', 200);
        },
        modalCheckoutOpen: function() {
            this.dialog = true;
            this.getPayment();
            this.getShipment();
        },
        modalCheckoutClose: function() {
            this.dialog = false;
        },
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
                    console.log(err.response);
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
                    console.log(err.response);
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
                            [row.product_id, row.price, row.stock, row.qty]
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
        // Save Cart
        saveCart: function(item) {
            this.loading = true;
            axios.post(`<?= base_url() ?>api/cart/save`, {
                    product_id: item.product_id,
                    price: item.product_price,
                    stock: item.stock,
                    qty: 1,
                    user_id: '<?= session()->get('id'); ?>',
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getCartCount();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.snackbar = true;
                    this.snackbarMessage = err;
                    if (err.response.status == 401) {
                        this.snackbarMessage = '<?= lang('App.pleaseLogin'); ?>';
                        setTimeout(() => window.location.href = '/login', 2000);
                    }
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        increment(item) {
            item.qty++;
            if (item.qty < 1) return;
            this.setQuantity(item);
        },
        decrement(item) {
            item.qty--;
            if (item.qty < 1) {
                item.qty = 1;
            } else {
                this.setQuantity(item);
            };
        },

        // Set Qty Item
        setQuantity: function(item) {
            this.loading = true;
            this.cart_id = item.cart_id;
            this.qty = item.qty;
            this.product_id = item.product_id;
            axios.put(`<?= base_url() ?>api/cart/update/${this.cart_id}`, {
                    product_id: this.product_id,
                    qty: this.qty,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUserCart();
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
        // Delete Item Keranjang
        removeItem: function(item) {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/cart/delete/${item.cart_id}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUserCart();
                        this.getCartCount();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        // Save Order
        saveOrder: function(item) {
            this.loading3 = true;
            const data = this.itemcart;
            //console.log(data);
            axios.post(`<?= base_url() ?>api/order/save`, {
                    data: data,
                    total: this.grandtotal,
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
                        this.idorder = data.data.idorder;
                        this.dialog = false;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
    }
</script>

<?php $this->endSection("js") ?>