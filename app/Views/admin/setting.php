<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-regular"><?= lang('App.setting') ?></h1>
<template>
    <v-card outlined elevation="1">
        <v-card-text>
            <v-form ref="form" v-model="valid">
                <v-text-field label="<?= lang('App.siteTitle') ?> *" v-model="siteTitle" :rules="[rules.required]" outlined></v-text-field>
            </v-form>
            <v-btn color="primary" @click="updateSetting" :loading="loading2">
                <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
            </v-btn>
        </v-card-text>
    </v-card>
</template>             

<v-dialog v-model="loading" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            Memuat, silahkan tunggu...
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
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
        settingData: [],
        settingId: "",
        siteTitle: "",
    }
    createdVue = function() {
        this.getSetting();
    }

    methodsVue = {
        ...methodsVue,
        // Get
        getSetting: function() {
            this.loading = true;
            axios.get('/api/setting', options)
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
                        this.settingData = data.data;
                        this.settingId = this.settingData.setting_id;
                        this.siteTitle = this.settingData.site_title;
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

        //Update
        updateSetting: function() {
            this.loading2 = true;
            axios.put(`/api/setting/update/${this.settingId}`, {
                    site_title: this.siteTitle,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getSetting();
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