<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-regular"><?= lang('App.listProduct') ?></h1>
<v-row class="fill-height">
    <v-col>
        <!-- Table List Product -->
        <v-card outlined elevation="1">
            <v-card-title>
                <!-- Button Add New Product -->
                 <v-btn color="primary" dark @click="modalAddOpen"><?= lang('App.addProduct') ?></v-btn>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
                </v-text-field>
            </v-card-title>

            <v-data-table :headers="headers" :items="products" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="Sedang memuat... Harap tunggu" dense>
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{item.product_id}}</td>
                        <td>
                            <v-avatar size="60px" rounded><img v-bind:src="'../' + item.media_path" /></v-avatar>
                        </td>
                        <td>
                            <h3>{{item.product_name}}</h3>
                        </td>
                        <td>
                            <v-edit-dialog :return-value.sync="item.product_price" @save="setPrice(item)" @cancel="" @open="" @close="">
                                {{item.product_price}}
                                <template v-slot:input>
                                    <v-text-field v-model="item.product_price" type="number" single-line></v-text-field>
                                </template>
                            </v-edit-dialog>
                        </td>
                        <td>
                            <v-edit-dialog :return-value.sync="item.stock" @save="setStock(item)" @cancel="" @open="" @close="">
                                {{item.stock}}
                                <template v-slot:input>
                                    <v-text-field v-model="item.stock" type="number" single-line></v-text-field>
                                </template>
                            </v-edit-dialog>
                        </td>
                        <td>
                            <v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setActive(item)"></v-switch>
                        </td>
                        <td>
                            <v-btn icon class="mr-2" @click="showItem(item)">
                                <v-icon color="primary">mdi-eye</v-icon>
                            </v-btn>
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
        <!-- End Table List Product -->

    </v-col>
</v-row>

<!-- Modal -->
<!-- Modal Save Product -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent scrollable width="900px">
            <v-card>
                <v-card-title>
                    <?= lang('App.addProduct') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-3">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productName') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-text-field label="<?= lang('App.productName') ?> *" v-model="productName" :rules="[rules.required]" outlined dense>
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productPrice') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-text-field label="<?= lang('App.productPrice') ?> *" v-model="productPrice" type="number" :rules="[rules.required]" outlined dense>
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productDesc') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-textarea v-model="productDescription" counter maxlength="3000" outlined full-width single-line></v-textarea>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productImg') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-image-input v-model="productImage" :clearable="true" :hide-actions="true" :image-width="700" :image-height="700" image-format="jpg,jpeg,png" overlay-padding="25px" @input="onFileInfo" />
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="saveProduct" :loading="loading">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>     
<!-- End Modal Save Product -->

<!-- Modal Show Product -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalShow" persistent scrollable width="900px">
            <v-card>
                <v-card-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalShow = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-3">
                    <v-row>
                        <v-col cols="12" md="2">
                            <v-label><?= lang('App.productName') ?></v-label>
                        </v-col>
                        <v-col cols="12" md="10">
                            <v-text-field label="<?= lang('App.productName') ?> *" v-model="productNameEdit" :rules="[rules.required]" outlined dense></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="2">
                            <v-label><?= lang('App.productPrice') ?></v-label>
                        </v-col>
                        <v-col cols="12" md="10">
                            <v-text-field label="<?= lang('App.productPrice') ?> *" v-model="productPriceEdit" type="number" :rules="[rules.required]" outlined dense></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="2">
                            <v-label><?= lang('App.productDesc') ?></v-label>
                        </v-col>
                        <v-col cols="12" md="10">
                            <v-textarea v-model="productDescriptionEdit" counter maxlength="3000" outlined full-width single-line></v-textarea>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="2">
                            <v-label><?= lang('App.productImg') ?></v-label>
                        </v-col>
                        <v-col cols="12" md="10">
                            <template>
                                <v-hover>
                                    <template v-slot:default="{ hover }">
                                        <v-card max-width="450">
                                            <v-img v-model="mediaID" v-bind:src="'../' + mediaPathEdit" />
                                            </v-img>
                                        </v-card>
                                    </template>
                                </v-hover>
                            </template>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>  
