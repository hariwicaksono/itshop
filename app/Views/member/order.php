<?php $this->extend("layouts/app-member"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="mb-2 font-weight-medium"><?= lang('App.orderList') ?></h1>
    <v-card>
        <v-tabs v-model="tabsMenu" color="primary">
            <v-tab href="#tab-semua">All <?= lang('App.order') ?></v-tab>
            <v-tab href="#tab-pending" @click="getOrderPending">Pending</v-tab>
            <v-tab href="#tab-process" @click="getOrderProcessed">Processed</v-tab>
            <v-tab href="#tab-deliver" @click="getOrderDelivered">Delivered</v-tab>
            <v-tab href="#tab-cancel" @click="getOrderCanceled">Canceled</v-tab>
            <v-tab-item v-model="tabsMenu">
                <v-tab-item value="tab-semua">
                    <v-card-text>
                        <div v-if="loader == true">
                            <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                        </div>
                        <v-card class="mb-4" v-for="item in dataOrder" :key="item.order_id" v-if="loader == false">
                            <v-card-subtitle><strong>{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}}</strong> / {{item.no_order}} / {{item.email}} / {{item.created_at}}
                            </v-card-subtitle>
                            <v-card-text>
                                <v-row>
                                    <v-col>
                                        <strong><?= lang('App.product'); ?></strong><br />

                                        <div v-if="show == true">
                                            <v-skeleton-loader type="list-item-avatar-three-line"></v-skeleton-loader>
                                        </div>
                                        <div v-for="row in itemOrder" :key="row.cart_id" v-if="show == false && item.order_id == row.order_id">
                                            <v-list-item class="ma-n3 pa-n3" two-line>
                                                <v-list-item-avatar size="50" rounded>
                                                    <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url() ?>' + row.media_path" v-if="row.media_path != null"></v-img>
                                                    <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                                </v-list-item-avatar>
                                                <v-list-item-content>
                                                    <p class="text-caption black--text">{{row.product_name}} - {{row.product_code ?? "-"}}</p>

                                                    <p v-if="row.discount > 0">
                                                        {{row.qty}} x <strong>{{ RibuanLocale(row.price - row.discount) }}</strong>
                                                        <span>
                                                            <span class="text-decoration-line-through">{{ RibuanLocale(row.price) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{row.discount_percent}}%</v-chip>
                                                        </span>
                                                    </p>
                                                    <p v-else>{{row.qty}} x <strong>{{ RibuanLocale(row.price) }}</strong></p>

                                                </v-list-item-content>
                                            </v-list-item>
                                        </div>
                                        <br />
                                        <em>Note: " {{item.note}} "</em>
                                    </v-col>
                                    <v-col>
                                        <strong><?= lang('App.shipment'); ?></strong><br />
                                        {{item.shipment}}<br />
                                        <strong><?= lang('App.payment'); ?></strong><br />
                                        {{item.payment_name}} / <em>{{item.status_payment}}</em>
                                        <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                                    <span class="text-h6"><strong>{{RibuanLocale(item.total)}}</strong></span>
                                </p>
                                    </v-col>
                                </v-row>
                                
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn outlined color="primary" @click="modalAddOpen(item)" :loading="loading" elevation="1" v-show="item.payment == 2 && item.status == 0">
                                    <v-icon>mdi-receipt-text</v-icon> <span class="grey--text text--darken-4"><?= lang('App.confirm') ?></span>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-card-text>
                </v-tab-item>
                <v-tab-item value="tab-pending">
                    <v-card-text>
                        <div v-if="loader == true">
                            <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                        </div>
                        <v-card class="mb-4" v-for="item in dataPending" :key="item.order_id" v-if="loader == false">
                            <v-card-subtitle><strong>{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}}</strong> / {{item.no_order}} / {{item.email}} / {{item.created_at}}
                            </v-card-subtitle>
                            <v-card-text>
                                <v-row>
                                    <v-col>
                                        <strong><?= lang('App.product'); ?></strong><br />
                                        {{item.qty}} x {{RibuanLocale(item.total)}} &nbsp;<a @click="showOrder(item)"><?= lang('App.see'); ?> Detail</a><br />
                                        <em>Note: " {{item.note}} "</em>
                                    </v-col>
                                    <v-col>
                                        <strong><?= lang('App.shipment'); ?></strong><br />
                                        {{item.shipment}}<br />
                                        <strong><?= lang('App.payment'); ?></strong><br />
                                        {{item.payment_name}} / <em>{{item.status_payment}}</em>
                                    </v-col>
                                </v-row>
                                <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                                    <span class="text-h6"><strong>{{RibuanLocale(item.total)}}</strong></span>
                                </p>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn outlined color="primary" @click="modalAddOpen(item)" :loading="loading" elevation="1" v-show="item.payment == 2 && item.status == 0">
                                    <v-icon>mdi-receipt-text</v-icon> <?= lang('App.confirm') ?>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-card-text>
                </v-tab-item>
                <v-tab-item value="tab-process">
                    <v-card-text>
                        <div v-if="loader == true">
                            <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                        </div>
                        <v-card class="mb-4" v-for="item in dataProcessed" :key="item.order_id" v-if="loader == false">
                            <v-card-subtitle><strong>{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}}</strong> / {{item.no_order}} / {{item.email}} / {{item.created_at}}
                            </v-card-subtitle>
                            <v-card-text>
                                <v-row>
                                    <v-col>
                                        <strong><?= lang('App.product'); ?></strong><br />
                                        {{item.qty}} x {{RibuanLocale(item.total)}} &nbsp;<a @click="showOrder(item)"><?= lang('App.see'); ?> Detail</a><br />
                                        <em>Note: " {{item.note}} "</em>
                                    </v-col>
                                    <v-col>
                                        <strong><?= lang('App.shipment'); ?></strong><br />
                                        {{item.shipment}}<br />
                                        <strong><?= lang('App.payment'); ?></strong><br />
                                        {{item.payment_name}} / <em>{{item.status_payment}}</em>
                                    </v-col>
                                </v-row>
                                <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                                    <span class="text-h6"><strong>{{RibuanLocale(item.total)}}</strong></span>
                                </p>
                            </v-card-text>
                        </v-card>
                    </v-card-text>
                </v-tab-item>
                <v-tab-item value="tab-deliver">
                    <v-card-text>
                        <div v-if="loader == true">
                            <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                        </div>
                        <v-card class="mb-4" v-for="item in dataDelivered" :key="item.order_id" v-if="loader == false">
                            <v-card-subtitle><strong>{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}}</strong> / {{item.no_order}} / {{item.email}} / {{item.created_at}}
                            </v-card-subtitle>
                            <v-card-text>
                                <v-row>
                                    <v-col>
                                        <strong><?= lang('App.product'); ?></strong><br />
                                        {{item.qty}} x {{RibuanLocale(item.total)}} &nbsp;<a @click="showOrder(item)"><?= lang('App.see'); ?> Detail</a><br />
                                        <em>Note: " {{item.note}} "</em>
                                    </v-col>
                                    <v-col>
                                        <strong><?= lang('App.shipment'); ?></strong><br />
                                        {{item.shipment}}<br />
                                        <strong><?= lang('App.payment'); ?></strong><br />
                                        {{item.payment_name}} / <em>{{item.status_payment}}</em>
                                    </v-col>
                                </v-row>
                                <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                                    <span class="text-h6"><strong>{{RibuanLocale(item.total)}}</strong></span>
                                </p>
                            </v-card-text>
                        </v-card>
                    </v-card-text>
                </v-tab-item>
                <v-tab-item value="tab-cancel">
                    <v-card-text>
                        <div v-if="loader == true">
                            <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                        </div>
                        <v-card class="mb-4" v-for="item in dataCanceled" :key="item.order_id" v-if="loader == false">
                            <v-card-subtitle><strong>{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}}</strong> / {{item.no_order}} / {{item.email}} / {{item.created_at}}
                            </v-card-subtitle>
                            <v-card-text>
                                <v-row>
                                    <v-col>
                                        <strong><?= lang('App.product'); ?></strong><br />
                                        {{item.qty}} x {{RibuanLocale(item.total)}} &nbsp;<a @click="showOrder(item)"><?= lang('App.see'); ?> Detail</a><br />
                                        <em>Note: " {{item.note}} "</em>
                                    </v-col>
                                    <v-col>
                                        <strong><?= lang('App.shipment'); ?></strong><br />
                                        {{item.shipment}}<br />
                                        <strong><?= lang('App.payment'); ?></strong><br />
                                        {{item.payment_name}} / <em>{{item.status_payment}}</em>
                                    </v-col>
                                </v-row>
                                <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                                    <span class="text-h6"><strong>{{RibuanLocale(item.total)}}</strong></span>
                                </p>
                            </v-card-text>
                        </v-card>
                    </v-card-text>
                </v-tab-item>
            </v-tab-item>
        </v-tabs>
    </v-card>
