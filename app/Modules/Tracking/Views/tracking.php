<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-medium"><?= lang('App.orderList') ?></h1>
<v-row>
    <v-col>
        <!-- Table List Order -->
        <v-card outlined elevation="1">
            <v-card-title>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" v-on:keydown.enter="getOrder" @click:clear="getOrder" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
                </v-text-field>
            </v-card-title>
            <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading">
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{item.no_order}}</td>
                        <td>{{item.email}}<br />{{item.phone}}</td>
                        <td>{{item.created_at}}</td>
                        <td>{{RibuanLocale(item.total)}}</td>
                        <td>
                            {{item.payment_name}}
                            <a @click="showConfirmation(item)" v-show="item.payment == '2'"><?= lang('App.see'); ?></a>
                        </td>
                        <td>
                            <v-select v-model="item.status" name="status" :items="list_status" item-text="label" item-value="value" label="Select Status" single-line @change="setStatus(item)"></v-select>
                        </td>
                        <td>
                            <v-select v-model="item.status_payment" name="status_payment" :items="list_payment" item-text="label" item-value="value" label="Select Status Payment" single-line @change="setStatusPayment(item)"></v-select>
                        </td>
                        <td>
                            <v-btn icon color="primary" class="mr-2" @click="showOrder(item)" title="Detail" alt="Detail">
                                <v-icon>mdi-information-outline</v-icon>
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
        <v-dialog v-model="modalOrder" scrollable persistent width="1000px">
            <v-card>
                <v-card-title>
                    <?= lang('App.order') ?> #{{noOrder}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalOrderClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5 mb-3">
                    <div v-for="item in itemOrder" :key="item.cart_id">
                        <h3 class="font-weight-regular mb-3"><strong>{{item.status==0?"Belum Diproses":""}}{{item.status==1?"Sedang Diproses":""}}{{item.status==2?"Dikirim":""}}{{item.status==3?"Dibatalkan":""}}</strong> / {{item.no_order}} / {{item.email}} / {{item.created_at}}</h3>
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
                                {{item.payment}} / <em>{{item.status_payment}}</em>
                            </v-col>
                        </v-row>
                        <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                            <span class="text-h6"><strong>{{RibuanLocale(item.total)}}</strong></span>
                        </p>
                        <h3 class="mb-3"><?= lang('App.productInfo') ?></h3>
                                <v-simple-table class="mb-3">
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th width="250">Product</th>
                                                <th>Code</th>
                                                <th>Qty</th>
                                                <th>Price</th>
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
                                                <td>
                                                    <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                                </td>
                                            </tr>
                                            <tr v-for="item in itemOrder" :key="item.cart_id" v-if="show == false">
                                                <td>{{item.product_name}}</td>
                                                <td>{{item.product_code}}</td>
                                                <td>{{item.qty}}</td>
                                                <td>
                                                    <span v-if="item.discount > 0">
                                                        {{ RibuanLocale(item.price - item.discount) }}
                                                    </span>
                                                    <span v-else>{{ RibuanLocale(item.price) }}</span>
                                                    <span v-show="item.discount > 0">
                                                        <p class="text-body-2 mb-0"><span class="text-decoration-line-through">{{ RibuanLocale(item.price) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.discount_percent}}%</v-chip></p>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                    </div>
                </v-card-text>
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
                    <?= lang('App.confirm') ?> Manual #{{ noOrder }}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalConfirmationClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    BANK: {{bank ?? "-"}}<br />
                    {{nama ?? "-"}}<br />
                    {{norekening ?? "-"}}<br />
                    Tanggal: {{tanggal ?? "-"}}<br />
                    {{RibuanLocale(nominal)}}
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
        dataTable: [{
            text: 'No. Order',
            value: 'no_order'
        }, {
            text: 'User',
            value: 'email'
        }, {
            text: 'Tanggal',
            value: 'tgl_input'
        }, {
            text: 'Total',
            value: 'total'
        }, {
            text: '<?= lang('App.payment'); ?>',
            value: 'payment'
        }, {
            text: '<?= lang('App.status'); ?>',
            value: 'status'
        }, {
            text: '<?= lang('App.status'); ?> Payment',
            value: 'status_payment'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        list_status: [{
            label: 'Pending',
            value: '0'
        }, {
            label: 'Processed',
            value: '1'
        }, {
            label: 'Delivered',
            value: '2'
        }, {
            label: 'Cancel',
            value: '3'
        }, ],
        list_payment: [{
            label: 'Pending',
            value: 'pending'
        }, {
            label: 'Settlement',
            value: 'settlement'
        }, {
            label: 'Canceled',
            value: 'canceled'
        }, {
            label: 'Denied',
            value: 'denied'
        }, {
            label: 'Expired',
            value: 'expired'
        }, {
            label: 'Success',
            value: 'success'
        }, {
            label: 'Challenged by FDS',
            value: 'challenged'
        }, ],
        dataOrder: [],
        totalData: 0,
        data: [],
        options: {},
        itemOrder: [],
        noOrder: "",
        idOrder: "",
        qty: "",
        total: "",
        payment: "",
        shipment: "",
        status: "",
        statusPayment: "",
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

    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataOrder: function() {
            if (this.dataOrder != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        }
    }

    methodsVue = {
        ...methodsVue,
        // Server-side paginate and sort
        getDataFromApi() {
            this.loading = true
            this.fetchData().then(data => {
                this.data = data.items
                this.totalData = data.total
                this.loading = false
            })
        },
        fetchData() {
            return new Promise((resolve, reject) => {
                const {
                    sortBy,
                    sortDesc,
                    page,
                    itemsPerPage
                } = this.options

                let search = this.search ?? "".trim();

                let items = this.dataOrder
                const total = items.length

                if (search == search.toLowerCase()) {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
                            .toLowerCase()
                            .includes(search);
                    });
                } else {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
                            .includes(search);
                    });
                }

                if (sortBy.length === 1 && sortDesc.length === 1) {
                    items = items.sort((a, b) => {
                        const sortA = a[sortBy[0]]
                        const sortB = b[sortBy[0]]

                        if (sortDesc[0]) {
                            if (sortA < sortB) return 1
                            if (sortA > sortB) return -1
                            return 0
                        } else {
                            if (sortA < sortB) return -1
                            if (sortA > sortB) return 1
                            return 0
                        }
                    })
                }

                if (itemsPerPage > 0) {
                    items = items.slice((page - 1) * itemsPerPage, page * itemsPerPage)
                }

                setTimeout(() => {
                    resolve({
                        items,
                        total,
                    })
                }, 100)
            })
        },
        // End Server-side paginate and sort

        // Get Order
        getOrder: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/order', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
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
            this.noOrder = item.no_order;
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
        // Set Status
        setStatus: function(item) {
            this.loading = true;
            this.idOrder = item.order_id;
            this.status = item.status;
            axios.put(`<?= base_url() ?>api/order/setstatus/${this.idOrder}`, {
                    status: this.status,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
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
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },
        // Set Status Payment
        setStatusPayment: function(item) {
            this.loading = true;
            this.idOrder = item.order_id;
            this.statusPayment = item.status_payment;
            axios.put(`<?= base_url() ?>api/order/setstatus/payment/${this.idOrder}`, {
                    status_payment: this.statusPayment,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
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
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },
        //Show Payment Confirm
        showConfirmation: function(item) {
            this.loading3 = true;
            this.modalConfirm = true;
            this.idOrder = item.order_id;
            this.noOrder = item.no_order;
            setTimeout(() => this.getConfirmation(), 200);
        },
        modalConfirmationClose: function() {
            this.modalConfirm = false;
            this.dataConfirm = [];
        },
        //Get
        getConfirmation: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>api/payment/get/${this.idOrder}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
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