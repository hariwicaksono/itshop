<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container>
        <v-card>
            <v-card-text>
                <div v-if="loading == true">
                    <v-skeleton-loader type="heading" class="mb-4"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                </div>
                <div v-else>
                    <h1 class="text-h4 font-weight-bold mb-4">{{dataPage.page_title}}</h1>
                    <p v-html="dataPage.page_body"></p>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<v-dialog v-model="loading2" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            Memuat, silahkan tunggu...
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
<br />
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            //"Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    dataVue = {
        ...dataVue,
        dataPage: [],
        slug: "<?= $slug; ?>"

    }

    var errorKeys = []

    createdVue = function() {
        axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        this.getPage()

    }

    computedVue = {
        ...computedVue,

    }

    watchVue = {
        ...watchVue,

    }

    methodsVue = {
        ...methodsVue,
        // Get Page
        getPage: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/page/${this.slug}`)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPage = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPage = data.data;
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