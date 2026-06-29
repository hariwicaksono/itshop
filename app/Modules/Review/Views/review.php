<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-3"><?= $title; ?></h1>
    <v-card>
        <v-card-title>
            <v-select v-model="filterStatus" :items="statusOptions" item-text="label" item-value="value" label="Filter Status" outlined dense hide-details style="max-width: 200px;" @change="getReviews"></v-select>
            <v-spacer></v-spacer>
            <v-text-field v-model="pencarian" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
            </v-text-field>
        </v-card-title>
        <v-data-table :headers="dataHeader" :items="dataReview" :items-per-page="10" :loading="loading" :search="pencarian" class="elevation-0" loading-text="<?= lang('App.loadingWait'); ?>">
            <template v-slot:item="{ item }">
                <tr>
                    <td width="60">{{ item.review_id }}</td>
                    <td>{{ item.product_name ?? '-' }}</td>
                    <td>{{ item.first_name ?? '-' }} {{ item.last_name ?? '-' }}<br><span class="text-caption grey--text">{{ item.email ?? '' }}</span></td>
                    <td width="150">
                        <v-rating v-model="item.rating" readonly dense half-increments size="16" color="amber"></v-rating>
                    </td>
                    <td style="max-width: 300px;">
                        <span class="d-inline-block text-truncate" style="max-width: 280px;" :title="item.review_text">
                            {{ item.review_text || '-' }}
                        </span>
                    </td>
                    <td>
                        <v-chip small :color="statusColor(item.status)" dark>{{ statusLabel(item.status) }}</v-chip>
                    </td>
                    <td>{{ formatDate(item.created_at) }}</td>
                    <td width="220">
                        <v-btn v-if="item.status != 1" color="success" class="mr-1" icon small @click="approveReview(item)" title="Setujui">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                        <v-btn v-if="item.status != 2" color="warning" class="mr-1" icon small @click="rejectReview(item)" title="Tolak">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                        <v-btn color="primary" class="mr-1" icon small @click="editItem(item)" title="<?= lang('App.edit'); ?>">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn color="error" icon small @click="deleteItem(item)" title="<?= lang('App.delete'); ?>">
                            <v-icon>mdi-delete</v-icon>
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
        <v-dialog v-model="modalEdit" persistent scrollable max-width="600px">
            <v-card>
                <v-card-title>
                    <?= lang('App.edit') ?> <?= $title; ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="pt-5">
                    <v-form ref="form" v-model="valid">
                        <p class="mb-1 text-subtitle-1">Rating</p>
                        <v-rating v-model="rating" hover dense half-increments color="amber" background-color="grey lighten-1"></v-rating>

                        <p class="mb-1 text-subtitle-1">Ulasan</p>
                        <v-textarea v-model="reviewText" label="Teks Ulasan" outlined rows="3"></v-textarea>

                        <p class="mb-1 text-subtitle-1">Status</p>
                        <v-select v-model="reviewStatus" :items="editStatusOptions" item-text="label" item-value="value" label="Status" outlined></v-select>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateReview" :loading="loading1" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.update') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<!-- Modal Delete -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalDelete" persistent max-width="400px">
            <v-card>
                <v-card-title class="text-h6">
                    Hapus ulasan ini?
                </v-card-title>
                <v-card-text>
                    Data yang dihapus tidak dapat dikembalikan.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="modalDeleteClose">Batal</v-btn>
                    <v-btn color="red darken-1" text @click="deleteReview" :loading="loading1">Hapus</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<?php $this->endSection("content"); ?>


