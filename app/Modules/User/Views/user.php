<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-medium"><?= lang('App.listUser') ?></h1>
<v-row>
    <v-col>
        <!-- Table List -->
        <v-card outlined elevation="1">
            <v-card-title>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" v-on:keydown.enter="getUsers" @click:clear="getUsers" append-icon="mdi-magnify" label="<?= lang("App.search") ?>" single-line hide-details clearable>
                </v-text-field>
            </v-card-title>

            <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading">
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{item.email}}</td>
                        <td>{{item.username}}</td>
                        <td>{{item.phone}}</td>
                        <td>
                            {{item.alamat}}
                            <div v-for="items in list_kabupaten">
                                <span v-if="items.kabupaten_kota_id == item.kabupaten_kota_id">{{items.kabupaten_kota_nama}}</span>
                            </div>
                            <div v-for="items in list_provinsi">
                                <span v-if="items.provinsi_id == item.provinsi_id">{{items.provinsi_nama}}</span>
                            </div>
                        </td>
                        <td>
                            <v-select v-model="item.role" name="role" :items="roles" item-text="label" item-value="value" label="Select" single-line @change="setRole(item)" :disabled="item.username == 'admin'"></v-select>
                        </td>
                        <td>
                            <v-switch v-model="item.active" name="active" false-value="0" true-value="1" color="success" @click="setActive(item)"  :disabled="item.username == 'admin'"></v-switch>
                        </td>
                        <td>
                            <v-btn icon color="primary" class="mr-2" @click="editItem(item)" title="Edit" alt="Edit">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon color="error" @click="deleteItem(item)" title="Delete" alt="Delete" :disabled="item.username == 'admin'">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <!-- End Table List -->
    </v-col>
</v-row>

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent scrollable width="900px">
            <v-card>
                <v-card-title><?= lang('App.editUser') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense text :type="notifType">
                            <ol>
                                <li class="grey--text text--darken-3" v-for="item in notifMessage">{{item}}</li>
                            </ol>
                        </v-alert>
                        <v-text-field label="Email *" v-model="emailEdit" :error-messages="emailError" outlined></v-text-field>

                        <v-row>
                            <v-col>
                                <v-text-field label="Username *" v-model="userNameEdit" :error-messages="usernameError" outlined></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field label="Telepon *" v-model="phoneEdit" :error-messages="phoneError" outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col>
                                <v-text-field label="First Name *" v-model="firstNameEdit" :error-messages="first_nameError" outlined></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field label="Last Name *" v-model="lastNameEdit" :error-messages="last_nameError" outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <v-text-field label="Alamat" v-model="alamatEdit" :error-messages="alamatError" outlined></v-text-field>

                        <v-row>
                            <v-col>
                                <v-select label="Provinsi" v-model="select_provinsi" :items="list_provinsi" item-text="provinsi_nama" item-value="provinsi_id" :error-messages="provinsi_idError" :loading="loading2" outlined></v-select>
                            </v-col>
                            <v-col>
                                <v-select label="Kabupaten/Kota" v-model="select_kabupaten" :items="list_kabupaten" item-text="kabupaten_kota_nama" item-value="kabupaten_kota_id" :error-messages="kabupaten_kota_idError" :loading="loading2" outlined></v-select>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col>
                                <v-text-field label="Kodepos" v-model="kodeposEdit" :error-messages="kodeposError" outlined></v-text-field>
                            </v-col>
                            <v-col>

                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateUser" :loading="loading">
                        <v-icon>mdi-content-save</v-icon> <?= lang("App.save") ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit Product -->

