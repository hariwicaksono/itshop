<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<v-container class="orange darken-1 px-4 py-0 fill-height mt-n10" fluid>
    <v-layout flex align-center justify-center>
        <v-flex xs12 sm5 md4>
            <v-card elevation="2" outlined>
                <v-card-text class="pa-10">
                    <v-img class="mx-auto mb-5" lazy-src="https://cdn.vuetifyjs.com/docs/images/logos/vuetify-logo-light.svg" max-width="30" src="https://cdn.vuetifyjs.com/docs/images/logos/vuetify-logo-light.svg"></v-img>
                    <h1 class="font-weight-normal text-center mb-8"><?= lang('App.signIn') ?></h1>
                    <v-form v-model="valid" ref="form">
                        <v-text-field label="<?= lang('App.labelEmail') ?>" v-model="email" :rules="[rules.email]" outlined dense></v-text-field>
                        <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.required, rules.min]" :type="show1 ? 'text' : 'password'" label="Password" hint="<?= lang('App.minChar') ?>" @click:append="show1 = !show1" counter outlined dense></v-text-field>
                        <v-layout justify-space-between>
                            <a href="<?= base_url('/password/reset') ?>"><?= lang('App.forgotPass') ?></a>
                            <v-btn @click="submit" color="primary" :loading="loading">Login</v-btn>
                        </v-layout>
                        <a href="<?= base_url('/register') ?>"><?= lang('App.register') ?></a>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
</v-container>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?> 
<script>
    computedVue = {
        ...computedVue,
    }
    dataVue = {
        ...dataVue,
        show1: false,
        email: '',
        password: '',
    }
    methodsVue = {
        ...methodsVue,
        submit() {
            this.loading = true;
            axios.post(`/auth/login`, {
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
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.error || data.message.email || data.message.password;
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