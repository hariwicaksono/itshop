<?php $this->extend("layouts/app-member"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="mb-2 font-weight-medium"><?= lang('App.myProfile') ?></h1>
    <v-card>
        <v-card-text>
            <v-form ref="form" v-model="valid">
                <v-text-field label="Email *" v-model="email" :rules="[rules.required]" disabled outlined></v-text-field>
                <v-text-field label="Username *" v-model="username" :rules="[rules.required]" outlined></v-text-field>
                <v-row>
                    <v-col>
                        <v-text-field label="Nama Depan *" v-model="firstName" :rules="[rules.required]" outlined></v-text-field>
                    </v-col>
                    <v-col>
                        <v-text-field label="Nama Belakang *" v-model="lastName" :rules="[rules.required]" outlined></v-text-field>
                    </v-col>
                </v-row>
                <v-text-field label="Alamat *" v-model="alamat" :rules="[rules.required]" outlined></v-text-field>
                <v-text-field label="Telepon *" v-model="phone" :rules="[rules.required]" outlined></v-text-field>
                <v-row>
                    <v-col>
                        <v-select label="Provinsi *" v-model="select_provinsi" :items="list_provinsi" item-text="provinsi_nama" item-value="provinsi_id" :eager="true" :loading="loading2" outlined></v-select>
                    </v-col>
                    <v-col>
                        <v-select label="Kabupaten *" v-model="select_kabupaten" :items="list_kabupaten" item-text="kabupaten_kota_nama" item-value="kabupaten_kota_id" :eager="true" :loading="loading2" outlined></v-select>
                    </v-col>
                </v-row>
                <v-text-field label="Kodepos *" v-model="kodepos" :rules="[rules.required]" outlined></v-text-field>
            </v-form>
            <v-btn large color="primary" @click="updateProfile" :loading="loading3">
                <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
            </v-btn>
        </v-card-text>
    </v-card>
</template>
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
        profile: [],
        email: "",
        username: "",
        firstName: "",
        lastName: "",
        alamat: "",
        phone: "",
        kodepos: "",
        list_kabupaten: [],
        list_provinsi: [],
        select_kabupaten: "",
        select_provinsi: "",
        provinsi: "",
        kabupaten: "",

    }

    createdVue = function() {
        this.getProfile();
        this.getProvinsi();
    }

    watchVue = {
        ...watchVue,
        select_provinsi: function() {
            if (!isNaN(this.select_provinsi)) {
                this.getKabupaten();
            }
        },
    }

    methodsVue = {
        ...methodsVue,
        // Get
        getProfile: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/user/<?= session()->get('id'); ?>`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.profile = data.data;
                        this.userId = this.profile.user_id;
                        this.email = this.profile.email;
                        this.username = this.profile.username;
                        this.firstName = this.profile.first_name;
                        this.lastName = this.profile.last_name;
                        this.alamat = this.profile.alamat;
                        this.phone = this.profile.phone;
                        this.select_kabupaten = this.profile.kabupaten_kota_id;
                        this.select_provinsi = this.profile.provinsi_id;
                        this.kodepos = this.profile.kodepos;
                        this.provinsi = this.profile.provinsi;
                        this.kabupaten = this.profile.nama_kabupaten;
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
        getProvinsi: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/provinsi`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_provinsi = data.data;
                    this.loading2 = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        getKabupaten: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/kabupaten/get?provinsi=${this.select_provinsi}`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_kabupaten = data.data;
                    this.loading2 = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        //Update
        updateProfile: function() {
            this.loading3 = true;
            axios.put(`<?= base_url() ?>api/user/update/${this.userId}`, {
                    username: this.username,
                    first_name: this.firstName,
                    last_name: this.lastName,
                    alamat: this.alamat,
                    phone: this.phone,
                    provinsi_id: this.select_provinsi,
                    kabupaten_kota_id: this.select_kabupaten,
                    kodepos: this.kodepos,
                }, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getProfile();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false;
                    var error = err.response;
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