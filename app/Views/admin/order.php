<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-regular"><?= lang('App.orderList') ?></h1>
<v-row class="fill-height">
    <v-col>
        <!-- Table List Order -->
        <v-card outlined elevation="1">
            <v-card-title>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
                </v-text-field>
            </v-card-title>

            <v-data-table :headers="tbheader" :items="dataOrder" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="Sedang memuat... Harap tunggu">
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{item.order_id}}</td>
                        <td>{{item.no_order}}</td>
                        <td>{{item.username}}/{{item.email}}</td>
                        <td>{{item.created_at}}</td>
                        <td>{{item.qty}}</td>
                        <td>Rp.{{item.total}}</td>
                        <td>{{item.payment}} <a @click="showConfirmation(item)"><?= lang('App.see');?></a></td>
                        <td>{{item.shipment}}</td>
                        <td>
                            <v-select v-model="item.status" name="status" :items="list_status" item-text="label" item-value="value" label="Select Status" single-line @change="setStatus(item)"></v-select>
                        </td>
                        <td>
                            <v-btn icon color="primary" class="mr-2" @click="showOrder(item)">
                                <v-icon>mdi-receipt</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>              
            </v-data-table>
        </v-card>                     
        <!-- End Table List -->

    </v-col>
</v-row>

<!-- Modal Item Order -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalOrder" persistent width="600px">
            <v-card class="pa-2">
                <v-card-title class="text-h5"><?= lang('App.order') ?></v-card-title>
                <v-card-text>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left">
                                        Product
                                    </th>
                                    <th class="text-left">
                                        Price
                                    </th>
                                    <th class="text-left">
                                        Qty
                                    </th>
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
                                    <td>{{item.price}}</td>
                                    <td>{{item.qty}}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="modalOrderClose"><?= lang('App.close') ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>              
<!-- End Modal -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalConfirm" persistent scrollable width="600px">
            <v-card>
                <v-card-title>
                    <?= lang('App.confirm') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalConfirmationClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    BANK: {{bank}}<br/>
                    {{nama}}<br/>
                    {{norekening}}<br/>
                    Tanggal: {{tanggal}}<br/>
                    Rp.{{nominal}}
                </v-card-text>
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
        search: '',
        tbheader: [{
                text: 'ID',
                value: 'order_id'
            },
            {
                text: 'No.Order',
                value: 'no_order'
            },
            {
                text: 'User',
                value: 'email'
            },
            {
                text: 'Tanggal',
                value: 'tgl_input'
            },
            {
                text: 'Qty',
                value: 'qty'
            },
            {
                text: 'Total',
                value: 'total'
            },
            {
                text: '<?= lang('App.payment'); ?>',
                value: 'payment'
            },
            {
                text: '<?= lang('App.shipment'); ?>',
                value: 'shipment'
            },
            {
                text: '<?= lang('App.status'); ?>',
                value: 'status'
            },
            {
                text: '<?= lang('App.action') ?>',
                value: 'actions',
                sortable: false
            },
        ],
        list_status: [{
                label: 'Pending',
                value: '0'
            },
            {
                label: 'Deliver',
                value: '1'
            },
            {
                label: 'Cancel',
                value: '2'
            },
        ],
        dataOrder: [],
        itemOrder: [],
        noOrder: "",
        idOrder: "",
        qty: "",
        total: "",
        payment: "",
        shipment: "",
        status: "",
        modalEdit: false,
        modalOrder: false,
        modalConfirm: false,
        bank: "",
        nama: "",
        norekening: "",
        nominal: "",
        tanggal: "",
    }

    createdVue = function() {
        this.getOrder();
    }

    methodsVue = {
        ...methodsVue,
        // Get Order
        getOrder: function() {
            this.loading = true;
            axios.get('/api/order', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataOrder = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
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
            axios.get(`/api/cart/order/${this.idOrder}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
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
                })
        },
        // Set Status
        setStatus: function(item) {
            this.loading = true;
            this.idOrder = item.order_id;
            this.status = item.status;
            axios.put(`/api/order/setstatus/${this.idOrder}`, {
                    status: this.status,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getOrder();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },
        //Show Payment Confirm
        showConfirmation: function(item) {
            this.loading3 = true;
            this.modalConfirm = true;
            this.idOrder = item.order_id;
            setTimeout(() => this.getConfirmation(), 200);
        },
        modalConfirmationClose: function() {
            this.modalConfirm = false;
            this.dataConfirm = [];
        },
        //Get
        getConfirmation: function() {
            this.show = true;
            axios.get(`/api/payment/get/${this.idOrder}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.dataConfirm = data.data;
                        this.bank = this.dataConfirm.bank;
                        this.nama = this.dataConfirm.nama;
                        this.norekening = this.dataConfirm.norekening;
                        this.nominal = this.dataConfirm.nominal;
                        this.tanggal = this.dataConfirm.created_at;
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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