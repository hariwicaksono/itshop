<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="mb-3 font-weight-medium"><?= $title; ?></h1>
    <v-card>
        <v-card-title>

            <v-spacer></v-spacer>
            <v-text-field v-model="search" v-on:keydown.enter="getPages" @click:clear="getPages" append-icon="mdi-magnify" label="<?= lang('App.search'); ?>"  single-line hide-details clearable>
            </v-text-field>
        </v-card-title>
        <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading">
            <template v-slot:item="{ item }">
                <tr>
                    <td>{{item.page_title}}<br />
                        <em>{{item.page_title_en}}</em>
                    </td>
                    <td>{{item.updated_at}}</td>
                    <td><v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setActive(item)" title="" alt=""></v-switch></td>
                    <td>
                        <v-btn color="primary" @click="editItem(item)" icon class="mr-2" title="Edit" alt="Edit">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn color="info" link :href="'<?= base_url(); ?>' + item.slug" target="_blank" icon title="Show" alt="Show">
                            <v-icon>mdi-arrow-right</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-card>
</template>


<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" scrollable persistent width="1000px">
            <v-card>
                <v-card-title><?= lang('App.edit') ?> Page
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-text-field v-model="pageTitle" label="Page Title" :error-messages="page_titleError" outlined></v-text-field>

                        <v-text-field v-model="pageTitleEn" label="Page Title (En)" :error-messages="page_title_enError" outlined></v-text-field>

                        <v-text-field v-model="slug" label="Slug" :error-messages="slugError" filled disabled></v-text-field>

                        <v-tabs>
                            <v-tab>
                                Indonesian
                            </v-tab>
                            <v-tab>
                                English
                            </v-tab>

                            <v-tab-item class="mt-4">
                                <quill-editor v-model="pageBody" ref="quillEditor" :options="editorOption" :error-messages="page_bodyError"></vue-quill-editor>
                            </v-tab-item>
                            <v-tab-item class="mt-4">
                                <quill-editor v-model="pageBodyEn" ref="quillEditor" :options="editorOption" :error-messages="page_body_enError"></vue-quill-editor>

                            </v-tab-item>
                        </v-tabs>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updatePage" :loading="loading2">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

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
            //"Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    dataVue = {
        ...dataVue,
        modalEdit: false,
        dataPages: [],
        totalData: 0,
        data: [],
        options: {},
        dataTable: [{
            text: 'Title',
            value: 'page_title'
        }, {
            text: 'Date',
            value: 'updated_at'
        }, {
            text: 'Active',
            value: 'active'
        }, {
            text: 'Aksi',
            value: 'actions',
            sortable: false
        }, ],
        pageId: "",
        pageTitle: "",
        page_titleError: "",
        pageTitleEn: "",
        page_title_enError: "",
        pageBody: "",
        page_bodyError: "",
        pageBodyEn: "",
        page_body_enError: "",
        active: "",
        slug: "",
        slugError: "",
        userId: "<?= session()->get('id'); ?>"
    }

    var errorKeys = []

    createdVue = function() {
        axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        this.getPages();

    }

    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataPages: function() {
            if (this.dataPages != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        }
    }

    computedVue = {
        ...computedVue,
        dataSettingWithIndex() {
            return this.settingData.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },
    }

    methodsVue = {
        ...methodsVue,
        // Server-side paginate and sort
        getDataFromApi() {
            this.loading = true
            this.fetchData().then(data => {
                this.data = data.items
                this.totalData = data.total
                this.loading = false
            })
        },
        fetchData() {
            return new Promise((resolve, reject) => {
                const {
                    sortBy,
                    sortDesc,
                    page,
                    itemsPerPage
                } = this.options

                let search = this.search ?? "".trim().toLowerCase();

                let items = this.dataPages
                const total = items.length

                if (search) {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
                            .toLowerCase()
                            .includes(search);
                    });
                }

                if (sortBy.length === 1 && sortDesc.length === 1) {
                    items = items.sort((a, b) => {
                        const sortA = a[sortBy[0]]
                        const sortB = b[sortBy[0]]

                        if (sortDesc[0]) {
                            if (sortA < sortB) return 1
                            if (sortA > sortB) return -1
                            return 0
                        } else {
                            if (sortA < sortB) return -1
                            if (sortA > sortB) return 1
                            return 0
                        }
                    })
                }

                if (itemsPerPage > 0) {
                    items = items.slice((page - 1) * itemsPerPage, page * itemsPerPage)
                }

                setTimeout(() => {
                    resolve({
                        items,
                        total,
                    })
                }, 100)
            })
        },
        // End Server-side paginate and sort

        // Get Pages
        getPages: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/pages')
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPages = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPages = data.data;
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


        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            this.notifType = "";
            this.pageId = item.page_id;
            this.pageTitle = item.page_title;
            this.pageTitleEn = item.page_title_en;
            this.pageBody = item.page_body;
            this.pageBodyEn = item.page_body_en;
            this.active = item.active;
            this.slug = item.slug;
        },

        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updatePage: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/page/update/${this.pageId}`, {
                    page_title: this.pageTitle,
                    page_title_en: this.pageTitleEn,
                    page_body: this.pageBody,
                    page_body_en: this.pageBodyEn
                })
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = false;
                        this.getPages();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                        this.modalEdit = true;
                        this.$refs.form.validate();
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

        // Set Item Active Page
        setActive: function(item) {
            this.loading = true;
            this.pageId = item.page_id;
            this.active = item.active;
            axios.put(`<?= base_url() ?>api/page/setactive/${this.pageId}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPages();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
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