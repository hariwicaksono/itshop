<?php
// Memanggil library
$language = new \App\Libraries\Language();
$setting = new \App\Libraries\Settings();
$appName = $setting->info['app_name'];
$imgLogo = $setting->info['img_logo'];
?>
<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("opengraph"); ?>
<meta property="og:title" content="<?= $title; ?> - <?= $appName; ?>">
<meta property="og:description" content="<?= esc($page_description ?? ''); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= current_url(); ?>">
<meta property="og:image" content="<?= base_url('images/' . $imgLogo); ?>">
<meta property="og:site_name" content="<?= $appName; ?>">
<?php $this->endSection("opengraph"); ?>

<?php $this->section("meta"); ?>
<meta name="description" content="<?= esc($page_description ?? ''); ?>">
<meta name="twitter:title" content="<?= $title; ?> - <?= $appName; ?>">
<meta name="twitter:description" content="<?= esc($page_description ?? ''); ?>">
<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "<?= esc($title ?? ''); ?>",
    "description": "<?= esc($page_description ?? ''); ?>",
    "url": "<?= current_url(); ?>",
    "publisher": {
        "@type": "Organization",
        "name": "<?= $appName; ?>"
    },
    "dateModified": "<?= esc($page_update_date ?? date('c')); ?>"
}
</script>
<?php $this->endSection("meta"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="mt-3">
        <v-card outlined>
            <v-breadcrumbs :items="itemsBC"></v-breadcrumbs>
        </v-card>
        <div class="mt-3" v-if="loading == true">
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
        <div class="mt-3" v-else>
            <h1 class="text-h4 font-weight-bold mb-4">{{dataPage.page_title}}</h1>
            <p v-html="dataPage.page_body"></p>
        </div>
    </v-container>
</template>

<v-dialog v-model="loading2" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
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
        itemsBC: [{
            text: 'Home',
            disabled: false,
            href: '/',
        }, {
            text: '<?= $title; ?>',
            disabled: true,
            href: '',
        }, ],
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
            axios.get(`<?= base_url() ?>openapi/page/${this.slug}`)
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