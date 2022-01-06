<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-regular"><?= lang('App.shipment') ?></h1>
<template>
    <!-- Table List -->
    <v-card outlined elevation="1">
        <v-card-title>
            <!-- Button Add New -->
             <!--<v-btn color="primary" dark @click="modalAddOpen"><?//= lang('App.add') ?></v-btn>-->
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
            </v-text-field>
        </v-card-title>

        <v-data-table :headers="tbheader" :items="shipmentData" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="Sedang memuat... Harap tunggu" dense>
            <template v-slot:item="{ item }">
                <tr>
                    <td>{{item.shipment_id}}</td>
                    <td>{{item.shipment}}</td>
                    <td>
                        <v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setActive(item)"></v-switch>
                    </td>
                    <td>
                        <v-btn icon class="mr-2" @click="editItem(item)">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <!--<v-btn icon @click="deleteItem(item)">
                            <v-icon color="red">mdi-delete</v-icon>
                        </v-btn>-->
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
                <v-card-title><?= lang('App.shipment') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-3">
                    <v-form ref="form" v-model="valid">
                        <v-text-field label="<?= lang('App.shipment') ?> *" v-model="shipment" :rules="[rules.required]" outlined>
                        </v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="saveShipment" :loading="loading">
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
                <v-card-title><?= lang('App.edit') ?> <?= lang('App.shipment') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-3">
                    <v-form ref="form" v-model="valid">
                        <v-text-field label="<?= lang('App.shipment') ?> *" v-model="shipmentEdit" :rules="[rules.required]" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="updateShipment" :loading="loading">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>  
<!-- End Modal Edit -->
                
<!-- Modal Delete -->
<!--<template>
    <v-row justify="center">
        <v-dialog v-model="modalDelete" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title class="text-h5"><?//= lang('App.delConfirm') ?></v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="modalDelete = false"><?//= lang('App.no') ?></v-btn>
                     <v-btn color="blue darken-1" dark @click="deleteShipment" :loading="loading"><?//= lang('App.yes') ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>-->          
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
        tbheader: [{
                text: 'ID',
                value: 'shipment_id'
            },
            {
                text: '<?= lang('App.shipment') ?>',
                value: 'shipment'
            },
            {
                text: '<?= lang('App.active') ?>',
                value: 'active'
            },
            {
                text: '<?= lang('App.action') ?>',
                value: 'actions',
                sortable: false
            },
        ],
        shipmentData: [],
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        shipmentId: "",
        shipment: "",
        shipmentEdit: "",
        active: "",
    }
    createdVue = function() {
        this.getShipment();
    }

    methodsVue = {
        ...methodsVue,
        modalAddOpen: function() {
            this.modalAdd = true;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },
        // Get Shipment
        getShipment: function() {
            this.loading = true;
            axios.get('/api/shipment/all', options)
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
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.shipmentData = data.data;
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
        // Save
        saveShipment: function() {
            this.loading = true;
            axios.post(`/api/shipment/save`, {
                    shipment: this.shipment,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getShipment();
                        this.modalAdd = false;
                        this.$refs.form.reset();
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
                    this.loading = false
                })
        },

        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            this.show = false;
            this.shipmentId = item.shipment_id;
            this.shipmentEdit = item.shipment;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updateShipment: function() {
            this.loading = true;
            axios.put(`/api/shipment/update/${this.shipmentId}`, {
                    shipment: this.shipmentEdit,
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
                        this.getShipment();
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
                    this.loading = false
                })
        },

        // Get Item Delete
        /** deleteItem: function(item) {
            this.modalDelete = true;
            this.shipmentId = item.shipment_id;
            this.shipment = item.shipment;
        },

        // Delete
        deleteShipment: function() {
            this.loading = true;
            axios.delete(`/api/shipment/delete/${this.shipmentId}`, options)
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
                        this.getShipment();
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
                })
        }, */

        // Set Item Active
        setActive: function(item) {
            this.loading = true;
            this.shipmentId = item.shipment_id;
            this.active = item.active;
            axios.put(`/api/shipment/setactive/${this.shipmentId}`, {
                    active: this.active,
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
                        this.getShipment();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },

    }
</script>
<?php $this->endSection("js") ?>