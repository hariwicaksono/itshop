<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="mt-3">
        <v-parallax src="<?= base_url() ?>/images/Banner-2023-2.jpg" dark class="rounded-lg" height="350">
            <v-row align="center" justify="center">
                <v-col class="text-center" cols="12">
                    <h1 class="text-h5 font-weight-thin mt-n15 mb-0">
                        <?= lang('App.welcome'); ?>
                    </h1>
                    <?= $company_name; ?>
                    <h1 class="text-h4 font-weight-medium mb-3">
                        <strong><?= $app_name; ?></strong>
                    </h1>
                </v-col>
            </v-row>
        </v-parallax>
    </v-container>
</template>

<template>
    <v-container>
        <h1 class="mb-3"><?= lang('App.product') ?></h1>
        <!-- Tampil Hasil Pencarian -->
        <h2 class="text-h5 font-weight-regular mb-5" v-show="result == true"><?= lang('App.searchResult'); ?>: "{{search}}" &nbsp;<v-btn small @click="clearSearch" title="Clear" alt="Clear"><v-icon small color="error">mdi-eraser</v-icon> Clear</v-btn></h2>

        <v-row>
            <v-col cols="12" md="3">
                <v-card class="mb-3">
                    <v-card-text>
                        <div class="mb-3">
                            <h3 class="mb-0">Urutkan Produk:</h3>
                            <v-select v-model="orderBy" :items="dataSort" label="" @change="getProducts"></v-select>
                        </div>

                        <div class="mb-3">
                            <h3 class="mb-3">Filter <?= lang('App.category'); ?>:</h3>
                            <v-select v-model="selectedCategory" label="Data <?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" multiple chips attach hide-details>
                                <template v-slot:prepend-item>
                                    <v-list-item ripple @mousedown.prevent @click="toggle">
                                        <v-list-item-action>
                                            <v-icon :color="selectedCategory.length > 0 ? 'primary darken-4' : ''">
                                                {{ icon }}
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Select All
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2"></v-divider>
                                </template>
                            </v-select>
                        </div>

                        <br />

                        <h3 class="mb-3">Show:</h3>
                        <v-btn @click="limitPage8" elevation="0" :color="activeColor1">8</v-btn>
                        <v-btn @click="limitPage32" elevation="0" :color="activeColor2">32</v-btn>
                        <v-btn @click="limitPage64" elevation="0" :color="activeColor3">64</v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="9">
                <v-row v-if="show == true">
                    <v-col v-for="n in 8" :key="n" cols="12" md="4">
                        <v-card outlined elevation="1">
                            <v-skeleton-loader class="mx-auto" max-width="300" type="card"></v-skeleton-loader>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row v-masonry transition-duration="0.3s" item-selector=".item" class="masonry-container" v-if="show == false">
                    <v-col v-masonry-tile class="item" v-for="item in products" :key="item.product_id" cols="12" sm="4">
                        <v-card min-height="400">
                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url(); ?>' + item.media_path" aspect-ratio="1" v-if="item.media_path != null">
                                <v-overlay absolute="true" v-if="item.active == '0'">
                                    <v-chip>
                                        <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.notAvailable'); ?>
                                    </v-chip>
                                </v-overlay>
                                <v-overlay absolute="true" v-else-if="item.stock == '0'">
                                    <v-chip>
                                        <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.outofStock'); ?>
                                    </v-chip>
                                </v-overlay>
                            </v-img>
                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" src="<?= base_url('images/no_image.jpg') ?>" v-else>
                                <v-overlay absolute="true" v-if="item.active == '0'">
                                    <v-chip>
                                        <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.notAvailable'); ?>
                                    </v-chip>
                                </v-overlay>
                                <v-overlay absolute="true" v-else-if="item.stock == '0'">
                                    <v-chip>
                                        <v-icon small>mdi-alert-circle-outline</v-icon> <?= lang('App.outofStock'); ?>
                                    </v-chip>
                                </v-overlay>
                            </v-img>
                            <v-card-title class="subtitle-1 font-weight-medium">
                                <a link :href="'<?= base_url(); ?>' + item.category_slug + '/' + item.slug" class="text-decoration-none" :title="item.product_name" :alt="item.product_name">{{ item.product_name }}</a>
                            </v-card-title>
                            <v-card-subtitle class="text-h6 font-weight-bold">
                                <span v-if="item.discount > 0">
                                    {{ RibuanLocale(item.product_price - item.discount) }}
                                </span>
                                <span v-else>{{ RibuanLocale(item.product_price) }}</span>

                                <span v-show="item.discount > 0">
                                    <p class="text-body-2 mb-0"><span class="text-decoration-line-through">{{ RibuanLocale(item.product_price) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.discount_percent}}%</v-chip></p>
                                </span>
                            </v-card-subtitle>
                            <v-card-actions>
                                <v-btn color="success" @click="sendWhatsApp(item)" elevation="1" :disabled="item.stock == 0 || item.active == 0">
                                    <v-icon>mdi-whatsapp</v-icon> Chat <span class="d-flex d-sm-none d-md-none d-lg-flex d-xl-flex">WhatsApp</span>
                                </v-btn>
                                <v-spacer></v-spacer>
                                <v-btn color="primary" @click="saveCart(item)" elevation="1" :disabled="item.stock == 0 || item.active == 0">
                                    <v-icon>mdi-cart</v-icon> <span class="d-flex d-sm-none d-md-none d-lg-flex d-xl-flex"><?= lang('App.buy'); ?></span>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <div class="text-center" v-show="products == '' && show == false">
                    <h1 class="font-weight-medium mb-3">No Products Found</h1>
                    <v-icon color="yellow darken-3" size="256">mdi-package-variant-remove</v-icon>
                    <h2 class="font-weight-regular mt-5">Sorry! The product you were looking for is unavailable.<br />Please try again later.</h2>
                </div>

                <br /><br />

                <paginate class="mb-5" :page-count="pageCount" :no-li-surround="true" :container-class="'v-pagination theme--light'" :page-link-class="'v-pagination__item v-btn'" :active-class="'v-pagination__item--active primary'" :disabled-class="'v-pagination__navigation--disabled'" :prev-link-class="'v-pagination__navigation'" :next-link-class="'v-pagination__navigation'" :prev-text="'<small>Prev</small>'" :next-text="'<small>Next</small>'" :click-handler="handlePagination">
                </paginate>
            </v-col>
        </v-row>

        <v-divider class="mb-4"></v-divider>

        <h1 class="mb-3"><?= lang('App.article'); ?></h1>
        <v-row>
            <v-col cols="12" sm="6">
                <v-select v-model="categoryArticles" label="Filter <?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" @change="getArticles" multiple attach hide-details></v-select>
            </v-col>
            <v-col cols="12" sm="6">
                <v-select v-model="orderByArticles" :items="dataSortArticles" label="Urutkan <?= lang('App.article'); ?>" @change="getArticles" hide-details></v-select>
            </v-col>
        </v-row>
        <div v-if="loading2 == true">
            <v-skeleton-loader type="list-item-avatar-three-line" class="mb-3"></v-skeleton-loader>
            <v-divider></v-divider>
            <v-skeleton-loader type="list-item-avatar-three-line" class="mb-3"></v-skeleton-loader>
            <v-divider></v-divider>
        </div>
        <v-list two-line v-else>
            <template v-for="(item, index) in articles">
                <v-list-item>
                    <v-list-item-avatar size="150" rounded>
                        <v-img lazy-src="<?= base_url('images/no-image.png') ?>" :src="'<?= base_url(); ?>' + item.media_path" aspect-ratio="1" v-if="item.media_path != null"></v-img>
                        <v-img lazy-src="<?= base_url('images/no-image.png') ?>" src="<?= base_url('images/no-image.png') ?>" v-else></v-img>
                    </v-list-item-avatar>

                    <v-list-item-content>
                        <v-list-item-title>
                            <a class="text-h4 text-capitalize text-decoration-none" :href="'<?= base_url('read/'); ?>' + item.category_slug + '/' + item.slug">{{item.article_title}}</a>
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            <h6 class="text-h6 mb-2 font-weight-light">{{item.article_headline}}</h6>
                        </v-list-item-subtitle>
                        <v-list-item-subtitle>
                            <p>{{dayjs(item.created_at).fromNow()}} &mdash; <v-icon small>mdi-tag</v-icon> {{item.category_name}} &mdash; <v-icon small>mdi-account</v-icon> {{item.first_name}} {{item.last_name}} <v-icon small color="primary" v-show="item.role == '1'" title="Official Account" alt="Official Account">mdi-check-decagram</v-icon></p>
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
            </template>
        </v-list>
        <div class="text-center" v-show="articles == '' && loading2 == false">
            <h1 class="font-weight-medium mb-3">No Articles Found</h1>
            <v-icon color="yellow darken-3" size="100">mdi-file-document-remove</v-icon>
            <h2 class="font-weight-regular mt-5">Sorry! The articles you were looking for is unavailable.<br />Please try again later.</h2>
        </div>
        <paginate class="mb-5" :page-count="pageCount1" :no-li-surround="true" :container-class="'v-pagination theme--light'" :page-link-class="'v-pagination__item v-btn'" :active-class="'v-pagination__item--active primary'" :disabled-class="'v-pagination__navigation--disabled'" :prev-link-class="'v-pagination__navigation'" :next-link-class="'v-pagination__navigation'" :prev-text="'<small>Prev</small>'" :next-text="'<small>Next</small>'" :click-handler="pagingArticles">
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
    // Mendapatkan Token JWT
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    }

    // Initial Data
    dataVue = {
        ...dataVue,
        dialog: false,
        products: [],
        pageCount: 0,
        pageCount1: 0,
        currentPage: 1,
        limitPage: 8,
        activeColor1: "primary",
        activeColor2: "",
        activeColor3: "",
        dataSort: [{
            text: 'Terbaru',
            value: 'created_new'
        }, {
            text: 'Terlama',
            value: 'created_old'
        }, {
            text: 'Termurah',
            value: 'price_asc'
        }, {
            text: 'Termahal',
            value: 'price_desc'
        }, ],
        orderBy: "created_new",
        messageNoData: "",
        dataCategory: [],
        selectedCategory: [],

        articles: [],
        dataSortArticles: [{
            text: 'Terbaru',
            value: 'created_new'
        }, {
            text: 'Terlama',
            value: 'created_old'
        }, ],
        orderByArticles: "created_new",
        categoryArticles: [],
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getProducts();
        this.getCategory();
        this.getArticles();
    }

    // Vue Computed
    // Computed: Properti-properti terolah (computed) yang kemudian digabung kedalam Vue instance
    computedVue = {
        ...computedVue,
        likesAllCategory() {
            return this.selectedCategory.length === this.dataCategory.length
        },
        likesSomeCategory() {
            return this.selectedCategory.length > 0 && !this.likesAllCategory
        },
        icon() {
            if (this.likesAllCategory) return 'mdi-close-box'
            if (this.likesSomeCategory) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
        },
    }

    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    var watchVue = {
        selectedCategory: function() {
            if (this.selectedCategory != '') {
                this.getProducts();
            }
        },
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        // Toggle Select All
        toggle() {
            this.$nextTick(() => {
                if (this.likesAllCategory) {
                    this.selectedCategory = []
                } else {
                    this.dataCategory.map((el) => {
                        const idC = el.category_id;
                        const combinedC = [].concat(idC);
                        this.selectedCategory.push(...combinedC);
                    });
                }
            })
        },

        // Limit Data Barang
        limitPage8: function() {
            this.limitPage = 8;
            this.activeColor1 = "primary";
            this.activeColor2 = "";
            this.activeColor3 = "";
            this.getProducts();
        },
        limitPage32: function() {
            this.limitPage = 32;
            this.activeColor1 = "";
            this.activeColor2 = "primary";
            this.activeColor3 = "";
            this.getProducts();
        },
        limitPage64: function() {
            this.limitPage = 64;
            this.activeColor1 = "";
            this.activeColor2 = "";
            this.activeColor3 = "primary";
            this.getProducts();
        },

        // Get Product
        getProducts: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>openapi/product/all?page=${this.currentPage}&limit=${this.limitPage}&category=${this.selectedCategory}&sort_by=${this.orderBy}`)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.products = data.data;
                        this.pageCount = Math.ceil(data.total_page / data.per_page);
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.products = data.data;
                        this.show = false;
                    }

                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        handlePagination: function(pageNumber) {
            this.show = true;
            axios.get(`<?= base_url() ?>openapi/product/all?page=${pageNumber}&limit=${this.limitPage}&sort_by=${this.orderBy}`)
                .then((res) => {
                    var data = res.data;
                    if (data.status == true) {
                        this.products = data.data;
                        this.pageCount = Math.ceil(data.total_page / data.per_page);
                        this.show = false;
                    } else {
                        this.products = data.data;
                        this.show = false;
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
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getCartCount();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
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

        // Get Category
        getCategory: function() {
            this.loading1 = true;
            axios.get('<?= base_url(); ?>openapi/category', options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataCategory = data.data;
                        this.dataCategory.map((el) => {
                            const idC = el.category_id;
                            const combinedC = [].concat(idC);
                            this.selectedCategory.push(...combinedC);
                        });
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataCategory = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading1 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // send WhatsApp (Wa.me)
        sendWhatsApp: function(item) {
            let encoded = encodeURIComponent('<?= $wa_text; ?> ' + item.category_name + ': ' + item.product_name);
            setTimeout(() => window.location.href = `https://wa.me/<?= $telepon; ?>?text=${encoded}`, 100);
        },

        // Get Articles
        getArticles: function() {
            this.loading2 = true;
            axios.get(`<?= base_url(); ?>openapi/articles/all?page=${this.currentPage}&limit=${this.limitPage}&category=${this.categoryArticles}&sort_by=${this.orderByArticles}`, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.articles = data.data;
                        this.pageCount1 = Math.ceil(data.total_page / data.per_page);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.articles = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading2 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },
        pagingArticles: function(pageNumber) {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>openapi/articles/all?page=${pageNumber}&limit=${this.limitPage}&sort_by=${this.orderByArticles}`)
                .then((res) => {
                    var data = res.data;
                    if (data.status == true) {
                        this.articles = data.data;
                        this.pageCount1 = Math.ceil(data.total_page / data.per_page);
                        this.loading2 = false;
                    } else {
                        this.articles = data.data;
                        this.loading2 = false;
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