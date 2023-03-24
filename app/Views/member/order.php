<?php $this->extend("layouts/app-member"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="mb-3 font-weight-medium"><?= lang('App.orderList') ?></h1>
    <v-card>
        <v-tabs color="primary">
            <v-tab>All <?= lang('App.order') ?></v-tab>
            <v-tab @click="getOrderPending">Pending</v-tab>
            <v-tab @click="getOrderProcessed">Processed</v-tab>
            <v-tab @click="getOrderDelivered">Delivered</v-tab>
            <v-tab @click="getOrderCanceled">Canceled</v-tab>
            <v-tab-item v-for="n in 5" :key="n">
                <v-card-text v-if="n==1">
                    <div v-if="loader == true">
                        <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                    </div>
                    <v-card class="mb-4" v-for="item in dataOrder" :key="item.order_id" v-if="loader == false">
                        <v-card-subtitle class="font-weight-medium">{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}} / {{item.no_order}} / {{item.email}} / {{item.created_at}}</v-card-subtitle>
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <?= lang('App.product'); ?>: {{item.qty}} <a @click="showOrder(item)"><?= lang('App.see'); ?></a><br />
                                    <?= lang('App.totalPrice'); ?>: Rp.{{item.total}}
                                </v-col>
                                <v-col>
                                    <?= lang('App.shipment'); ?>: {{item.shipment}}<br />
                                    <?= lang('App.payment'); ?>: {{item.payment_name}}
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="success" @click="modalAddOpen(item)" :loading="loading" :disabled="item.status!=0 ? true:false">
                                <?= lang('App.confirm') ?>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-card-text>
                <v-card-text v-if="n==2">
                    <div v-if="loader == true">
                        <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                    </div>
                    <v-card class="mb-4" v-for="item in dataPending" :key="item.order_id" v-if="loader == false">
                        <v-card-subtitle class="font-weight-medium">{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}} / {{item.no_order}} / {{item.email}} / {{item.created_at}}</v-card-subtitle>
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <?= lang('App.product'); ?>: {{item.qty}} <a @click="showOrder(item)"><?= lang('App.see'); ?></a><br />
                                    <?= lang('App.totalPrice'); ?>: Rp.{{item.total}}
                                </v-col>
                                <v-col>
                                    <?= lang('App.shipment'); ?>: {{item.shipment}}<br />
                                    <?= lang('App.payment'); ?>: {{item.payment_name}}
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="success" @click="modalAddOpen(item)" :loading="loading" :disabled="item.status!=0 ? true:false">
                                <?= lang('App.confirm') ?>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-card-text>
                <v-card-text v-if="n==3">
                <div v-if="loader == true">
                        <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                    </div>
                    <v-card class="mb-4" v-for="item in dataProcessed" :key="item.order_id" v-if="loader == false">
                        <v-card-subtitle class="font-weight-medium">{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}} / {{item.no_order}} / {{item.email}} / {{item.created_at}}</v-card-subtitle>
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <?= lang('App.product'); ?>: {{item.qty}} <a @click="showOrder(item)"><?= lang('App.see'); ?></a><br />
                                    <?= lang('App.totalPrice'); ?>: Rp.{{item.total}}
                                </v-col>
                                <v-col>
                                    <?= lang('App.shipment'); ?>: {{item.shipment}}<br />
                                    <?= lang('App.payment'); ?>: {{item.payment_name}}
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-card-text>
                <v-card-text v-if="n==4">
                    <div v-if="loader == true">
                        <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                    </div>
                    <v-card class="mb-4" v-for="item in dataDelivered" :key="item.order_id" v-if="loader == false">
                        <v-card-subtitle class="font-weight-medium">{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}} / {{item.no_order}} / {{item.email}} / {{item.created_at}}</v-card-subtitle>
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <?= lang('App.product'); ?>: {{item.qty}} <a @click="showOrder(item)"><?= lang('App.see'); ?></a><br />
                                    <?= lang('App.totalPrice'); ?>: Rp.{{item.total}}
                                </v-col>
                                <v-col>
                                    <?= lang('App.shipment'); ?>: {{item.shipment}}<br />
                                    <?= lang('App.payment'); ?>: {{item.payment_name}}
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-card-text>
                <v-card-text v-if="n==5">
                    <div v-if="loader == true">
                        <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                    </div>
                    <v-card class="mb-4" v-for="item in dataDelivered" :key="item.order_id" v-if="loader == false">
                        <v-card-subtitle class="font-weight-medium">{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}} / {{item.no_order}} / {{item.email}} / {{item.created_at}}</v-card-subtitle>
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <?= lang('App.product'); ?>: {{item.qty}} <a @click="showOrder(item)"><?= lang('App.see'); ?></a><br />
                                    <?= lang('App.totalPrice'); ?>: Rp.{{item.total}}
                                </v-col>
                                <v-col>
                                    <?= lang('App.shipment'); ?>: {{item.shipment}}<br />
                                    <?= lang('App.payment'); ?>: {{item.payment_name}}
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-card-text>
            </v-tab-item>
        </v-tabs>
    </v-card>
</template>

<!-- Modal Item Order -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalOrder" persistent width="600px">
            <v-card>
                <v-card-title class="text-h5"><?= lang('App.order') ?></v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th width="300">Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="show == true">
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                </tr>
                                <tr v-for="item in itemOrder" :key="item.cart_id" v-if="show == false">
                                    <td>{{item.product_name}}</td>
                                    <td>{{RibuanLocale(item.price)}}</td>
                                    <td>{{item.qty}}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large text @click="modalOrderClose"><?= lang('App.close') ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal -->

<!-- Modal Save -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent scrollable width="600px">
            <v-card>
                <v-card-title>
                    <?= lang('App.confirm') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <v-text-field label="<?= lang('App.bank') ?> *" v-model="bank" :error-messages="bankError" outlined></v-text-field>
                        <v-text-field label="<?= lang('App.nama') ?> *" v-model="nama" :error-messages="namaError" outlined></v-text-field>
                        <v-text-field label="<?= lang('App.norekening') ?> *" v-model="norekening" :error-messages="norekeningError" outlined></v-text-field>
                        <v-text-field label="<?= lang('App.nominal') ?> *" v-model="nominal" :error-messages="nominalError" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveConfirm" :loading="loading2">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
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
        dataOrder: [],
        dataPending: [],
        dataProcessed: [],
        dataDelivered: [],
        dataCanceled: [],
        itemOrder: [],
        modalAdd: false,
        modalOrder: false,
        idOrder: "",
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
        //Show Order
        showOrder: function(item) {
            this.loading3 = true;
            this.modalOrder = true;
            this.idOrder = item.order_id;
            setTimeout(() => this.getItemOrder(), 200);
        },
        modalOrderClose: function() {
            this.modalOrder = false;
            this.itemOrder = [];
        },
        //Get Item Order
        getItemOrder: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>api/cart/order/${this.idOrder}`, options)
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