<?php $this->extend("layouts/app-member"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-regular"><?= lang('App.profile') ?></h1>
<v-card>
    <v-card-text>
        <v-form ref="form" v-model="valid">
            <v-text-field label="<?= lang('App.email') ?> *" v-model="email" :rules="[rules.required]" disabled outlined></v-text-field>
            <v-text-field label="<?= lang('App.username') ?> *" v-model="username" :rules="[rules.required]" outlined></v-text-field>
            <v-text-field label="<?= lang('App.namalengkap') ?> *" v-model="namaLengkap" :rules="[rules.required]" outlined></v-text-field>
            <v-text-field label="<?= lang('App.alamat') ?> *" v-model="alamat" :rules="[rules.required]" outlined></v-text-field>
            <v-row>
                <v-col>
                    <v-select label="<?= lang('App.provinsi') ?> *" v-model="select_provinsi" :items="list_provinsi" item-text="provinsi" item-value="provinsi_id" :eager="true" :loading="loading2" outlined></v-select>
                </v-col>
                <v-col>
                    <v-select label="<?= lang('App.kabupaten') ?> *" v-model="select_kabupaten" :items="list_kabupaten" item-text="nama_kabupaten" item-value="kabupaten_id" :eager="true" :loading="loading2" outlined></v-select>
                </v-col>
            </v-row>
            <v-text-field label="<?= lang('App.kodepos') ?> *" v-model="kodepos" :rules="[rules.required]" outlined></v-text-field>
        </v-form>
        <v-btn color="primary" @click="updateProfile" :loading="loading3">
            <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
        </v-btn>
    </v-card-text>
</v-card>
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
        namaLengkap: "",
        alamat: "",
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
            axios.get(`/api/user/<?= session()->get('id'); ?>`, options)
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
                        this.profile = data.data;
                        this.userId = this.profile.user_id;
                        this.email = this.profile.email;
                        this.username = this.profile.username;
                        this.namaLengkap = this.profile.nama_lengkap;
                        this.alamat = this.profile.alamat;
                        this.select_kabupaten = this.profile.kabupaten_id;
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
                })
        },
        getProvinsi: function() {
            this.loading2 = true;
            axios.get(`/api/provinsi`, options)
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
            axios.get(`/api/kabupaten/get?provinsi=${this.select_provinsi}`, options)
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
            axios.put(`/api/user/update/${this.userId}`, {
                    username: this.username,
                    nama_lengkap: this.namaLengkap,
                    alamat: this.alamat,
                    provinsi_id: this.select_provinsi,
                    kabupaten_id: this.select_kabupaten,
                    kodepos: this.kodepos,
                }, options)
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
                    this.loading = false
                })
        },
    }
</script>
<?php $this->endSection("js") ?>