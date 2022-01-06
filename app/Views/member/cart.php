<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<v-container>
    <h1 class="font-weight-regular mb-3"><?= lang('App.cart') ?></h1>
    <v-data-table height="250" :headers="tbheader" :fixed-header="true" :items="carts" item-key="cart_id" :loading="loading" loading-text="Memuat data, silahkan tunggu...">
        <template v-slot:item="{ item }">
            <tr>
                <td>
                    <v-avatar size="50px" rounded><img v-bind:src="'../' + item.media_path" /></v-avatar> {{item.product_name}}
                </td>
                <td>
                    <v-edit-dialog :return-value.sync="item.qty" @save="setQuantity(item)" @cancel="" @open="" @close="">
                        {{item.qty}}
                        <template v-slot:input>
                            <v-text-field v-model="item.qty" type="number" single-line></v-text-field>
                        </template>
                    </v-edit-dialog>
                </td>
                <td>Rp.{{item.total}}</td>
                <td>
                    <v-btn icon @click="removeItem(item)">
                        <v-icon color="red">
                            mdi-delete
                        </v-icon>
                    </v-btn>
                </td>
            </tr>
        </template>
        <template slot="footer.prepend">
             <v-text-field label="<?= lang('App.note') ?> *" v-model="note"></v-text-field>
        </template>  
    </v-data-table>
    <br />
    <v-row>
        <v-col cols="12" md="9">
            <h1 class="font-weight-regular float-end"><?= lang('App.totalPrice'); ?> <strong>Rp.{{ sumTotal('total') }}</strong></h1>
        </v-col>
        <v-col cols="12" md="3">
            <v-btn color="success" block large :loading="loading2" @click="modalCheckoutOpen" :disabled="carts == '' ? true:false">
                <v-icon>mdi-cart</v-icon> <?= lang('App.buy') ?>
            </v-btn>
        </v-col>
    </v-row>
</v-container>

<!-- Modal Reset Keranjang -->
<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" persistent width="900px">
            <v-card class="pa-2">
                <v-card-title class="text-h5">
                    <?= lang('App.checkout') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalCheckoutClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <h3 class="font-weight-medium"><?= lang('App.yourOrder'); ?></h3>
                    <v-row class="my-1" v-for="item in carts" :key="item.cart_id">
                        <v-col cols="2">
                            <v-avatar size="50px" rounded><img v-bind:src="'../' + item.media_path" /></v-avatar>
                        </v-col>
                        <v-col cols="10">
                            <strong>{{item.product_name}}</strong> - Qty: {{item.qty}} - <?= lang('app.price');?>: Rp.{{item.product_price}}
                        </v-col>
                    </v-row>
                    <h4 class="float-end">Total Rp.{{total}}</h4>

                    <h3 class="font-weight-medium mb-4"><?= lang('App.chooseShipment'); ?></h3>
                    <v-select v-model="select_shipment" :items="list_shipment" item-text="shipment" item-value="shipment_id" label="<?= lang('App.shipment'); ?>" :eager="true" :rules="[rules.required]" outlined dense></v-select>

                    <h3 class="font-weight-medium mb-4"><?= lang('App.choosePayment'); ?></h3>
                    <v-select v-model="select_payment" :items="list_payment" item-text="payment" item-value="payment_id" label="<?= lang('App.payment'); ?>" :eager="true" :rules="[rules.required]" outlined dense></v-select>

                    <h3 class="font-weight-medium"><?= lang('App.yourNote'); ?></h3>
                    {{note??'-'}}
                </v-card-text>

                <v-divider></v-divider>
                <v-card-actions>
                    <h3 class="font-weight-regular pe-5"><?= lang('App.totalPrice'); ?><br /><strong>Rp.{{grandtotal}}</strong></h3>
                    <v-spacer></v-spacer>
                    <v-btn color="success" dark large @click="saveOrder" :loading="loading3">
                        <?= lang('App.processPayment') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>              
<!-- End Modal Reset -->

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
            axios.get(`/api/shipment`, options)
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
            axios.get(`/api/payment`, options)
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
            axios.get(`/api/cart/usercart`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
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
            axios.post(`/api/cart/save`, {
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
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
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
                })
        },
        // Set Qty Item
        setQuantity: function(item) {
            this.loading = true;
            this.cart_id = item.cart_id;
            this.qty = item.qty;
            this.product_id = item.product_id;
            axios.put(`/api/cart/update/${this.cart_id}`, {
                    product_id: this.product_id,
                    qty: this.qty,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUserCart();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                })
        },
        // Delete Item Keranjang
        removeItem: function(item) {
            this.loading = true;
            axios.delete(`/api/cart/delete/${item.cart_id}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
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
                })
        },
        // Save Order
        saveOrder: function(item) {
            this.loading3 = true;
            const data = this.itemcart;
            //console.log(data);
            axios.post(`/api/order/save`, {
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
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
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
                })
        },
    }
</script>

<?php $this->endSection("js") ?>