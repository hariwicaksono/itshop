<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-regular"><?= lang('App.listUser') ?></h1>
<v-row class="fill-height">
    <v-col>
        <!-- Table List -->
        <v-card outlined elevation="1">
            <v-card-title>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang("App.search") ?>" single-line hide-details>
                </v-text-field>
            </v-card-title>

            <v-data-table :headers="headers" :items="users" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="Loading... Please wait" dense>
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{item.user_id}}</td>
                        <td>{{item.email}}</td>
                        <td>{{item.username}}</td>
                        <td>
                            <v-select v-model="item.role" name="role" :items="roles" item-text="label" item-value="value" label="Select" single-line @change="setRole(item)"></v-select>
                        </td>
                        <td>
                            <v-switch v-model="item.active" name="active" false-value="0" true-value="1" color="success" @click="setActive(item)"></v-switch>
                        </td>
                        <td>
                            <v-btn icon class="mr-2" @click="editItem(item)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="deleteItem(item)">
                                <v-icon color="red">mdi-delete</v-icon>
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
        <v-dialog v-model="modalEdit" persistent width="600px">
            <v-card>
                <v-card-title><?= lang('App.editUser') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-3">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-label>Username</v-label>
                            </v-col>
                            <v-col cols="12" md="8">
                                <v-text-field label="Username *" v-model="userNameEdit" :rules="[rules.required]" outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-label>Email</v-label>
                            </v-col>
                            <v-col cols="12" md="8">
                                <v-text-field label="Email *" v-model="emailEdit" :rules="[rules.required]" outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="updateUser" :loading="loading">
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

    dataVue = {
        ...dataVue,
        group: null,
        search: '',
        headers: [{
                text: 'ID',
                value: 'user_id'
            },
            {
                text: 'E-mail',
                value: 'email'
            },
            {
                text: 'Username',
                value: 'username'
            },
            {
                text: 'Role',
                value: 'role'
            },
            {
                text: '<?= lang("App.active") ?>',
                value: 'active'
            },
            {
                text: '<?= lang('App.action') ?>',
                value: 'actions',
                sortable: false
            },
        ],
        users: [],
        roles: [{
                label: 'Admin',
                value: '1'
            },
            {
                label: 'User',
                value: '2'
            },
        ],
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        userName: '',
        email: '',
        role: '',
        active: '',
        userIdEdit: '',
        userNameEdit: '',
        emailEdit: '',
        userIdDelete: '',
        userNameDelete: '',
    }
    createdVue = function() {
        this.getUsers();
    }

    methodsVue = {
        ...methodsVue,
        modalAddOpen: function() {
            this.modalAdd = true;
            this.notifType = '';
        },
        modalAddClose: function() {
            this.userName = '';
            this.email = '';
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },
        // Get User
        getUsers: function() {
            this.loading = true;
            axios.get('/api/user', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarType = "success";
                        //this.snackbarMessage = data.message;
                        this.users = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                })
        },

        // Save User
        saveUser: function() {
            this.loading = true;
            axios.post(`/api/user/save`, {
                    username: this.userName,
                    email: this.email,
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                        this.userName = '';
                        this.email = '';
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
                    this.loading = false
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
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updateUser: function() {
            this.loading = true;
            axios.put(`/api/user/update/${this.userIdEdit}`, {
                    username: this.userNameEdit,
                    email: this.emailEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                        this.modalEdit = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = true;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
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
            axios.delete(`/api/user/delete/${this.userIdDelete}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
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
                })
        },

        // Set Item Active
        setActive: function(user) {
            this.loading = true;
            this.userIdEdit = user.user_id;
            this.active = user.active;
            axios.put(`/api/user/setactive/${this.userIdEdit}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },

        // Set Role
        setRole: function(user) {
            this.loading = true;
            this.userIdEdit = user.user_id;
            this.role = user.role;
            axios.put(`/api/user/setrole/${this.userIdEdit}`, {
                    role: this.role,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getUsers();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },


    }
</script>
<?php $this->endSection("js") ?>