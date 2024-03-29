<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="px-4 py-15" fill-height fluid>
        <v-layout flex align-center justify-center>
            <v-flex xs12 sm8 md8>
                <?php if (session()->getFlashdata('success')) { ?>
                    <v-alert type="success" dismissible v-model="alert">
                        <?= session()->getFlashdata('success') ?>
                    </v-alert>
                <?php } ?>
                <v-card outlined>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" sm="5" style="background-image: url('https://picsum.photos/800/600?random') !important;background-position: center;background-repeat: no-repeat;-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;">
                            </v-col>
                            <v-col cols="12" sm="7" class="pa-8">
                                <v-img class="mx-auto mb-5" lazy-src="<?= base_url('images/logo.png') ?>" max-width="60" src="<?= base_url('images/logo.png') ?>"></v-img>
                                <h1 class="text-center mb-8"><?= lang('App.signIn') ?></h1>
                                <v-form class="mb-3" v-model="valid" ref="form">
                                    <v-text-field label="Email" v-model="email" :rules="[rules.email]" :error-messages="emailError" outlined></v-text-field>
                                    <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.min]" :type="show1 ? 'text' : 'password'" label="Password" hint="<?= lang('App.minChar') ?>" @click:append="show1 = !show1" :error-messages="passwordError" outlined counter></v-text-field>
                                </v-form>
                                <v-layout justify-space-between>
                                    <v-btn @click="submit" color="primary" dark x-large :loading="loading" elevation="1">Login</v-btn>
                                    <p>
                                        <a href="<?= base_url('password/reset') ?>"><?= lang('App.forgotPass') ?></a><br />
                                        <a href="<?= base_url('register') ?>"><?= lang('App.register') ?></a>
                                    </p>
                                </v-layout>
                            </v-col>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    var errorKeys = []
    computedVue = {
        ...computedVue,
    }
    dataVue = {
        ...dataVue,
        alert: false,
        show1: false,
        email: "",
        emailError: "",
        password: "",
        passwordError: "",
    }
    createdVue = function() {
        this.alert = true;
        setTimeout(() => {
            this.alert = false
        }, 5000)
    }
    methodsVue = {
        ...methodsVue,
        submit() {
            this.loading = true;
            axios.post(`<?= base_url() ?>auth/login`, {
                    email: this.email,
                    password: this.password,
                })
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        localStorage.setItem('access_token', JSON.stringify(data.access_token));
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.$refs.form.resetValidation();
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
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
                    console.log(err);
                    this.loading = false
                })
        },
        clear() {
            this.$refs.form.reset()
        }
    }
</script>

<?php $this->endSection("js") ?>