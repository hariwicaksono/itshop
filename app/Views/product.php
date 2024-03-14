<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>

<template>
    <v-container class="mt-3 mb-5">
        <v-card outlined>
            <v-breadcrumbs :items="itemsBC"></v-breadcrumbs>
        </v-card>
        <br />
        <v-row>
            <v-col cols="12" sm="4">
                <a :href="'<?= base_url() ?>' + image" target="_blank" v-if="image != null">
                    <v-img v-model="image" :src="'<?= base_url() ?>' + image" aspect-ratio="1" class="mb-4" title="Cover" alt="Cover">
                        <v-overlay absolute="true" v-if="products.active == '0'">
                            <v-chip>
                                <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.notAvailable'); ?>
                            </v-chip>
                        </v-overlay>
                        <v-overlay absolute="true" v-else-if="products.stock == '0'">
                            <v-chip>
                                <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.outofStock'); ?>
                            </v-chip>
                        </v-overlay>
                    </v-img>
                </a>
                <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else>
                    <v-overlay absolute="true" v-if="products.active == '0'">
                        <v-chip>
                            <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.notAvailable'); ?>
                        </v-chip>
                    </v-overlay>
                    <v-overlay absolute="true" v-else-if="products.stock == '0'">
                        <v-chip>
                            <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.outofStock'); ?>
                        </v-chip>
                    </v-overlay>
                </v-img>

                <v-row>
                    <v-col>
                        <a @click="click(products.media_path)">
                            <v-img :src="'<?= base_url() ?>' + products.media_path" aspect-ratio="1" class="mb-3" title="Thumb Cover" alt="Thumb Cover" v-if="products.media_path != null"></v-img>
                        </a>
                    </v-col>
                    <v-col>
                        <a @click="click(products.media_path1)">
                            <v-img :src="'<?= base_url() ?>' + products.media_path1" aspect-ratio="1" class="mb-3" title="Thumb Image 1" alt="Thumb Image 1" v-if="products.media_path1 != null"></v-img>
                        </a>
                    </v-col>
                    <v-col>
                        <a @click="click(products.media_path2)">
                            <v-img :src="'<?= base_url() ?>' + products.media_path2" aspect-ratio="1" class="mb-3" title="Thumb Image 2" alt="Thumb Image 2" v-if="products.media_path2 != null"></v-img>
                        </a>
                    </v-col>
                    <v-col>
                        <a @click="click(products.media_path3)">
                            <v-img :src="'<?= base_url() ?>' + products.media_path3" aspect-ratio="1" class="mb-3" title="Thumb Image 3" alt="Thumb Image 3" v-if="products.media_path3 != null"></v-img>
                        </a>
                    </v-col>
                    <v-col>
                        <a @click="click(products.media_path4)">
                            <v-img :src="'<?= base_url() ?>' + products.media_path4" aspect-ratio="1" class="mb-3" title="Thumb Image 4" alt="Thumb Image 4" v-if="products.media_path4 != null"></v-img>
                        </a>
                    </v-col>
                </v-row>
                <!-- <v-img src="<?= base_url('images/no_image.jpg') ?>" class="mb-3" v-else></v-img> -->
            </v-col>
            <v-col cols="12" sm="5">
                <h1 class="text-h5 font-weight-bold mb-3">{{name}}</h1>
                <p class="mb-4">{{code}} &nbsp;&bull;&nbsp; <?= lang('App.sold'); ?>: <?= $productSold; ?></p>
                <h2 class="text-h4 font-weight-bold mb-5">
                    <span v-if="discount > 0">
                        {{ RibuanLocale(price - discount) }}
                    </span>
                    <span v-else>{{ RibuanLocale(price) }}</span>
                    <span v-show="discount > 0">
                        <p class="text-body-1 mb-0"><span class="text-decoration-line-through">{{ RibuanLocale(price) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{discountPercent}}%</v-chip></p>
                    </span>
                </h2>
                <v-divider></v-divider>
                <h4 class="mb-3 mt-5">Detail Produk:</h4>
                <p v-html="products.product_description"></p>

                <h4 class="mb-3 mt-7"><?= lang('App.category'); ?>:</h4>
                <v-chip close close-icon="mdi-check-circle">{{category}}</v-chip>

                <h4 class="mb-3 mt-7">Link Demo:</h4>
                <v-btn text outlined :href="linkDemo" link target="_blank" :disabled="linkDemo == null"><v-icon>mdi mdi-link</v-icon> Link Demo</v-btn>

                <h4 class="mb-3 mt-7">Pengiriman:</h4>
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
                        <span class="text-subtitle-1 font-weight-regular">Stock: <strong>{{stock}}</strong></span>
                        <h2 class="mb-5 mt-2"><span class="text-subtitle-1 font-weight-regular">Subtotal:</span> <span>{{RibuanLocale(subTotal)}}</span></h2>

                        <v-btn large block color="success" @click="sendWhatsApp(products)" elevation="1" class="mb-3" :disabled="products.stock == 0 || products.active == 0">
                            <v-icon>mdi-whatsapp</v-icon> <span class="d-flex d-sm-none d-md-none d-lg-flex d-xl-flex">Chat</span> WhatsApp
                        </v-btn>
                        <v-btn large block color="primary" @click="saveCart(products)" elevation="1" class="mb-3" :disabled="products.stock == 0 || products.active == 0">
                            <v-icon>mdi-cart-plus</v-icon> <?= lang('App.carts'); ?>
                        </v-btn>
                        <v-btn large block color="primary" outlined link href="<?= base_url('cart'); ?>" elevation="1">
                            <v-icon>mdi-cart</v-icon> <?= lang('App.cart'); ?>
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
            <?= lang('App.loadingWait'); ?>
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
        itemsBC: [{
            text: 'Home',
            disabled: false,
            href: '/',
        }, {
            text: '<?= $title; ?>',
            disabled: true,
            href: '',
        }, ],
        dialog: false,
        idProduct: '<?= $product_id; ?>',
        qty: 1,
        qtyError: "",
        products: [],
        shipment: [],
        code: "",
        name: "",
        price: 0,
        discount: 0,
        discountPercent: 0,
        stock: 0,
        subTotal: 0,
        pageCount: 0,
        currentPage: 1,
        image: "",
        linkDemo: "",
        category: ""
    }

    createdVue = function() {
        this.getProduct();
        this.getShipment();

    }

    watchVue = {
        qty: function() {
            if (this.qty >= 1) {
                if (this.discount > 0) {
                    var subtotalDisc = this.price - this.discount;
                    this.subTotal = subtotalDisc * this.qty;
                } else {
                    this.subTotal = this.price * this.qty;
                }
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
                        this.code = this.products.product_code;
                        this.name = this.products.product_name;
                        this.price = parseInt(this.products.product_price);
                        this.discount = parseInt(this.products.discount);
                        this.discountPercent = parseInt(this.products.discount_percent);
                        this.stock = parseInt(this.products.stock);
                        this.image = this.products.media_path;
                        if (this.qty == 1) {
                            if (this.discount > 0) {
                                var subtotalDisc = this.price - this.discount;
                                this.subTotal = subtotalDisc * this.qty;
                            } else {
                                this.subTotal = this.price * this.qty;
                            }
                        }
                        this.linkDemo = this.products.link_demo;
                        this.category = this.products.category_name;
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

        click(image) {
            this.image = image;
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

        // send WhatsApp (Wa.me)
        sendWhatsApp: function(item) {
            let encoded = encodeURIComponent('<?= $wa_text; ?> ' + item.category_name + ': ' + item.product_name);
            setTimeout(() => window.location.href = `https://wa.me/<?= $telepon; ?>?text=${encoded}`, 100);
        },
    }
</script>

<?php $this->endSection("js") ?>