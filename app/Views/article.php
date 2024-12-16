<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="mt-3">
        <v-card outlined>
            <v-breadcrumbs :items="itemsBC"></v-breadcrumbs>
        </v-card>
        <v-row>
            <v-col cols="12" sm="9">
                <div class="mt-5" v-if="loading == true">
                    <v-skeleton-loader type="heading" class="mb-4"></v-skeleton-loader>
                    <v-skeleton-loader type="list-item-avatar-two-line" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="image, image" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="sentences" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                    <v-skeleton-loader type="paragraph" class="mb-3"></v-skeleton-loader>
                </div>
                <div class="mt-5" v-else>
                    <h1 class="text-h4 font-weight-medium text-capitalize mb-4">{{dataArticle.article_title}}</h1>
                    <v-list-item class="ma-n3 pa-n3" two-line>
                        <v-list-item-avatar size="60" rounded>
                            <v-img lazy-src="<?= base_url('assets/images/default.png') ?>" src="<?= base_url('assets/images/default.png') ?>"></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <p class="text-subtitle-1 primary--text font-weight-medium">{{dataArticle.first_name}} {{dataArticle.last_name}} <v-icon small color="primary" v-show="dataArticle.role == '1'" title="Official Account" alt="Official Account">mdi-check-decagram</v-icon></p>
                            <p class="mb-0">{{dayjs(dataArticle.created_at).fromNow()}} / <em>{{dayjs(dataArticle.created_at).format('DD MMMM YYYY HH:mm')}}</em> &nbsp;&mdash;&nbsp; <v-icon small>mdi-tag</v-icon> {{dataArticle.category_name}} &nbsp;&mdash;&nbsp; <v-icon small>mdi-eye</v-icon> {{formatNumber(dataArticle.views)}}</p>
                        </v-list-item-content>
                    </v-list-item>
                    <br />
                    <v-img lazy-src="<?= base_url('images/no-image.png') ?>" :src="'<?= base_url(); ?>' + dataArticle.media_path" v-show="dataArticle.media_path != null"></v-img>

                    <blockquote class="blockquote">
                        <p>{{dataArticle.article_headline}}</p>
                    </blockquote>

                    <p v-html="dataArticle.article_body"></p>
                    <br />
                    <v-card outlined>
                        <v-card-text>
                            <v-list-item class="ma-n3 pa-n3" two-line>
                                <v-list-item-avatar size="100" rounded>
                                    <v-img lazy-src="<?= base_url('assets/images/default.png') ?>" src="<?= base_url('assets/images/default.png') ?>"></v-img>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <p class="text-h6 font-weight-medium"><?= lang('App.writtenBy'); ?>: {{dataArticle.first_name}} {{dataArticle.last_name}} <v-icon color="primary" v-show="dataArticle.role == '1'" title="Official Account" alt="Official Account">mdi-check-decagram</v-icon></p>
                                    <p class="text-h6 font-weight-light"><em>{{dataArticle.biography}}</em></p>
                                </v-list-item-content>
                            </v-list-item>
                            <v-divider></v-divider>
                        </v-card-text>
                    </v-card>
                </div>
            </v-col>
            <v-col cols="12" sm="3">

            </v-col>
        </v-row>
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
        dataArticle: [],
        slug: "<?= $slug; ?>"
    }

    var errorKeys = []

    createdVue = function() {
        //axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        this.getArticle()

    }

    computedVue = {
        ...computedVue,

    }

    watchVue = {
        ...watchVue,

    }

    methodsVue = {
        ...methodsVue,
        // Get Article
        getArticle: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>openapi/article/${this.slug}`)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataArticle = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataArticle = data.data;
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