<!-- Modal Delete Product -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalDelete" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title class="text-h5"><?= lang('App.delConfirm') ?></v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="modalDelete = false"><?= lang("App.no") ?></v-btn>
                    <v-btn color="blue darken-1" dark @click="deleteUser" :loading="loading"><?= lang("App.yes") ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete Product -->
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

    // Deklarasi errorKeys
    var errorKeys = []

    dataVue = {
        ...dataVue,
        group: null,
        search: "",
        dataTable: [{
            text: 'Email',
            value: 'email'
        }, {
            text: 'Username',
            value: 'username'
        }, {
            text: 'Telepon',
            value: 'phone'
        }, {
            text: 'Alamat',
            value: 'alamat'
        }, {
            text: 'Role',
            value: 'role'
        }, {
            text: '<?= lang("App.active") ?>',
            value: 'active'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        dataUsers: [],
        totalData: 0,
        data: [],
        options: {},
        roles: [{
            label: 'Admin',
            value: '1'
        }, {
            label: 'User',
            value: '2'
        }, ],
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        userName: "",
        usernameError: "",
        email: "",
        emailError: "",
        role: "",
        active: "",
        userIdEdit: "",
        userNameEdit: "",
        emailEdit: "",
        userIdDelete: "",
        userNameDelete: "",
        firstName: "",
        first_nameError: "",
        firstNameEdit: "",
        lastName: "",
        last_nameError: "",
        lastNameEdit: "",
        phone: "",
        phoneError: "",
        phoneEdit: "",
        alamat: "",
        alamatError: "",
        alamatEdit: "",
        kodepos: "",
        kodeposError: "",
        kodeposEdit: "",
        list_kabupaten: [],
        list_provinsi: [],
        select_kabupaten: "",
        select_provinsi: "",
        provinsi: "",
        provinsi_idError: "",
        kabupaten: "",
        kabupaten_kota_idError: ""

    }

    createdVue = function() {
        this.getUsers();
        this.getProvinsi();
        this.getKab();
    }

    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataUsers: function() {
            if (this.dataUsers != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        },

        select_provinsi: function() {
            if (!isNaN(this.select_provinsi)) {
                this.getKabupaten();
            }
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

                let search = this.search ?? "".trim();

                let items = this.dataUsers;
                const total = items.length;

                if (search == search.toLowerCase()) {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
                            .toLowerCase()
                            .includes(search);
                    });
                } else {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
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

        // Get User
        getUsers: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/user', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarType = "success";
                        //this.snackbarMessage = data.message;
                        this.dataUsers = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataUsers = data.data;
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

        modalAddOpen: function() {
            this.modalAdd = true;
            this.notifType = "";
        },
        modalAddClose: function() {
            this.userName = "";
            this.email = "";
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },

        // Save User
        saveUser: function() {
            this.loading = true;
            axios.post(`<?= base_url() ?>api/user/save`, {
                    username: this.userName,
                    email: this.email,
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                        this.userName = "";
                        this.email = "";
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalAdd = true;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Item Edit
        editItem: function(user) {
            this.modalEdit = true;
            this.show = false;
            this.notifType = "";
            this.userIdEdit = user.user_id;
            this.userNameEdit = user.username;
            this.emailEdit = user.email;
            this.firstNameEdit = user.first_name;
            this.lastNameEdit = user.last_name;
            this.phoneEdit = user.phone;
            this.alamatEdit = user.alamat;
            this.kodeposEdit = user.kodepos;
            this.select_kabupaten = user.kabupaten_kota_id;
            this.select_provinsi = user.provinsi_id;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updateUser: function() {
            this.loading = true;
            axios.put(`<?= base_url() ?>api/user/update/${this.userIdEdit}`, {
                    username: this.userNameEdit,
                    email: this.emailEdit,
                    first_name: this.firstNameEdit,
                    last_name: this.lastNameEdit,
                    phone: this.phoneEdit,
                    alamat: this.alamatEdit,
                    provinsi_id: this.select_provinsi,
                    kabupaten_kota_id: this.select_kabupaten,
                    kodepos: this.kodeposEdit
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                        this.modalEdit = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                            this.notifType = "error";
                            this.notifMessage = data.data;
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
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
                    console.log(err.response);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        getProvinsi: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/provinsi`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_provinsi = data.data;
                    this.loading2 = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        getKab: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/kabupaten`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_kabupaten = data.data;
                    this.loading2 = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        getKabupaten: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/kabupaten/get?provinsi=${this.select_provinsi}`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    this.list_kabupaten = data.data;
                    this.loading2 = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },

        // Get Item Delete
        deleteItem: function(user) {
            this.modalDelete = true;
            this.userIdDelete = user.user_id;
            this.userNameDelete = username.username;
        },

        // Delete
        deleteUser: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/user/delete/${this.userIdDelete}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                        this.modalDelete = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalDelete = true;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Item Active
        setActive: function(user) {
            this.loading = true;
            this.userIdEdit = user.user_id;
            this.active = user.active;
            axios.put(`<?= base_url() ?>api/user/setactive/${this.userIdEdit}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Role
        setRole: function(user) {
            this.loading = true;
            this.userIdEdit = user.user_id;
            this.role = user.role;
            axios.put(`<?= base_url() ?>api/user/setrole/${this.userIdEdit}`, {
                    role: this.role,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false;
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