</template>

<!-- Modal Save -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent scrollable width="900px">
            <v-card>
                <v-card-title>
                    <?= lang('App.confirm') ?> Manual #{{ noOrder }}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-row>
                        <v-col cols="12" md="8">
                            <v-form ref="form" v-model="valid">
                                <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                                <v-text-field label="Bank *" v-model="bank" :error-messages="bankError" outlined></v-text-field>
                                <v-text-field label="Nama Rekening *" v-model="nama" :error-messages="namaError" outlined></v-text-field>
                                <v-text-field label="Nomor Rekening *" v-model="norekening" :error-messages="norekeningError" outlined></v-text-field>
                                <v-text-field label="Nominal (Rp) *" v-model="nominal" :error-messages="nominalError" outlined></v-text-field>
                            </v-form>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-alert color="yellow lighten-2" icon="mdi-information" light class="text-body-2 mt-2" @click="">
                                Informasi Bank Transfer!<br /><br />
                                <strong><?= $bank['payment']; ?></strong><br />
                                No. Rekening: <strong><?= $bank['number']; ?></strong><br />
                                A.N: <?= $bank['account']; ?>
                            </v-alert>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveConfirm" :loading="loading2">
                        <v-icon>mdi-send</v-icon> <?= lang('App.send') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Save -->

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
        tabsMenu: "tab-semua",
        dataOrder: [],
        dataPending: [],
        dataProcessed: [],
        dataDelivered: [],
        dataCanceled: [],
        itemOrder: [],
        modalAdd: false,
        modalOrder: false,
        idOrder: "",
        noOrder: "",
        idPayment: "",
        bank: "",
        bankError: "",
        nama: "",
        namaError: "",
        norekening: "",
        norekeningError: "",
        nominal: "",
        nominalError: ""
    }

    createdVue = function() {
        this.getOrders();
    }

    methodsVue = {
        ...methodsVue,
        // Get Order
        getOrders: function() {
            this.loader = true;
            axios.get('<?= base_url() ?>api/order/user/<?= session()->get('id'); ?>', options)
                .then(res => {
                    // handle success
                    this.loader = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataOrder = data.data;
                        this.getItemOrder();
                        //console.log(this.dataOrder);
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
        getOrderPending: function() {
            this.loader = true;
            axios.get('<?= base_url() ?>api/order/pending/<?= session()->get('id'); ?>', options)
                .then(res => {
                    // handle success
                    this.loader = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPending = data.data;
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
        getOrderProcessed: function() {
            this.loader = true;
            axios.get('<?= base_url() ?>api/order/processed/<?= session()->get('id'); ?>', options)
                .then(res => {
                    // handle success
                    this.loader = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataProcessed = data.data;
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
        getOrderDelivered: function() {
            this.loader = true;
            axios.get('<?= base_url() ?>api/order/delivered/<?= session()->get('id'); ?>', options)
                .then(res => {
                    // handle success
                    this.loader = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataDelivered = data.data;
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
        getOrderCanceled: function() {
            this.loader = true;
            axios.get('<?= base_url() ?>api/order/canceled/<?= session()->get('id'); ?>', options)
                .then(res => {
                    // handle success
                    this.loader = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataCanceled = data.data;
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

        //Get Item Order
        getItemOrder: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>api/cart/user/orderitem`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.itemOrder = data.data;
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.itemOrder = data.data;
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
        modalAddOpen: function(item) {
            this.modalAdd = true;
            this.notifType = '';
            this.idOrder = item.order_id;
            this.noOrder = item.no_order;
            this.idPayment = item.payment_id;
            this.nominal = item.total;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },
        // Save
        saveConfirm: function() {
            this.loading2 = true;
            axios.post(`<?= base_url() ?>api/payment/confirm`, {
                    order_id: this.idOrder,
                    payment_id: this.idPayment,
                    bank: this.bank,
                    nama: this.nama,
                    norekening: this.norekening,
                    nominal: this.nominal,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalAdd = false;
                        this.getOrders();
                        this.order_id = "";
                        this.payment_id = "";
                        this.bank = "";
                        this.nama = "";
                        this.norekening = "";
                        this.nominal = "";
                        this.$refs.form.resetValidation();
                    } else {
                        this.modalAdd = true;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false;
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