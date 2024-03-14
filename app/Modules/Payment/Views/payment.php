<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-medium"><?= lang('App.payment') ?></h1>
<template>
    <!-- Table List -->
    <v-card outlined elevation="1">
        <v-card-title>
            <!-- Button Add New -->
            <v-btn large color="primary" dark @click="modalAddOpen" elevation="1"><v-icon>mdi-plus</v-icon> <?= lang('App.add') ?></v-btn>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
            </v-text-field>
        </v-card-title>

        <v-data-table :headers="dataTable" :items="paymentData" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="Sedang memuat... Harap tunggu" dense>
            <template v-slot:item="{ item }">
                <tr>
                    <td>{{item.payment}}</td>
                    <td>
                        <v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setActive(item)"></v-switch>
                    </td>
                    <td>
                        <v-switch v-model="item.cod" value="cod" false-value="0" true-value="1" color="success" @click="setCod(item)"></v-switch>
                    </td>
                    <td>
                        <v-btn icon class="mr-2" @click="editItem(item)" title="Edit" alt="Edit">
                            <v-icon color="primary">mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon @click="deleteItem(item)" title="Delete" alt="Delete">
                            <v-icon color="red">mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-card>
    <!-- End Table List -->
</template>

<!-- Modal -->
<!-- Modal Save -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent width="600px">
            <v-card>
                <v-card-title><?= lang('App.payment') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-text-field label="<?= lang('App.payment') ?> *" v-model="payment" :rules="[rules.required]" outlined>
                        </v-text-field>
                        <v-text-field label="<?= lang('App.account') ?> *" v-model="account" :rules="[rules.required]" outlined>
                        </v-text-field>
                        <v-text-field label="<?= lang('App.number') ?> *" v-model="number" :rules="[rules.required]" outlined>
                        </v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="savePayment" :loading="loading">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Save -->

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent width="600px">
            <v-card>
                <v-card-title><?= lang('App.edit') ?> <?= lang('App.payment') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-text-field label="<?= lang('App.payment') ?> *" v-model="paymentEdit" :rules="[rules.required]" outlined></v-text-field>
                        <v-text-field label="<?= lang('App.account') ?> *" v-model="accountEdit" :rules="[rules.required]" outlined></v-text-field>
                        <v-text-field label="<?= lang('App.number') ?> *" v-model="numberEdit" :rules="[rules.required]" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updatePayment" :loading="loading">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<!-- Modal Delete -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalDelete" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title>
                    <v-icon color="error" class="mr-2" x-large>mdi-alert-octagon</v-icon> <?= lang('App.confirm'); ?> <?= lang('App.delete'); ?>
                </v-card-title>
                <v-card-text>
                    <div class="mt-3 py-4">
                        <h2 class="font-weight-regular"><?= lang('App.delConfirm') ?></h2>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large text @click="modalDelete = false"><?= lang("App.no") ?></v-btn>
                    <v-btn large color="error" dark @click="deletePayment" :loading="loading"><?= lang("App.yes") ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->
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
        search: "",
        dataTable: [{
                text: '<?= lang('App.payment') ?>',
                value: 'payment'
            },
            {
                text: '<?= lang('App.active') ?>',
                value: 'active'
            },
            {
                text: '<?= lang('App.cod') ?>',
                value: 'cod'
            },
            {
                text: '<?= lang('App.action') ?>',
                value: 'actions',
                sortable: false
            },
        ],
        paymentData: [],
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        paymentId: "",
        payment: "",
        account: "",
        number: "",
        paymentIdEdit: "",
        paymentEdit: "",
        accountEdit: "",
        numberEdit: "",
        active: "",
    }

    createdVue = function() {
        this.getPayment();
    }

    methodsVue = {
        ...methodsVue,
        modalAddOpen: function() {
            this.modalAdd = true;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.reset();
            this.$refs.form.resetValidation();
        },
        // Get Payment
        getPayment: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/payment/all', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.paymentData = data.data;
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
        // Save
        savePayment: function() {
            this.loading = true;
            axios.post(`<?= base_url() ?>api/payment/save`, {
                    payment: this.payment,
                    account: this.account,
                    number: this.number,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPayment();
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalAdd = true;
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

        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            this.paymentIdEdit = item.payment_id;
            this.paymentEdit = item.payment;
            this.accountEdit = item.account;
            this.numberEdit = item.number;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.reset();
            this.$refs.form.resetValidation();
        },

        //Update
        updatePayment: function() {
            this.loading = true;
            axios.put(`<?= base_url() ?>api/payment/update/${this.paymentIdEdit}`, {
                    payment: this.paymentEdit,
                    account: this.accountEdit,
                    number: this.numberEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPayment();
                        this.modalEdit = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = true;
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

        // Get Item Delete
        deleteItem: function(item) {
            this.modalDelete = true;
            this.paymentId = item.payment_id;
        },

        // Delete
        deletePayment: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/payment/delete/${this.paymentId}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPayment();
                        this.modalDelete = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalDelete = true;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Item Active
        setActive: function(item) {
            this.loading = true;
            this.paymentId = item.payment_id;
            this.active = item.active;
            axios.put(`<?= base_url() ?>api/payment/setactive/${this.paymentId}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPayment();
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
        // Set Item
        setCod: function(item) {
            this.loading = true;
            this.paymentId = item.payment_id;
            this.cod = item.cod;
            axios.put(`<?= base_url() ?>api/payment/setcod/${this.paymentId}`, {
                    cod: this.cod,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPayment();
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