<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="primary px-4 py-10" fill-height fluid>
        <v-layout flex align-center justify-center>
            <v-flex xs12 sm12 md6>
                <v-card>
                    <v-card-text class="pa-6">
                        <v-img class="mx-auto mb-5" lazy-src="<?= base_url('images/logo.png') ?>" max-width="60" src="<?= base_url('images/logo.png') ?>"></v-img>
                        <h1 class="text-center mb-5"><?= lang('App.forgotPass') ?></h1>
                        <v-alert v-if="notifType != ''" dense :type="notifType">{{notifMessage}}</v-alert>
                        <v-form v-model="valid" ref="form">
                            <v-text-field label="<?= lang('App.labelEmail') ?>" v-model="email" :rules="[rules.required, rules.email]" outlined :disabled="submitted"></v-text-field>
                            <v-btn large @click="submit" color="primary" class="mb-5" :loading="loading" :disabled="submitted"><v-icon>mdi mdi-email</v-icon> <?= lang('App.send') ?> Reset Password</v-btn>
                            <v-layout justify-space-between>
                                <p>
                                    <a href="<?= base_url('login') ?>"><?= lang('App.haveAccount') ?></a><br />
                                    <a href="<?= base_url('register') ?>"><?= lang('App.register') ?></a>
                                </p>
                            </v-layout>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    computedVue = {
        ...computedVue,
    }
    dataVue = {
        ...dataVue,
        show1: false,
        submitted: false,
        email: '',
    }
    methodsVue = {
        ...methodsVue,
        submit() {
            this.loading = true;
            axios.post(`<?= base_url() ?>auth/resetPassword`, {
                    email: this.email
                })
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.submitted = true;
                        this.notifType = "success";
                        this.notifMessage = data.message;
                        this.$refs.form.resetValidation();
                        //setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.snackbar = true;
                        this.snackbarType = "error";
                        this.snackbarMessage = data.message.email || data.message.password;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },
        clear() {
            this.$refs.form.reset()
        }
    }
</script>

<?php $this->endSection("js") ?>