<template>
    <v-text-field light v-model="search" v-on:keydown.enter="searchProduct" solo class="mt-7 mr-3" label="<?= lang('App.search'); ?>" prepend-inner-icon="mdi-magnify" :loading="loading3" clearable @click:clear="clearSearch"></v-text-field>
</template>

<?php $this->section("dialog") ?>
<?php $uri = new \CodeIgniter\HTTP\URI(current_url()); ?>
<?php if ($uri->getSegment(1) != "") : ?>
    <template>
        <v-row justify="center">
            <v-dialog v-model="modalSearch" fullscreen hide-overlay persistent scrollable>
                <v-card>
                    <v-toolbar class="mb-3">
                        <v-btn icon link href="<?= base_url(); ?>">
                            <v-icon>mdi-arrow-left</v-icon>
                        </v-btn>
                        <v-toolbar-title>
                            <?= lang('App.search'); ?>
                        </v-toolbar-title>
                        <v-spacer></v-spacer>

                        <v-toolbar-items>
                            <v-btn text @click="modalSearch = false">
                                <v-icon>mdi-close</v-icon> <?= lang('App.close') ?>
                            </v-btn>
                        </v-toolbar-items>
                    </v-toolbar>
                    <v-card-text class="py-5" style="height: 100vh;" v-if="result">
                        <v-container>
                            <h2 class="mb-3 font-weight-regular"><?= lang('App.searchResult'); ?> kata kunci: "{{search}}"</h2>
                            <v-divider></v-divider>
                            <v-row v-for="(item,i) in products" :key="i" class="mt-3 mb-3">
                                <v-col cols="12" md="2">
                                    <v-img aspect-ratio="1" lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                                    <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                </v-col>
                                <v-col cols="12" md="10">
                                    <h1 class="mb-3 font-weight-medium"><a :href="'<?= base_url(); ?>' + item.category_slug + '/' + item.slug">{{item.product_name}}</a></h1>
                                    <p class="text-h5 font-weight-medium mb-0">
                                        <span v-if="item.discount > 0">
                                            {{ RibuanLocale(item.product_price - item.discount) }}
                                        </span>
                                        <span v-else>{{ RibuanLocale(item.product_price) }}</span>
                                    </p>

                                    <span v-show="item.discount > 0">
                                        <p class="text-h6 font-weight-regular mb-0"><span class="text-decoration-line-through">{{ RibuanLocale(item.product_price) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.discount_percent}}%</v-chip></p>
                                    </span>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-row>
    </template>
<?php endif; ?>
<?php $this->endSection("dialog") ?>

<?php $this->section("js") ?>
<script>
    var errorKeys = []
    dataVue = {
        ...dataVue,
        modalSearch: false,
        result: false,
        search: null,
        products: [],
        pageCount: 0,
    }

    methodsVue = {
        ...methodsVue,
        // Search Data
        searchProduct: function() {
            if (this.search != '') {
                this.searchData();
            }
        },

        clearSearch: function() {
            this.getProducts();
            this.result = false;
            this.search = null;
        },

        // Search Data 
        searchData: function() {
            this.loading3 = true;
            this.show = true;
            axios.get(`<?= base_url(); ?>api/search?keyword=${this.search}`)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        <?php if ($uri->getSegment(1) != "") { ?>
                            this.modalSearch = true;
                        <?php } else { ?>
                            this.modalSearch = false;
                        <?php } ?>
                        this.products = data.data;
                        this.pageCount = 0;
                        this.result = true;
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalSearch = false;
                        this.products = data.data;
                        this.pageCount = 0;
                        this.result = true;
                        this.show = false;
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