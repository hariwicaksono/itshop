<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>

<template>
    <v-container class="py-7">
        <v-row>
            <v-col cols="12" sm="4">
                <v-img :src="'<?= base_url() ?>' + products.media_path" class="mb-3" title="" alt="" v-if="products.media_path != null"></v-img>
                <v-img src="<?= base_url('images/no_image.jpg') ?>" class="mb-3" v-else></v-img>
            </v-col>
            <v-col cols="12" sm="5">
                <h1 class="text-h5 font-weight-bold mb-4">{{name}}</h1>
                <h2 class="text-h4 font-weight-bold mb-4">{{RibuanLocale(price)}}</h2>
                <v-chip class="mb-3" title="Stock" alt="Stock"><v-icon>mdi-package-variant-closed</v-icon>&nbsp;{{stock}}</v-chip>

                <h4 class="mb-4 mt-3">Detail Product:</h4>
                <p v-html="products.product_description"></p>

                <h4 class="mb-3 mt-3">Pengiriman:</h4>
                <p><v-icon>mdi-map-marker-outline</v-icon> Dikirim dari <strong>Purwokerto, Kab. Banyumas</strong></p>
                <ul>
                    <li v-for="item in shipment" :key="item.shipment_id">{{item.shipment}}</li>
                </ul>

            </v-col>
            <v-col cols="12" sm="3">
                <v-card outlined>
                    <v-card-title>Atur jumlah pesanan</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="qty" type="number" single-line prepend-icon="mdi-minus" append-outer-icon="mdi-plus" @click:append-outer="increment(products)" @click:prepend="decrement(products)" min="1" :error-messages="qtyError"></v-text-field>
                        <h2 class="mb-5 mt-2"><span class="text-subtitle-1 font-weight-regular">Subtotal:</span> {{RibuanLocale(subTotal)}}</h2>
                        <v-btn large block color="primary" @click="saveCart(products)" elevation="0">
                            + <?= lang('App.carts'); ?>
                        </v-btn>
                    </v-card-text>
                </v-card>

            </v-col>

        </v-row>
    </v-container>
</template>

<v-dialog v-model="loading" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            Memuat, silahkan tunggu...
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
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
        idProduct: '<?= $product_id; ?>',
        qty: 1,
        qtyError: "",
        products: [],
        shipment: [],
        name: "",
        price: 0,
        stock: 0,
        subTotal: 0,
        pageCount: 0,
        currentPage: 1,
    }

    createdVue = function() {
        this.getProduct();
        this.getShipment();
    }

    watchVue = {
        qty: function() {
            if (this.qty >= 1) {
                this.subTotal = this.price * this.qty;
            }

            if (this.qty > this.stock) {
                this.qtyError = "Out of Stock";
            } else {
                this.qtyError = "";
            }
        }
    }

    methodsVue = {
        ...methodsVue,
        // Get Product
        getProduct: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>openapi/product/${this.idProduct}`)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.products = data.data;
                        this.name = this.products.product_name;
                        this.price = this.products.product_price;
                        this.stock = parseInt(this.products.stock);
                        if (this.qty == 1) {
                            this.subTotal = this.price;
                        }

                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.products = [];
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },

        increment(items) {
            this.qty++;
            if (this.qty < 1) return;
            if (this.qty >= items.stock) {
                this.qty = items.stock;
            }
        },
        decrement(items) {
            this.qty--;
            if (this.qty < 1) {
                this.qty = 1;
            };
        },

        // Save Cart
        saveCart: function(item) {
            this.loading = true;
            axios.post(`<?= base_url() ?>api/cart/save`, {
                    product_id: item.product_id,
                    price: item.product_price,
                    stock: item.stock,
                    qty: this.qty,
                    user_id: '<?= session()->get('id'); ?>',
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        //this.snackbarType = "warning";
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

        // Get Shipment
        getShipment: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>openapi/shipment`)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.shipment = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.shipment = [];
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
    }
</script>

<?php $this->endSection("js") ?>