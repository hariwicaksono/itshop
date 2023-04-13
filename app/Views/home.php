<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="mt-3">
        <v-parallax src="<?= base_url() ?>/images/Banner-2023-2.jpg" dark class="rounded-lg" height="350">
            <v-row align="center" justify="center">
                <v-col class="text-center" cols="12">
                    <h1 class="text-h4 font-weight-thin mt-n15 mb-3">
                        Welcome to<br /><strong><?= COMPANY_NAME; ?></strong>
                    </h1>
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
        <v-row v-if="show == false" v-masonry transition-duration="0.3s" item-selector=".item" class="masonry-container" v-if="show == false">
            <v-col v-masonry-tile class="item" v-for="item in products" :key="item.product_id" cols="12" md="4">
                <v-card min-height="150">
                    <v-img v-bind:src="'<?= base_url(); ?>' + item.media_path" height="380px"></v-img>
                    <v-card-title class="subtitle-1"><a link :href="'<?= base_url('source-code/'); ?>' + item.slug" class="font-weight-regular text-decoration-none" title="" alt="">{{ item.product_name }}</a></v-card-title>
                    <v-card-subtitle class="text-h6 grey--text text--darken-4 font-weight-bold" v-if="item.discount > 0">
                        <v-btn class="float-end" color="primary" @click="saveCart(item)" elevation="1">
                            <v-icon>mdi-cart</v-icon> <?= lang('App.buy'); ?>
                        </v-btn>
                        <span v-if="item.discount > 0">
                            {{ RibuanLocale(item.product_price - item.discount) }}
                        </span>
                        <span v-else>{{ item.product_price }}</span>
                        <span v-show="item.discount > 0">
                            <p class="text-body-2 mb-0"><span class="text-decoration-line-through">{{ RibuanLocale(item.product_price) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.discount_percent}}%</v-chip></p>
                        </span>
                    </v-card-subtitle>
                </v-card>
            </v-col>
        </v-row>
        <br />
        <paginate class="mb-5" :page-count="pageCount" :no-li-surround="true" :container-class="'v-pagination theme--light'" :page-link-class="'v-pagination__item v-btn'" :active-class="'v-pagination__item--active primary'" :disabled-class="'v-pagination__navigation--disabled'" :prev-link-class="'v-pagination__navigation'" :next-link-class="'v-pagination__navigation'" :prev-text="'<small>Prev</small>'" :next-text="'<small>Next</small>'" :click-handler="handlePagination">
        </paginate>
    </v-container>
    <br />
</template>

<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" scrollable width="600px">
            <v-card>
                <v-card-actions>
                    <v-card-title>Fitur &amp; Teknologi</v-card-title>
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
            <?= lang('App.loadingWait'); ?>
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
            axios.get(`<?= base_url() ?>openapi/product/all?page=${this.currentPage}`)
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
            axios.get(`<?= base_url() ?>openapi/product/all?page=${pageNumber}`)
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
    }
</script>

<?php $this->endSection("js") ?>