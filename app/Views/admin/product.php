<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-3 font-weight-medium"><?= lang('App.listProduct') ?></h1>
<v-row class="fill-height">
    <v-col>
        <!-- Table List Product -->
        <v-card outlined elevation="1">
            <v-card-title>
                <!-- Button Add New Product -->
                <v-btn large color="primary" dark @click="modalAddOpen" elevation="1"><v-icon>mdi-plus</v-icon> <?= lang('App.add') ?></v-btn>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
                </v-text-field>
            </v-card-title>

            <v-data-table v-model="selected" item-key="product_id" show-select :headers="headers" :items="products" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="Sedang memuat... Harap tunggu" dense>
                <template v-slot:item="{ item, isSelected, select}">
                    <tr :class="isSelected ? 'grey lighten-2':'' || item.stock <= item.stock_min ? 'red lighten-4':''" @click="toggle(isSelected,select,$event)">
                        <td>
                            <v-icon color="primary" v-if="isSelected">mdi-checkbox-marked</v-icon>
                            <v-icon v-else>mdi-checkbox-blank-outline</v-icon>
                        </td>
                        <td style="max-width:300px">
                            <v-list-item class="ma-n3 pa-n3" two-line>
                                <v-list-item-avatar size="80" rounded>
                                    <v-img :src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                                    <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <p class="text-subtitle-2 text-underlined primary--text">{{item.product_name}}</p>
                                    <p class="mb-0">SKU: {{item.product_sku ?? "-"}}</p>
                                </v-list-item-content>
                            </v-list-item>
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
                            <v-btn icon class="mr-2" @click="showItem(item)" title="Show" alt="Show">
                                <v-icon color="info">mdi-eye</v-icon>
                            </v-btn>
                            <v-btn icon class="mr-2" @click="editItem(item)" title="Edit" alt="Edit">
                                <v-icon color="primary">mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="deleteItem(item)" title="Delete" alt="Delete">
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
        <v-dialog v-model="modalAdd" persistent scrollable width="1000px">
            <v-card>
                <v-card-title>
                    <?= lang('App.addProduct') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <v-row>
                            <v-col class="mb-n10" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productImg') ?></p>
                                <p class="text-caption">Format gambar .jpg .jpeg .png </p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-row>
                                    <v-col>
                                        <v-file-input v-model="image" single-line label="Cover" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" :loading="loading1" class="mb-0" v-show="imagePreview == null"></v-file-input>

                                        <div v-show="imagePreview">
                                            <v-img :src="imagePreview" max-width="150" class="mt-n3 mb-3">
                                                <v-overlay v-model="overlay" absolute :opacity="0.1">
                                                    <v-btn small class="ma-2" color="error" dark @click="deleteMedia" :loading="loading1">
                                                        <?= lang('App.delete') ?>
                                                        <v-icon dark right>
                                                            mdi-delete
                                                        </v-icon>
                                                    </v-btn>
                                                </v-overlay>
                                            </v-img>
                                        </div>
                                    </v-col>
                                    <v-col>
                                        <v-file-input v-model="image1" single-line label="Image 1" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange1" :loading="loading2" class="mb-0" v-show="imagePreview1 == null"></v-file-input>

                                        <div v-show="imagePreview1">
                                            <v-img :src="imagePreview1" max-width="150" class="mt-n3 mb-3">
                                                <v-overlay v-model="overlay1" absolute :opacity="0.1">
                                                    <v-btn small class="ma-2" color="error" dark @click="deleteMedia1" :loading="loading2">
                                                        <?= lang('App.delete') ?>
                                                        <v-icon dark right>
                                                            mdi-delete
                                                        </v-icon>
                                                    </v-btn>
                                                </v-overlay>
                                            </v-img>
                                        </div>
                                    </v-col>
                                    <v-col>
                                        <v-file-input v-model="image2" single-line label="Image 2" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange2" :loading="loading3" class="mb-0" v-show="imagePreview2 == null"></v-file-input>

                                        <div v-show="imagePreview2">
                                            <v-img :src="imagePreview2" max-width="150" class="mt-n3 mb-3">
                                                <v-overlay v-model="overlay2" absolute :opacity="0.1">
                                                    <v-btn small class="ma-2" color="error" dark @click="deleteMedia2" :loading="loading3">
                                                        <?= lang('App.delete') ?>
                                                        <v-icon dark right>
                                                            mdi-delete
                                                        </v-icon>
                                                    </v-btn>
                                                </v-overlay>
                                            </v-img>
                                        </div>
                                    </v-col>
                                    <v-col>
                                        <v-file-input v-model="image3" single-line label="Image 3" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange3" :loading="loading4" class="mb-0" v-show="imagePreview3 == null"></v-file-input>

                                        <div v-show="imagePreview3">
                                            <v-img :src="imagePreview3" max-width="150" class="mt-n3 mb-3">
                                                <v-overlay v-model="overlay3" absolute :opacity="0.1">
                                                    <v-btn small class="ma-2" color="error" dark @click="deleteMedia3" :loading="loading4">
                                                        <?= lang('App.delete') ?>
                                                        <v-icon dark right>
                                                            mdi-delete
                                                        </v-icon>
                                                    </v-btn>
                                                </v-overlay>
                                            </v-img>
                                        </div>
                                    </v-col>
                                    <v-col>
                                        <v-file-input v-model="image4" single-line label="Image 4" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange4" :loading="loading5" class="mb-0" v-show="imagePreview4 == null"></v-file-input>

                                        <div v-show="imagePreview4">
                                            <v-img :src="imagePreview4" max-width="150" class="mt-n3 mb-3">
                                                <v-overlay v-model="overlay3" absolute :opacity="0.1">
                                                    <v-btn small class="ma-2" color="error" dark @click="deleteMedia4" :loading="loading5">
                                                        <?= lang('App.delete') ?>
                                                        <v-icon dark right>
                                                            mdi-delete
                                                        </v-icon>
                                                    </v-btn>
                                                </v-overlay>
                                            </v-img>
                                        </div>
                                    </v-col>
                                </v-row>

                            </v-col>
                        </v-row>
                        <v-row class="mt-n3">
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productName') ?></p>
                                <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, warna, bahan, tipe.</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productName') ?> *" v-model="productName" :error-messages="product_nameError" outlined>
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productPrice') ?></p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productPrice') ?> (Rp)" v-model="productPrice" type="number" :error-messages="product_priceError" outlined>
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productDesc') ?></p>
                                <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya. Semakin detail, semakin berguna bagi pembeli, cantumkan min. 260 karakter agar pembeli semakin mudah mengerti dan menemukan barang anda</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-textarea v-model="productDescription" counter maxlength="3000" :error-messages="product_descriptionError" rows="10" outlined full-width single-line></v-textarea>
                            </v-col>
                        </v-row>

                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveProduct" :loading="loading">
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
        <v-dialog v-model="modalShow" persistent scrollable width="1000px">
            <v-card>
                <v-card-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalShow = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="py-5">
                    <v-row>
                        <v-col class="mb-n10" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productImg') ?></p>
                            <p class="text-caption">Format gambar .jpg .jpeg .png </p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-row>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" />
                                        </v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media1" v-bind:src="'<?= base_url(); ?>' + mediaPath1" />
                                        </v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media2" v-bind:src="'<?= base_url(); ?>' + mediaPath2" />
                                        </v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media3" v-bind:src="'<?= base_url(); ?>' + mediaPath3" />
                                        </v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media3" v-bind:src="'<?= base_url(); ?>' + mediaPath4" />
                                        </v-img>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productName') ?></p>
                            <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, warna, bahan, tipe.</p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-text-field label="<?= lang('App.productName') ?> *" v-model="productNameEdit" outlined></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productPrice') ?></p>
                            <p class="text-caption"></p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-text-field label="<?= lang('App.productPrice') ?> (Rp)" v-model="productPriceEdit" type="number" outlined></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productDesc') ?></p>
                            <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya. Semakin detail, semakin berguna bagi pembeli, cantumkan min. 260 karakter agar pembeli semakin mudah mengerti dan menemukan barang anda</p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-textarea v-model="productDescriptionEdit" counter maxlength="3000" rows="10" outlined full-width single-line></v-textarea>
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
        <v-dialog v-model="modalEdit" persistent scrollable width="1000px">
            <v-card>
                <v-card-title>
                    <?= lang('App.editProduct') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <v-row>
                            <v-col class="mb-n10" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productImg') ?></p>
                                <p class="text-caption">Format gambar .jpg .jpeg .png </p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-row>
                                    <v-col>
                                        <template v-if="mediaPath != null">
                                            <v-hover>
                                                <template v-slot:default="{ hover }">
                                                    <v-card max-width="120">
                                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" />
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia(mediaID)" :loading="loading1">
                                                                    <?= lang('App.delete') ?>
                                                                    <v-icon>
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-overlay>
                                                        </v-fade-transition>
                                                    </v-card>
                                                </template>
                                            </v-hover>
                                        </template>
                                        <template v-else>
                                            <v-file-input v-model="image" single-line label="Cover" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" :loading="loading1" class="mb-0" v-show="imagePreview == null"></v-file-input>

                                            <div v-show="imagePreview">
                                                <v-img :src="imagePreview" max-width="150" class="mt-n3 mb-3">
                                                    <v-overlay v-model="overlay" absolute :opacity="0.1">
                                                        <v-btn small class="ma-2" color="error" dark @click="deleteMedia" :loading="loading1">
                                                            <?= lang('App.delete') ?>
                                                            <v-icon dark right>
                                                                mdi-delete
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-overlay>
                                                </v-img>
                                            </div>
                                        </template>
                                    </v-col>
                                    <v-col>
                                        <template v-if="mediaPath1 != null">
                                            <v-hover>
                                                <template v-slot:default="{ hover }">
                                                    <v-card max-width="120">
                                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" />
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia(mediaID)" :loading="loading1">
                                                                    <?= lang('App.delete') ?>
                                                                    <v-icon>
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-overlay>
                                                        </v-fade-transition>
                                                    </v-card>
                                                </template>
                                            </v-hover>
                                        </template>
                                        <template v-else>
                                            <v-file-input v-model="image1" single-line label="Image 1" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange1" :loading="loading2" class="mb-0" v-show="imagePreview1 == null"></v-file-input>

                                            <div v-show="imagePreview1">
                                                <v-img :src="imagePreview1" max-width="150" class="mt-n3 mb-3">
                                                    <v-overlay v-model="overlay1" absolute :opacity="0.1">
                                                        <v-btn small class="ma-2" color="error" dark @click="deleteMedia1" :loading="loading2">
                                                            <?= lang('App.delete') ?>
                                                            <v-icon dark right>
                                                                mdi-delete
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-overlay>
                                                </v-img>
                                            </div>
                                        </template>
                                    </v-col>
                                    <v-col>
                                        <template v-if="mediaPath2 != null">
                                            <v-hover>
                                                <template v-slot:default="{ hover }">
                                                    <v-card max-width="120">
                                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" />
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia(mediaID)" :loading="loading1">
                                                                    <?= lang('App.delete') ?>
                                                                    <v-icon>
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-overlay>
                                                        </v-fade-transition>
                                                    </v-card>
                                                </template>
                                            </v-hover>
                                        </template>
                                        <template v-else>
                                            <v-file-input v-model="image2" single-line label="Image 2" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange2" :loading="loading3" class="mb-0" v-show="imagePreview2 == null"></v-file-input>

                                            <div v-show="imagePreview2">
                                                <v-img :src="imagePreview2" max-width="150" class="mt-n3 mb-3">
                                                    <v-overlay v-model="overlay2" absolute :opacity="0.1">
                                                        <v-btn small class="ma-2" color="error" dark @click="deleteMedia2" :loading="loading3">
                                                            <?= lang('App.delete') ?>
                                                            <v-icon dark right>
                                                                mdi-delete
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-overlay>
                                                </v-img>
                                            </div>
                                        </template>
                                    </v-col>
                                    <v-col>
                                        <template v-if="mediaPath3 != null">
                                            <v-hover>
                                                <template v-slot:default="{ hover }">
                                                    <v-card max-width="120">
                                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" />
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia(mediaID)" :loading="loading1">
                                                                    <?= lang('App.delete') ?>
                                                                    <v-icon>
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-overlay>
                                                        </v-fade-transition>
                                                    </v-card>
                                                </template>
                                            </v-hover>
                                        </template>
                                        <template v-else>
                                            <v-file-input v-model="image3" single-line label="Image 3" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange3" :loading="loading4" class="mb-0" v-show="imagePreview3 == null"></v-file-input>

                                            <div v-show="imagePreview3">
                                                <v-img :src="imagePreview3" max-width="150" class="mt-n3 mb-3">
                                                    <v-overlay v-model="overlay3" absolute :opacity="0.1">
                                                        <v-btn small class="ma-2" color="error" dark @click="deleteMedia3" :loading="loading4">
                                                            <?= lang('App.delete') ?>
                                                            <v-icon dark right>
                                                                mdi-delete
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-overlay>
                                                </v-img>
                                            </div>
                                        </template>
                                    </v-col>
                                    <v-col>
                                        <template v-if="mediaPath4 != null">
                                            <v-hover>
                                                <template v-slot:default="{ hover }">
                                                    <v-card max-width="120">
                                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" />
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia(mediaID)" :loading="loading1">
                                                                    <?= lang('App.delete') ?>
                                                                    <v-icon>
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-overlay>
                                                        </v-fade-transition>
                                                    </v-card>
                                                </template>
                                            </v-hover>
                                        </template>
                                        <template v-else>
                                            <v-file-input v-model="image4" single-line label="Image 4" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange4" :loading="loading5" class="mb-0" v-show="imagePreview4 == null"></v-file-input>

                                            <div v-show="imagePreview4">
                                                <v-img :src="imagePreview4" max-width="150" class="mt-n3 mb-3">
                                                    <v-overlay v-model="overlay3" absolute :opacity="0.1">
                                                        <v-btn small class="ma-2" color="error" dark @click="deleteMedia4" :loading="loading5">
                                                            <?= lang('App.delete') ?>
                                                            <v-icon dark right>
                                                                mdi-delete
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-overlay>
                                                </v-img>
                                            </div>
                                        </template>
                                    </v-col>
                                </v-row>

                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productName') ?></p>
                                <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, warna, bahan, tipe.</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productName') ?> *" v-model="productNameEdit" :rules="[rules.required]" outlined></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productPrice') ?></p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productPrice') ?> (Rp)" v-model="productPriceEdit" type="number" :rules="[rules.required]" outlined></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productDesc') ?></p>
                                <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya. Semakin detail, semakin berguna bagi pembeli, cantumkan min. 260 karakter agar pembeli semakin mudah mengerti dan menemukan barang anda</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-textarea v-model="productDescriptionEdit" counter maxlength="3000" rows="10" outlined full-width single-line></v-textarea>
                            </v-col>
                        </v-row>

                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateProduct" :loading="loading">
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
                    <v-btn large color="blue darken-1" text @click="modalDelete = false"><?= lang('App.no') ?></v-btn>
                    <v-btn large color="blue darken-1" dark @click="deleteProduct" :loading="loading"><?= lang('App.yes') ?></v-btn>
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
        selected: [],
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        productName: "",
        product_nameError: "",
        productPrice: "",
        product_priceError: "",
        productDescription: "",
        product_descriptionError: "",
        productImage: null,
        product_imageError: "",
        productImage1: null,
        product_image1Error: "",
        productImage2: null,
        product_image2Error: "",
        productImage3: null,
        product_image3Error: "",
        productImage4: null,
        product_image4Error: "",
        stock: "",
        stockError: "",
        active: "",
        activeError: "",
        productIdEdit: "",
        productNameEdit: "",
        productPriceEdit: "",
        productDescriptionEdit: "",
        productImageEdit: null,
        mediaPathEdit: null,
        productIdDelete: "",
        productNameDelete: "",
        mediaID: "",
        media1: "",
        media2: "",
        media3: "",
        media4: "",
        image: null,
        image1: null,
        image2: null,
        image3: null,
        image4: null,
        mediaPath: null,
        mediaPath1: null,
        mediaPath2: null,
        mediaPath3: null,
        mediaPath4: null,
        imagePreview: null,
        imagePreview1: null,
        imagePreview2: null,
        imagePreview3: null,
        imagePreview4: null,
        overlay: false,
        overlay1: false,
        overlay2: false,
        overlay3: false,
        overlay4: false,
        alert: false,
    }

    createdVue = function() {
        this.getProducts();
    }

    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    watchVue = {
        ...watchVue,
        alert: function() {
            if (this.alert == true) {
                window.onbeforeunload = function() {
                    return "Data will be lost if you leave the page, are you sure?";
                };
            } else {
                window.onbeforeunload = null;
            }
        },
    }

    methodsVue = {
        ...methodsVue,
        toggle(isSelected, select, e) {
            select(!isSelected)
        },

        modalAddOpen: function() {
            this.modalAdd = true;
            this.productImage = null;
            this.notifType = "";
        },
        modalAddClose: function() {
            this.productName = "";
            this.productPrice = "";
            this.productDescription = "";
            this.productImage = null;
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },
        // Get Product
        getProducts: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/product', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
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
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },
        // Upload Browse File
        onFileChange() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image)
            reader.onload = e => {
                this.imagePreview = e.target.result;
                this.uploadFile(this.imagePreview);
            }
        },
        // Upload Clear File
        onFileClear() {
            this.image = null;
            this.imagePreview = null;
            this.overlay = false;
            this.snackbar = true;
            this.snackbarMessage = 'Gambar berhasil dihapus';
            this.deleteMedia();
        },
        uploadFile: function(file) {
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
            axios.post(`<?= base_url() ?>api/media/save`, formData, options)
                .then(res => {
                    // handle success
                    this.loading1 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.mediaID = data.data;
                        this.overlay = true;
                        this.alert = true;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        // Delete Media
        deleteMedia: function() {
            this.loading1 = true;
            axios.delete(`<?= base_url() ?>api/media/delete/${this.mediaID}`, options)
                .then(res => {
                    // handle success
                    this.loading1 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image = null;
                        this.imagePreview = null;
                        this.overlay = false;
                        this.alert = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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
        // Upload Browse File 1
        onFileChange1() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image1)
            reader.onload = e => {
                this.imagePreview1 = e.target.result;
                this.uploadFile1(this.imagePreview1);
            }
        },
        // Upload Clear File 1
        onFileClear1() {
            this.image1 = null;
            this.imagePreview1 = null;
            this.overlay1 = false;
            this.snackbar = true;
            this.snackbarMessage = 'Gambar berhasil dihapus';
            this.deleteMedia1();
        },
        uploadFile1: function(file) {
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
            axios.post(`<?= base_url() ?>api/media/save`, formData, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.media1 = data.data;
                        this.overlay1 = true;
                        this.alert = true;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        // Delete Media 1
        deleteMedia1: function() {
            this.loading2 = true;
            axios.delete(`<?= base_url() ?>api/media/delete/${this.media1}`, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image1 = null;
                        this.imagePreview1 = null;
                        this.overlay1 = false;
                        this.alert = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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
        // Upload Browse File 2
        onFileChange2() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image2)
            reader.onload = e => {
                this.imagePreview2 = e.target.result;
                this.uploadFile2(this.imagePreview2);
            }
        },
        // Upload Clear File 2
        onFileClear2() {
            this.image2 = null;
            this.imagePreview2 = null;
            this.overlay2 = false;
            this.snackbar = true;
            this.snackbarMessage = 'Gambar berhasil dihapus';
            this.deleteMedia2();
        },
        uploadFile2: function(file) {
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
            axios.post(`<?= base_url() ?>api/media/save`, formData, options)
                .then(res => {
                    // handle success
                    this.loading3 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.media2 = data.data;
                        this.overlay2 = true;
                        this.alert = true;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        // Delete Media 2
        deleteMedia2: function() {
            this.loading3 = true;
            axios.delete(`<?= base_url() ?>api/media/delete/${this.media2}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image2 = null;
                        this.imagePreview2 = null;
                        this.overlay2 = false;
                        this.alert = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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
        // Upload Browse File 3
        onFileChange3() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image3)
            reader.onload = e => {
                this.imagePreview3 = e.target.result;
                this.uploadFile3(this.imagePreview3);
            }
        },
        // Upload Clear File 3
        onFileClear3() {
            this.image3 = null;
            this.imagePreview3 = null;
            this.overlay3 = false;
            this.snackbar = true;
            this.snackbarMessage = 'Gambar berhasil dihapus';
            this.deleteMedia3();
        },
        uploadFile3: function(file) {
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
            axios.post(`<?= base_url() ?>api/media/save`, formData, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.media3 = data.data;
                        this.overlay3 = true;
                        this.alert = true;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        // Delete Media 3
        deleteMedia3: function() {
            this.loading4 = true;
            axios.delete(`<?= base_url() ?>api/media/delete/${this.media3}`, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image3 = null;
                        this.imagePreview3 = null;
                        this.overlay3 = false;
                        this.alert = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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
        // Upload Browse File 4
        onFileChange4() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image4)
            reader.onload = e => {
                this.imagePreview4 = e.target.result;
                this.uploadFile4(this.imagePreview4);
            }
        },
        // Upload Clear File 4
        onFileClear4() {
            this.image4 = null;
            this.imagePreview4 = null;
            this.overlay4 = false;
            this.snackbar = true;
            this.snackbarMessage = 'Gambar berhasil dihapus';
            this.deleteMedia4();
        },
        uploadFile4: function(file) {
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
            axios.post(`<?= base_url() ?>api/media/save`, formData, options)
                .then(res => {
                    // handle success
                    this.loading5 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.media4 = data.data;
                        this.overlay4 = true;
                        this.alert = true;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        // Delete Media 4
        deleteMedia4: function() {
            this.loading5 = true;
            axios.delete(`<?= base_url() ?>api/media/delete/${this.media4}`, options)
                .then(res => {
                    // handle success
                    this.loading5 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image4 = null;
                        this.imagePreview4 = null;
                        this.overlay4 = false;
                        this.alert = false;
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
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

        // Save Product
        saveProduct: function() {
            this.loading = true;
            axios.post(`<?= base_url() ?>api/product/save`, {
                    product_name: this.productName,
                    product_price: this.productPrice,
                    product_description: this.productDescription,
                    product_image: this.mediaID,
                    product_image1: this.media1,
                    product_image2: this.media2,
                    product_image3: this.media3,
                    product_image4: this.media4,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getProducts();
                        this.productName = "";
                        this.productPrice = "";
                        this.productDescription = "";
                        this.productImage = null;
                        this.productImage1 = null;
                        this.productImage2 = null;
                        this.productImage3 = null;
                        this.productImage4 = null;
                        this.mediaID = "";
                        this.media1 = "";
                        this.media2 = "";
                        this.media3 = "";
                        this.media4 = "";
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.modalAdd = true;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                        this.$refs.form.validate();
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
            this.media1 = product.product_image1;
            this.media2 = product.product_image2;
            this.media3 = product.product_image3;
            this.media4 = product.product_image4;
            this.mediaPath = product.media_path;
            this.mediaPath1 = product.media_path1;
            this.mediaPath2 = product.media_path2;
            this.mediaPath3 = product.media_path3;
            this.mediaPath4 = product.media_path4;
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
            this.media1 = product.product_image1;
            this.media2 = product.product_image2;
            this.media3 = product.product_image3;
            this.media4 = product.product_image4;
            this.mediaPath = product.media_path;
            this.mediaPath1 = product.media_path1;
            this.mediaPath2 = product.media_path2;
            this.mediaPath3 = product.media_path3;
            this.mediaPath4 = product.media_path4;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update Product
        updateProduct: function() {
            this.loading = true;
            axios.put(`<?= base_url() ?>api/product/update/${this.productIdEdit}`, {
                    product_name: this.productNameEdit,
                    product_price: this.productPriceEdit,
                    product_description: this.productDescriptionEdit,
                    product_image: this.mediaID,
                    product_image1: this.media1,
                    product_image2: this.media2,
                    product_image3: this.media3,
                    product_image4: this.media4,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
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
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
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
            axios.delete(`<?= base_url() ?>api/product/delete/${this.productIdDelete}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
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
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Item Product Price
        setPrice: function(product) {
            this.loading = true;
            this.productIdEdit = product.product_id;
            this.product_price = product.product_price;
            axios.put(`<?= base_url() ?>api/product/setprice/${this.productIdEdit}`, {
                    product_price: this.product_price,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getProducts();
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

        // Set Stock Product
        setStock: function(product) {
            this.loading = true;
            this.productIdEdit = product.product_id;
            this.stock = product.stock;
            axios.put(`<?= base_url() ?>api/product/setstock/${this.productIdEdit}`, {
                    stock: this.stock,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getProducts();
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

        // Set Item Active Product
        setActive: function(product) {
            this.loading = true;
            this.productIdEdit = product.product_id;
            this.active = product.active;
            axios.put(`<?= base_url() ?>api/product/setactive/${this.productIdEdit}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getProducts();
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