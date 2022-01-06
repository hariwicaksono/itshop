<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container>
        <v-parallax src="images/technology_geometric_hexagon_orange_1451800.png" dark class="rounded-lg" height="350">
            <v-row align="center" justify="center">
                <v-col class="text-center" cols="12">
                    <h1 class="text-h3 mb-3">
                        CodeIgniter 4 + Vue.js + Vuetify
                    </h1>
                    <h2 class="text-h4 mb-3">
                        Build your application today!
                    </h2>
                </v-col>
            </v-row>
        </v-parallax>
    </v-container>
</template>

<template>
    <v-container>
        <h1 class="mb-3 font-weight-regular"><?= lang('App.latestProduct') ?></h1>
        <v-row v-if="show == true">
            <v-col v-for="n in 4" :key="n" cols="12" md="3">
                <v-card outlined elevation="1">
                    <v-skeleton-loader class="mx-auto" max-width="300" type="card"></v-skeleton-loader>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="show == false">
            <v-col v-for="item in products" :key="item.product_id" cols="12" md="3">
                <v-card outlined elevation="1">
                    <v-img v-bind:src="'../' + item.media_path" height="200px"></v-img>
                    <v-card-title class="subtitle-1">{{ item.product_name }}</v-card-title>
                    <v-card-subtitle class="subtitle-1 orange--text font-weight-medium">
                        Rp.{{ item.product_price }}
                        <v-btn icon class="float-end" color="warning" @click="saveCart(item)">
                            <v-icon>mdi-cart</v-icon>
                        </v-btn>
                    </v-card-subtitle>
                </v-card>
            </v-col>
        </v-row>
        <br />
        <paginate :page-count="pageCount" :no-li-surround="true" :container-class="'v-pagination theme--light'" :page-link-class="'v-pagination__item v-btn'" :active-class="'v-pagination__item--active primary white--text'" :disabled-class="'v-pagination__navigation--disabled'" :prev-link-class="'v-pagination__navigation black--text'" :next-link-class="'v-pagination__navigation black--text'" :click-handler="handlePagination" :prev-text="'<'" :next-text="'>'">
        </paginate>
    </v-container>
</template>

<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" width="900">
            <v-card>
                <v-card-actions>
                    <v-card-title></v-card-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="dialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-actions>
                <v-card-text>

                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
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
    computedVue = {
        ...computedVue,
    }
    createdVue = function() {
        this.getProducts();
    }
    dataVue = {
        ...dataVue,
        dialog: false,
        products: [],
        pageCount: 0,
        currentPage: 1,
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
        // Get Product
        getProducts: function() {
            this.show = true;
            axios.get(`/openapi/product/all?page=${this.currentPage}`)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.products = data.data;
                    this.pageCount = Math.ceil(data.total_page / data.per_page);
                    this.show = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        handlePagination: function(pageNumber) {
            this.show = true;
            axios.get(`/openapi/product/all?page=${pageNumber}`)
                .then((res) => {
                    var data = res.data;
                    this.products = data.data;
                    this.pageCount = Math.ceil(data.total_page / data.per_page);
                    this.show = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
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
    }
</script>

<?php $this->endSection("js") ?>