<!-- End Modal Show Product -->
                  
<!-- Modal Edit Product -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent scrollable width="900px">
            <v-card>
                <v-card-title>
                    <?= lang('App.editProduct') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-3">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productName') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-text-field label="<?= lang('App.productName') ?> *" v-model="productNameEdit" :rules="[rules.required]" outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productPrice') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-text-field label="<?= lang('App.productPrice') ?> *" v-model="productPriceEdit" type="number" :rules="[rules.required]" outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productDesc') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <v-textarea v-model="productDescriptionEdit" counter maxlength="3000" outlined full-width single-line></v-textarea>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="2">
                                <v-label><?= lang('App.productImg') ?></v-label>
                            </v-col>
                            <v-col cols="12" md="10">
                                <template v-if="show == false">
                                    <v-hover>
                                        <template v-slot:default="{ hover }">
                                            <v-card max-width="450">
                                                <v-img v-model="mediaID" v-bind:src="'../' + mediaPathEdit" />
                                                </v-img>

                                                <v-fade-transition>
                                                    <v-overlay v-if="hover" absolute color="#036358">
                                                        <v-btn color="red" dark @click="deleteMedia(mediaID)" :loading="loading">
                                                            <v-icon left>
                                                                mdi-delete
                                                            </v-icon> <?= lang('App.delete') ?>
                                                        </v-btn>
                                                    </v-overlay>
                                                </v-fade-transition>
                                            </v-card>
                                        </template>
                                    </v-hover>
                                </template>
                                <v-image-input v-if="show == true" v-model="productImage" :clearable="true" :hide-actions="true" :image-width="700" :image-height="700" :full-height="true" :full-width="true" image-format="jpg,jpeg,png" overlay-padding="25px" @input="onFileInfo" />
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="updateProduct" :loading="loading">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
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
                    <v-btn color="blue darken-1" text @click="modalDelete = false"><?= lang('App.no') ?></v-btn>
                     <v-btn color="blue darken-1" dark @click="deleteProduct" :loading="loading"><?= lang('App.yes') ?></v-btn>
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
    function b64toBlob(b64Data, contentType, sliceSize) {
        contentType = contentType || '';
        sliceSize = sliceSize || 512;

        var byteCharacters = atob(b64Data);
        var byteArrays = [];

        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            var slice = byteCharacters.slice(offset, offset + sliceSize);

            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            var byteArray = new Uint8Array(byteNumbers);

            byteArrays.push(byteArray);
        }

        var blob = new Blob(byteArrays, {
            type: contentType
        });
        return blob;
    }

    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    dataVue = {
        ...dataVue,
        search: '',
        headers: [{
                text: 'ID',
                value: 'product_id'
            },
            {
                text: 'FOTO',
                value: 'media_path'
            },
            {
                text: '<?= lang('App.productInfo') ?>',
                value: 'product_name'
            },
            {
                text: '<?= lang('App.price') ?>',
                value: 'product_price'
            },
            {
                text: '<?= lang('App.stock') ?>',
                value: 'stock'
            },
            {
                text: '<?= lang('App.active') ?>',
                value: 'active'
            },
            {
                text: '<?= lang('App.action') ?>',
                value: 'actions',
                sortable: false
            },
        ],
        products: [],
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        productName: '',
        productPrice: '',
        productDescription: '',
        productImage: null,
        stock: '',
        active: '',
        productIdEdit: '',
        productNameEdit: '',
        productPriceEdit: '',
        productDescriptionEdit: '',
        productImageEdit: null,
        mediaPathEdit: null,
        productIdDelete: '',
        productNameDelete: '',
        mediaID: '',
        mediaPath: null,
        outputFotoReg: null,
    }
    createdVue = function() {
        this.getProducts();
    }

    methodsVue = {
        ...methodsVue,
        modalAddOpen: function() {
            this.modalAdd = true;
            this.productImage = null;
            this.notifType = '';
        },
        modalAddClose: function() {
            this.productName = '';
            this.productPrice = '';
            this.productDescription = '';
            this.productImage = null;
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },
        // Get Product
        getProducts: function() {
            this.loading = true;
            axios.get('/api/product', options)
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
                        //this.snackbarMessage = data.message;
                        this.products = data.data;
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
        onFileInfo(value) {
            this.fileInfo = value;
            this.uploadMedia(value);
        },
        uploadMedia: function(file) {
            var formData = new FormData()
            // Split the base64 string in data and contentType
            var block = file.split(";");
            // Get the content type of the image
            var contentType = block[0].split(":")[1]; // In this case "image/gif"
            // get the real base64 content of the file
            var realData = block[1].split(",")[1]; // In this case "R0lGODlhPQBEAPeoAJosM...."

            // Convert it to a blob to upload
            var blob = b64toBlob(realData, contentType);
            formData.append('productImage', blob);
            axios.post(`/api/media/save`, formData, options)
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
                        this.mediaID = data.data
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false
                })
        },
        // Delete Product
        deleteMedia: function() {
            this.loading = true;
            axios.delete(`/api/media/delete/${this.mediaID}`, options)
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
                        this.show = true;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false;
                    this.show = false;
                })
        },
        // Save Product
        saveProduct: function() {
            this.loading = true;
            axios.post(`/api/product/save`, {
                    product_name: this.productName,
                    product_price: this.productPrice,
                    product_description: this.productDescription,
                    product_image: this.mediaID,
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
                        this.getProducts();
                        this.productName = '';
                        this.productPrice = '';
                        this.productDescription = '';
                        this.productImage = null;
                        this.mediaID = '';
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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

        // Get Item Show Product
        showItem: function(product) {
            this.modalShow = true;
            this.show = false;
            this.notifType = "";
            this.productIdEdit = product.product_id;
            this.productNameEdit = product.product_name;
            this.productPriceEdit = product.product_price;
            this.productDescriptionEdit = product.product_description;
            this.mediaID = product.product_image;
            this.mediaPathEdit = product.media_path;
        },

        // Get Item Edit Product
        editItem: function(product) {
            this.modalEdit = true;
            this.show = false;
            this.notifType = "";
            this.productIdEdit = product.product_id;
            this.productNameEdit = product.product_name;
            this.productPriceEdit = product.product_price;
            this.productDescriptionEdit = product.product_description;
            this.mediaID = product.product_image;
            this.mediaPathEdit = product.media_path;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update Product
        updateProduct: function() {
            this.loading = true;
            axios.put(`/api/product/update/${this.productIdEdit}`, {
                    product_name: this.productNameEdit,
                    product_price: this.productPriceEdit,
                    product_description: this.productDescriptionEdit,
                    product_image: this.mediaID
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
                        this.getProducts();
                        this.modalEdit = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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

        // Get Item Delete Product
        deleteItem: function(product) {
            this.modalDelete = true;
            this.productIdDelete = product.product_id;
            this.productNameDelete = product.product_name;
        },

        // Delete Product
        deleteProduct: function() {
            this.loading = true;
            axios.delete(`/api/product/delete/${this.productIdDelete}`, options)
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
                        this.getProducts();
                        this.modalDelete = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.modalDelete = true;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                })
        },

        // Set Item Product Price
        setPrice: function(product) {
            this.loading = true;
            this.productIdEdit = product.product_id;
            this.product_price = product.product_price;
            axios.put(`/api/product/setprice/${this.productIdEdit}`, {
                    product_price: this.product_price,
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
                        this.getProducts();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },

        // Set Stock Product
        setStock: function(product) {
            this.loading = true;
            this.productIdEdit = product.product_id;
            this.stock = product.stock;
            axios.put(`/api/product/setstock/${this.productIdEdit}`, {
                    stock: this.stock,
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
                        this.getProducts();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                })
        },

        // Set Item Active Product
        setActive: function(product) {
            this.loading = true;
            this.productIdEdit = product.product_id;
            this.active = product.active;
            axios.put(`/api/product/setactive/${this.productIdEdit}`, {
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
                        this.getProducts();
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