<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>


<template>
    <v-container>


        <div>
            <h1 class="text-h4 mb-3">{{products.product_name}}</h1>
            <v-chip class="mb-3">Rp {{products.product_price}}</v-chip>
            <h4 class="mb-3">Cover:</h4>
            <v-img :src="'<?= base_url() ?>' + products.media_path" width="200" class="mb-3" title="" alt="" v-if="products.media_path != null"></v-img>
            <v-img src="<?= base_url('images/no_image.jpg') ?>" width="200" class="mb-3" v-else></v-img>
            <h4 class="mb-3">Deskripsi:</h4>
            <p>{{products.product_description}}</p>

        </div>
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
    computedVue = {
        ...computedVue,
    }
    createdVue = function() {
        this.getProduct();
    }
    dataVue = {
        ...dataVue,
        dialog: false,
        idProduct: '<?= $product_id; ?>',
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
        getProduct: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>openapi/product/${this.idProduct}`)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    this.products = data.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.products = data.data;
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