<?php $this->section("js"); ?>
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    var errorKeys = []

    dataVue = {
        ...dataVue,
        pencarian: "",
        modalEdit: false,
        modalDelete: false,
        filterStatus: "",
        dataHeader: [{
            text: "#",
            value: "review_id"
        }, {
            text: "Mobil",
            value: "car_nama"
        }, {
            text: "Pemberi Ulasan",
            value: "fullname"
        }, {
            text: "Rating",
            value: "rating"
        }, {
            text: "Ulasan",
            value: "review_text"
        }, {
            text: "Status",
            value: "status"
        }, {
            text: "Tanggal",
            value: "created_at"
        }, {
            text: "<?= lang('App.action') ?>",
            value: "actions",
            sortable: false
        }],
        dataReview: [],
        idReview: "",
        rating: 0,
        reviewText: "",
        reviewStatus: 0,
        statusOptions: [
            { label: 'Semua', value: '' },
            { label: 'Menunggu', value: 0 },
            { label: 'Disetujui', value: 1 },
            { label: 'Ditolak', value: 2 }
        ],
        editStatusOptions: [
            { label: 'Menunggu', value: 0 },
            { label: 'Disetujui', value: 1 },
            { label: 'Ditolak', value: 2 }
        ],
    };

    createdVue = function () {
        axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        this.getReviews();
    };

    methodsVue = {
        ...methodsVue,

        // Format datetime
        formatDate(dt) {
            if (!dt) return '-';
            return dt.replace('T', ' ');
        },

        statusLabel(status) {
            const labels = { 0: 'Menunggu', 1: 'Disetujui', 2: 'Ditolak' };
            return labels[status] ?? 'Unknown';
        },

        statusColor(status) {
            const colors = { 0: 'grey', 1: 'green', 2: 'red' };
            return colors[status] ?? 'grey';
        },

        // Get Review Data
        getReviews: function () {
            this.loading = true;
            var url = '<?= base_url(); ?>api/review';
            if (this.filterStatus !== '' && this.filterStatus !== null) {
                url += '?status=' + this.filterStatus;
            }
            axios.get(url)
                .then(res => {
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataReview = data.data;
                    } else {
                        this.dataReview = [];
                    }
                })
                .catch(err => {
                    this.loading = false;
                    console.log(err);
                });
        },

        // Approve Review (status = 1)
        approveReview: function (item) {
            this.loading1 = true;
            axios.put(`<?= base_url(); ?>api/review/setstatus/${item.review_id}`, {
                status: 1
            }, options)
                .then(res => {
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = 'Ulasan disetujui dan ditampilkan publik.';
                        this.getReviews();
                    }
                })
                .catch(err => {
                    this.loading1 = false;
                    console.log(err);
                });
        },

        // Reject Review (status = 2)
        rejectReview: function (item) {
            this.loading1 = true;
            axios.put(`<?= base_url(); ?>api/review/setstatus/${item.review_id}`, {
                status: 2
            }, options)
                .then(res => {
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = 'Ulasan ditolak.';
                        this.getReviews();
                    }
                })
                .catch(err => {
                    this.loading1 = false;
                    console.log(err);
                });
        },

        // Edit Item
        editItem: function (item) {
            this.idReview = item.review_id;
            this.rating = item.rating;
            this.reviewText = item.review_text ?? "";
            this.reviewStatus = item.status ?? 0;
            this.modalEdit = true;
        },

        modalEditClose: function () {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        // Update Review
        updateReview: function () {
            this.loading1 = true;
            axios.put(`<?= base_url(); ?>api/review/update/${this.idReview}`, {
                rating: this.rating,
                review_text: this.reviewText,
                status: this.reviewStatus,
            }, options)
                .then(res => {
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getReviews();
                        this.modalEditClose();
                    }
                })
                .catch(err => {
                    this.loading1 = false;
                    console.log(err);
                });
        },

        // Delete Item
        deleteItem: function (item) {
            this.modalDelete = true;
            this.idReview = item.review_id;
        },

        modalDeleteClose: function () {
            this.modalDelete = false;
        },

        deleteReview: function () {
            this.loading1 = true;
            axios.delete(`<?= base_url(); ?>api/review/delete/${this.idReview}`)
                .then(res => {
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getReviews();
                        this.modalDelete = false;
                    }
                })
                .catch(err => {
                    this.loading1 = false;
                    console.log(err);
                });
        },
    };
</script>
<?php $this->endSection("js"); ?>
