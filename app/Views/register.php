<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="indigo mt-n10" fill-height fluid>
        <v-layout flex align-center justify-center>
            <v-flex xs12 sm6 md6>
                <v-card elevation="2" outlined>
                    <v-card-text class="pa-6">
                        <v-img class="mx-auto mb-5" lazy-src="<?= base_url('images/logo.png') ?>" max-width="60" src="<?= base_url('images/logo.png') ?>"></v-img>
                        <h1 class="font-weight-normal text-center mb-6"><?= lang('App.register') ?></h1>
                        <v-alert v-if="notifType != ''" dense :type="notifType">{{notifMessage}}</v-alert>
                        <v-form v-model="valid" ref="form">
                            <v-text-field v-model="email" :rules="[rules.required, rules.email]" label="E-mail" outlined required :disabled="submitted"></v-text-field>
                            <v-text-field v-model="username" :rules="[rules.required]" label="Username" maxlength="20" outlined required :disabled="submitted"></v-text-field>
                            <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.required, rules.min]" :type="show1 ? 'text' : 'password'" label="Password" hint="<?= lang('App.minChar') ?>" counter @click:append="show1 = !show1" outlined :disabled="submitted"></v-text-field>
                            <v-text-field block v-model="verify" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.required, passwordMatch]" :type="show1 ? 'text' : 'password'" label="Confirm Password" counter @click:append="show1 = !show1" outlined :disabled="submitted"></v-text-field>
                            <v-layout class="mb-5">
                                <v-btn block x-large color="primary" @click="submit" :loading="loading" :disabled="submitted">Daftar</v-btn>
                            </v-layout>
                            <a href="<?= base_url('login') ?>"><?= lang('App.haveAccount') ?></a>
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
        passwordMatch() {
            return () => this.password === this.verify || "<?= lang('App.samePassword') ?>";
        }
    }
    dataVue = {
        ...dataVue,
        show1: false,
        submitted: false,
        username: "",
        password: "",
        verify: "",
        email: "",
    }
    methodsVue = {
        ...methodsVue,
        submit() {
            this.loading = true;
            axios.post(`<?= base_url() ?>auth/register`, {
                    email: this.email,
                    username: this.username,
                    password: this.password,
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
                        //setTimeout(() => window.location.href = data.data.url, 1000);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message.email || data.message.password;
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