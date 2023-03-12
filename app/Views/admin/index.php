<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<v-row>
    <v-col lg="4" cols="sm" class="pb-2">
        <v-card elevation="2">
            <v-row class="no-gutters">
                <div class="col-auto">
                    <div class="blue fill-height">&nbsp;</div>
                </div>
                <div class="col pa-3 py-4">
                    <h3 class="text-truncate text-uppercase"><?= lang('App.product') ?></h3>
                    <h1 class="text-h3"><?= $jmlProduct; ?></h1>
                </div>
            </v-row>
        </v-card>
    </v-col>
    <v-col lg="4" cols="sm" class="pb-2">
        <v-card elevation="2">
            <v-row class="no-gutters">
                <div class="col-auto">
                    <div class="warning fill-height">&nbsp;</div>
                </div>
                <div class="col pa-3 py-4">
                    <h3 class="text-truncate text-uppercase">User</h3>
                    <h1 class="text-h3"><?= $jmlUser; ?></h1>
                </div>
            </v-row>
    </v-col>
    <v-col lg="4" cols="sm" class="pb-2">
        <v-card elevation="2">
            <v-row class="no-gutters">
                <div class="col-auto">
                    <div class="green fill-height">&nbsp;</div>
                </div>
                <div class="col pa-3 py-4">
                    <h3 class="text-truncate text-uppercase"><?= lang('App.order'); ?></h3>
                    <h1 class="text-h3"><?= $jmlOrder; ?></h1>
                </div>
            </v-row>
        </v-card>
    </v-col>
</v-row>

<template>
    <v-card class="mx-auto text-center mt-5" elevation="1">
        <v-card-title class="primary--text">
            <?= lang('App.sales') ?>
        </v-card-title>
        <v-sparkline :value="sparklineData" :labels="sparklineLabel" padding="18" label-size="4" color="cyan" :gradient="['#007bff','cyan']" :line-width="2" :stroke-linecap="'round'">
        </v-sparkline>
        
    </v-card>
</template>

<!--<template>
<h1 class="mt-4 mb-3 font-weight-regular"><?= lang('App.latestProduct') ?></h1>
<v-row v-if="show == true" class="mb-2">
    <v-col v-for="n in 4" :key="n" cols="12" sm="3">
        <v-card elevation="1">
            <v-card-text>
                <v-skeleton-loader class="mx-auto" max-width="300" type="paragraph, heading"></v-skeleton-loader>
            </v-card-text>
        </v-card>
    </v-col>
</v-row>
<v-row v-if="show == false" class="mb-2">
    <v-col v-for="item in products" :key="item.product_id">
        <v-card elevation="1">
            <v-card-title>{{ item.product_name }}</v-card-title>
            <v-card-text>
                Rp.{{ item.product_price }}
            </v-card-text>
        </v-card>
    </v-col>
</v-row>

<paginate :page-count="pageCount" :no-li-surround="true" :container-class="'v-pagination theme--light'" :page-link-class="'v-pagination__item v-btn primary--text'" :active-class="'v-pagination__item--active primary white--text'" :disabled-class="'v-pagination__navigation--disabled'" :prev-link-class="'v-pagination__navigation'" :next-link-class="'v-pagination__navigation'" :click-handler="handlePagination">
</paginate>
</template>-->
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    dataVue = {
        ...dataVue,
        products: [],
        sparklineLabel: [],
        sparklineData: [],
        pageCount: 0,
        currentPage: 1,
    }
    createdVue = function() {
        //this.getProducts();
        this.getChart1();
    }
    methodsVue = {
        ...methodsVue,
        getChart1: function() {
                this.show = true;
                axios.get(`<?= base_url()?>api/chart1`, options)
                    .then(res => {
                        // handle success
                        var data = res.data;
                        if (data.expired == true) {
                            this.snackbar = true;
                            this.snackbarMessage = data.message;
                            setTimeout(() => window.location.href = data.data.url, 1000);
                        }
                        if (data.status == true) {
                            var datas = data.data;
                            this.sparklineLabel = datas.map(x => (x.tahun));
                            this.sparklineData = datas.map(x => (parseInt(x.jumlah)));
                            console.log(this.sparklineLabel)
                            console.log(this.sparklineData)
                        } else {
                            this.snackbar = true;
                            this.snackbarMessage = data.message;
                        }
                    })
                    .catch(err => {
                        // handle error
                        console.log(err.response);
                    })
            },
        // Get Product
        getProducts: function() {
            this.show = true;
            axios.get(`<?= base_url()?>api/product/all?page=${this.currentPage}`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarType = "warning";
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.products = data.data;
                        this.pageCount = Math.ceil(data.total_page / data.per_page);
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarType = "warning";
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        handlePagination: function(pageNumber) {
            this.show = true;
            axios.get(`<?= base_url()?>api/product/all?page=${pageNumber}`, options)
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
        }
    }
</script>
<?php $this->endSection("js") ?>