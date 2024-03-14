<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="mb-2 font-weight-medium"><?= $title; ?></h1>
    <v-row>
        <v-col>
            <!-- Table List Product -->
            <v-card outlined elevation="1">
                <v-card-title>
                    <!-- Button Add New Product -->
                    <v-btn large color="primary" dark @click="modalAddOpen" elevation="1"><v-icon>mdi-plus</v-icon> <?= lang('App.add') ?></v-btn>
                    <v-spacer></v-spacer>
                    <v-text-field v-model="search" v-on:keydown.enter="getProducts" @click:clear="getProducts" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable>
                    </v-text-field>
                </v-card-title>

                <v-data-table v-model="selected" item-key="product_id" show-select :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading">
                    <template v-slot:item="{ item, isSelected, select}">
                        <tr :class="isSelected ? 'grey lighten-2':'' || item.stock <= item.stock_min ? 'red lighten-4':''">
                            <td @click="toggle(isSelected,select,$event)">
                                <v-icon color="primary" v-if="isSelected">mdi-checkbox-marked</v-icon>
                                <v-icon v-else>mdi-checkbox-blank-outline</v-icon>
                            </td>
                            <td style="max-width:300px">
                                <a link @click="showItem(item)" title="Detail Product" alt="Detail Product">
                                    <v-list-item class="ma-n3 pa-n3" two-line>
                                        <v-list-item-avatar size="80" rounded>
                                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                        </v-list-item-avatar>
                                        <v-list-item-content>
                                            <p class="text-subtitle-2 text-underlined primary--text">{{item.product_name}}</p>
                                            <p class="mb-0">Code: {{item.product_code ?? "-"}}</p>
                                        </v-list-item-content>
                                    </v-list-item>
                                </a>
                            </td>
                            <td>{{item.category_name}}</td>
                            <td>
                                <v-edit-dialog large :return-value.sync="item.product_price" @save="setPrice(item)" @cancel="" @open="" @close="">
                                    <div v-if="item.discount > 0"><span class="text-decoration-line-through">{{ Ribuan(item.product_price) }}</span>
                                        <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.discount_percent}}%</v-chip><br />{{ Ribuan(item.product_price - item.discount) }}
                                    </div>
                                    <div v-else>{{ Ribuan(item.product_price) }}</div>
                                    <template v-slot:input>
                                        <v-text-field v-model="item.product_price" type="number" min="0" single-line :disabled="item.discount > 0"></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </td>
                            <td>
                                <v-edit-dialog large :return-value.sync="item.stock" @save="setStock(item)" @cancel="" @open="" @close="">
                                    {{item.stock}}
                                    <template v-slot:input>
                                        <v-text-field v-model="item.stock" type="number" min="0" single-line></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </td>
                            <td>
                                <v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setActive(item)"></v-switch>
                            </td>
                            <td>
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
</template>

<!-- Modal -->
<!-- Modal Save Product -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" fullscreen persistent scrollable>
            <v-card>
                <v-card-title class="mb-1">
                    <v-btn icon @click="modalAddClose" class="mr-3">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <?= lang('App.addProduct') ?>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveProduct" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
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
                                        <v-file-input v-model="image" single-line label="Cover" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" :loading="loading1" class="mb-0" :error-messages="product_imageError" v-if="imagePreview == null"></v-file-input>

                                        <div v-else>
                                            <v-img :src="imagePreview" aspect-ratio="1" class="mb-3">
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
                                        <v-file-input v-model="image1" single-line label="Image 1" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange1" :loading="loading2" class="mb-0" v-if="imagePreview1 == null"></v-file-input>

                                        <div v-else>
                                            <v-img :src="imagePreview1" aspect-ratio="1" class="mb-3">
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
                                        <v-file-input v-model="image2" single-line label="Image 2" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange2" :loading="loading3" class="mb-0" v-if="imagePreview2 == null"></v-file-input>

                                        <div v-else>
                                            <v-img :src="imagePreview2" aspect-ratio="1" class="mb-3">
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
                                        <v-file-input v-model="image3" single-line label="Image 3" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange3" :loading="loading4" class="mb-0" v-if="imagePreview3 == null"></v-file-input>

                                        <div v-else>
                                            <v-img :src="imagePreview3" aspect-ratio="1" class="mb-3">
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
                                        <v-file-input v-model="image4" single-line label="Image 4" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange4" :loading="loading5" class="mb-0" v-if="imagePreview4 == null"></v-file-input>

                                        <div v-else>
                                            <v-img :src="imagePreview4" aspect-ratio="1" class="mb-3">
                                                <v-overlay v-model="overlay4" absolute :opacity="0.1">
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
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productName') ?>*</p>
                                <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, warna, bahan, tipe.</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productName') ?>" v-model="productName" :error-messages="product_nameError" outlined>
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" md="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.category') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" md="9">
                                <v-select v-model="idCategory" label="<?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" :error-messages="category_idError" :loading="loading2" outlined append-outer-icon="mdi-plus-thick" @click:append-outer="addCategory"></v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productCode') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productCode') ?>" v-model="productCode" :error-messages="product_codeError" outlined></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productPrice') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="" v-model="productPrice" type="number" :error-messages="product_priceError" prefix="Rp" outlined>
                                </v-text-field>

                                <v-checkbox v-model="checkDiscount" label="Aktifkan <?= lang('App.discount') ?>" class="mt-n2"></v-checkbox>
                                <v-text-field v-model="discount" label="<?= lang('App.discount') ?> (Rp)" type="number" :error-messages="discountError" prefix="Rp" :suffix=" discountPercent.toFixed() + '%'" @focus="$event.target.select()" outlined v-show="checkDiscount == true"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" md="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.stock') ?></p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" md="5">
                                <v-text-field label="1000" v-model="stock" type="number" :error-messages="stockError" single-line outlined></v-text-field>
                            </v-col>

                            <v-col cols="12" md="4">
                                <v-row class="mt-n5">
                                    <v-col cols="12" md="5">
                                        <span class="text-subtitle-1 font-weight-bold mb-0"><?= lang('App.stock') ?> Min</span>
                                    </v-col>
                                    <v-col cols="12" md="7">
                                        <v-text-field label="0" v-model="stockMin" type="number" :error-messages="stock_minError" single-line outlined></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productDesc') ?></p>
                                <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya. Semakin detail, semakin berguna bagi pembeli, cantumkan min. 260 karakter agar pembeli semakin mudah mengerti dan menemukan barang anda</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <quill-editor v-model="productDescription" ref="quillEditor" :options="editorOption" :error-messages="product_descriptionError" style="min-height:200px !important;"></quill-editor>
                                    <!-- <v-textarea v-model="productDescription" counter maxlength="3000" :error-messages="product_descriptionError" rows="10" outlined full-width single-line></v-textarea> -->
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold">Link Demo</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="Link Demo" v-model="linkDemo" type="text" outlined></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Save Product -->

<!-- Modal Show Product -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalShow" fullscreen persistent scrollable>
            <v-card>
                <v-card-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalShow = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
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
                                        <v-img v-model="mediaID" lazy-src="<?= base_url('images/no_image.jpg') ?>" v-bind:src="'<?= base_url(); ?>' + mediaPath" aspect-ratio="1" v-if="mediaPath != null"></v-img>
                                        <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media1" lazy-src="<?= base_url('images/no_image.jpg') ?>" v-bind:src="'<?= base_url(); ?>' + mediaPath1" aspect-ratio="1" v-if="mediaPath1 != null"></v-img>
                                        <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media2" lazy-src="<?= base_url('images/no_image.jpg') ?>" v-bind:src="'<?= base_url(); ?>' + mediaPath2" aspect-ratio="1" v-if="mediaPath2 != null"></v-img>
                                        <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media3" lazy-src="<?= base_url('images/no_image.jpg') ?>" v-bind:src="'<?= base_url(); ?>' + mediaPath3" aspect-ratio="1" v-if="mediaPath3 != null"></v-img>
                                        <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                    </v-card>
                                </v-col>
                                <v-col>
                                    <v-card max-width="120">
                                        <v-img v-model="media3" lazy-src="<?= base_url('images/no_image.jpg') ?>" v-bind:src="'<?= base_url(); ?>' + mediaPath4" aspect-ratio="1" v-if="mediaPath4 != null"></v-img>
                                        <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productName') ?>*</p>
                            <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, warna, bahan, tipe.</p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-text-field label="<?= lang('App.productName') ?>" v-model="productNameEdit" outlined></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" md="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.category') ?>*</p>
                            <p class="text-caption"></p>
                        </v-col>
                        <v-col cols="12" md="9">
                            <v-select v-model="idCategoryEdit" label="<?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" :error-messages="category_idError" :loading="loading2" outlined append-outer-icon="mdi-plus-thick" @click:append-outer="addCategory"></v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productCode') ?>*</p>
                            <p class="text-caption"></p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-text-field label="<?= lang('App.productCode') ?>" v-model="productCodeEdit" type="text" outlined></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productPrice') ?></p>
                            <p class="text-caption"></p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-text-field label="" v-model="productPriceEdit" type="number" prefix="Rp" outlined></v-text-field>

                            <v-checkbox v-model="checkDiscount" label="Aktifkan <?= lang('App.discount') ?>" class="mt-n2"></v-checkbox>
                            <v-text-field v-model="discountEdit" label="<?= lang('App.discount') ?> (Rp)" type="number" :error-messages="discountError" prefix="Rp" :suffix=" discountPercentEdit.toFixed() + '%'" @focus="$event.target.select()" outlined v-show="checkDiscount == true"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" md="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.stock') ?></p>
                            <p class="text-caption"></p>
                        </v-col>
                        <v-col cols="12" md="5">
                            <v-text-field label="1000" v-model="stock" type="number" :error-messages="stockError" single-line outlined></v-text-field>
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-row class="mt-n5">
                                <v-col cols="12" md="5">
                                    <span class="text-subtitle-1 font-weight-bold mb-0"><?= lang('App.stock') ?> Min</span>
                                </v-col>
                                <v-col cols="12" md="7">
                                    <v-text-field label="0" v-model="stockMin" type="number" :error-messages="stock_minError" single-line outlined></v-text-field>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productDesc') ?></p>
                            <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya. Semakin detail, semakin berguna bagi pembeli, cantumkan min. 260 karakter agar pembeli semakin mudah mengerti dan menemukan barang anda</p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <quill-editor v-model="productDescriptionEdit" ref="quillEditor" :options="editorOption" :error-messages="product_descriptionError"></quill-editor>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col class="mb-n5" cols="12" sm="3">
                            <p class="mb-1 text-subtitle-1 font-weight-bold">Link Demo</p>
                            <p class="text-caption"></p>
                        </v-col>
                        <v-col cols="12" sm="9">
                            <v-text-field label="Link Demo" v-model="linkdemoEdit" type="text" outlined></v-text-field>
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
        <v-dialog v-model="modalEdit" fullscreen persistent scrollable>
            <v-card>
                <v-card-title class="mb-1">
                    <v-btn icon @click="modalEditClose" class="mr-3">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <?= lang('App.editProduct') ?>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateProduct" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.update') ?>
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
                                                    <v-card>
                                                        <v-img v-model="mediaID" v-bind:src="'<?= base_url(); ?>' + mediaPath" aspect-ratio="1">
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia" :loading="loading1">
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
                                            <v-file-input v-model="image" single-line label="Cover" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" :loading="loading1" class="mb-0" :error-messages="product_imageError" v-if="imagePreview == null"></v-file-input>

                                            <div v-else>
                                                <v-img :src="imagePreview" aspect-ratio="1" class="mb-3">
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
                                                    <v-card>
                                                        <v-img v-model="media1" v-bind:src="'<?= base_url(); ?>' + mediaPath1" aspect-ratio="1">
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia1" :loading="loading1">
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
                                            <v-file-input v-model="image1" single-line label="Image 1" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange1" :loading="loading2" class="mb-0" v-if="imagePreview1 == null"></v-file-input>

                                            <div v-else>
                                                <v-img :src="imagePreview1" aspect-ratio="1" class="mb-3">
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
                                                    <v-card>
                                                        <v-img v-model="media2" v-bind:src="'<?= base_url(); ?>' + mediaPath2" aspect-ratio="1">
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia2" :loading="loading1">
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
                                            <v-file-input v-model="image2" single-line label="Image 2" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange2" :loading="loading3" class="mb-0" v-if="imagePreview2 == null"></v-file-input>

                                            <div v-else>
                                                <v-img :src="imagePreview2" aspect-ratio="1" class="mb-3">
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
                                                    <v-card>
                                                        <v-img v-model="media3" v-bind:src="'<?= base_url(); ?>' + mediaPath3" aspect-ratio="1">
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia3" :loading="loading1">
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
                                            <v-file-input v-model="image3" single-line label="Image 3" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange3" :loading="loading4" class="mb-0" v-if="imagePreview3 == null"></v-file-input>

                                            <div v-else>
                                                <v-img :src="imagePreview3" aspect-ratio="1" class="mb-3">
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
                                                    <v-card>
                                                        <v-img v-model="media4" v-bind:src="'<?= base_url(); ?>' + mediaPath4" aspect-ratio="1">
                                                        </v-img>

                                                        <v-fade-transition>
                                                            <v-overlay v-if="hover" absolute color="#036358">
                                                                <v-btn small color="red" dark @click="deleteMedia4" :loading="loading1">
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
                                            <v-file-input v-model="image4" single-line label="Image 4" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange4" :loading="loading5" class="mb-0" v-if="imagePreview4 == null"></v-file-input>

                                            <div v-else>
                                                <v-img :src="imagePreview4" aspect-ratio="1" class="mb-3">
                                                    <v-overlay v-model="overlay4" absolute :opacity="0.1">
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
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productName') ?>*</p>
                                <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, warna, bahan, tipe.</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productName') ?>" v-model="productNameEdit" :error-messages="product_nameError" outlined></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" md="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.category') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" md="9">
                                <v-select v-model="idCategoryEdit" label="<?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" :error-messages="category_idError" :loading="loading2" outlined append-outer-icon="mdi-plus-thick" @click:append-outer="addCategory"></v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productCode') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="<?= lang('App.productCode') ?>" v-model="productCodeEdit" :error-messages="product_codeError" outlined></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productPrice') ?></p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="" v-model="productPriceEdit" type="number" :error-messages="product_priceError" prefix="Rp" outlined></v-text-field>

                                <v-checkbox v-model="checkDiscount" label="Aktifkan <?= lang('App.discount') ?>" class="mt-n2"></v-checkbox>
                                <v-text-field v-model="discountEdit" label="<?= lang('App.discount') ?> (Rp)" type="number" :error-messages="discountError" prefix="Rp" :suffix=" discountPercentEdit.toFixed() + '%'" @focus="$event.target.select()" outlined v-show="checkDiscount == true"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" md="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.stock') ?></p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" md="5">
                                <v-text-field label="1000" v-model="stockEdit" type="number" :error-messages="stockError" single-line outlined></v-text-field>
                            </v-col>

                            <v-col cols="12" md="4">
                                <v-row class="mt-n5">
                                    <v-col cols="12" md="5">
                                        <span class="text-subtitle-1 font-weight-bold mb-0"><?= lang('App.stock') ?> Min</span>
                                    </v-col>
                                    <v-col cols="12" md="7">
                                        <v-text-field label="0" v-model="stockMinEdit" type="number" :error-messages="stock_minError" single-line outlined></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productDesc') ?></p>
                                <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya. Semakin detail, semakin berguna bagi pembeli, cantumkan min. 260 karakter agar pembeli semakin mudah mengerti dan menemukan barang anda</p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <quill-editor v-model="productDescriptionEdit" ref="quillEditor" :options="editorOption" :error-messages="product_descriptionError"></quill-editor>
                                    <!-- <v-textarea v-model="productDescriptionEdit" counter maxlength="3000" rows="10" :error-messages="product_descriptionError" outlined full-width single-line></v-textarea> -->
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="mb-n5" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold">Link Demo</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-text-field label="Link Demo" v-model="linkdemoEdit" type="text" outlined></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
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
                <v-card-title>
                    <v-icon color="error" class="mr-2" x-large>mdi-alert-octagon</v-icon> <?= lang('App.confirm'); ?> <?= lang('App.delete'); ?>
                </v-card-title>
                <v-card-text>
                    <div class="mt-3 py-4">
                        <h2 class="font-weight-regular"><?= lang('App.delConfirm') ?></h2>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large text @click="modalDelete = false"><?= lang('App.no') ?></v-btn>
                    <v-btn large color="error" dark @click="deleteProduct" :loading="loading"><?= lang('App.yes') ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete Product -->

<!-- Modal Kategori -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalCategory" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <?= lang('App.category') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalCategoryClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-container>
                            <v-row>
                                <v-col cols="12" md="7">
                                    <v-text-field label="Nama Kategori" v-model="nameCategory" type="text" :error-messages="category_nameError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="5">
                                    <v-btn color="primary" large @click="saveCategory" :loading="loading6"><?= lang('App.add') ?></v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                    <v-data-table :headers="tbCategory" :items="dataCategory" :items-per-page="5" class="elevation-1" :loading="loading1">
                        <template v-slot:item.actions="{ item }">
                            <v-btn color="error" icon @click="deleteCategory(item)" :loading="loading7">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalCategoryClose"><?= lang('App.close') ?></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

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

    // Deklarasi errorKeys
    var errorKeys = []

    dataVue = {
        ...dataVue,
        search: "",
        dataTable: [{
            text: '<?= lang('App.productInfo') ?>',
            value: 'product_name'
        }, {
            text: '<?= lang('App.category') ?>',
            value: 'category_name'
        }, {
            text: '<?= lang('App.price') ?>',
            value: 'product_price'
        }, {
            text: '<?= lang('App.stock') ?>',
            value: 'stock'
        }, {
            text: '<?= lang('App.active') ?>',
            value: 'active'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        products: [],
        totalData: 0,
        data: [],
        options: {},
        selected: [],
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        productCode: "",
        product_codeError: "",
        productName: "",
        product_nameError: "",
        productPrice: 0,
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
        linkDemo: null,
        link_demoError: "",
        checkDiscount: false,
        discount: 0,
        discountEdit: 0,
        discountError: "",
        discountPercent: 0,
        discountPercentEdit: 0,
        stock: "",
        stockError: "",
        active: "",
        activeError: "",
        productIdEdit: "",
        productCodeEdit: "",
        productNameEdit: "",
        productPriceEdit: 0,
        productDescriptionEdit: "",
        productImageEdit: null,
        mediaPathEdit: null,
        productIdDelete: "",
        productNameDelete: "",
        linkdemoEdit: "",
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
        stock: 1000,
        stockEdit: "",
        stockError: "",
        stockMin: 0,
        stockMinEdit: "",
        stock_minError: "",

        modalCategory: false,
        dataCategory: [],
        idCategory: "",
        idCategoryEdit: "",
        category_idError: "",
        nameCategory: "",
        nameCategoryEdit: "",
        category_nameError: "",
        tbCategory: [{
            text: '#',
            value: 'category_id'
        }, {
            text: 'Nama Kategori',
            value: 'category_name'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
    }

    createdVue = function() {
        this.getProducts();
        this.getCategory();
    }

    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        products: function() {
            if (this.products != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        },

        discount: function() {
            if (Number(this.discount) == 0) {
                this.discountPercent = 0;
            }

            if (Number(this.discount) > 0) {
                let hitung = Number(this.productPrice) - Number(this.discount)
                let persen = Number(this.productPrice) - hitung
                this.discountPercent = (persen / Number(this.productPrice)) * 100;
            }
        },

        discountEdit: function() {
            if (Number(this.discountEdit) == 0) {
                this.discountPercentEdit = 0;
            }

            if (Number(this.discountEdit) > 0) {
                let hitungEdit = Number(this.productPriceEdit) - Number(this.discountEdit)
                let persenEdit = Number(this.productPriceEdit) - hitungEdit
                this.discountPercentEdit = (persenEdit / Number(this.productPriceEdit)) * 100;
            }
        },

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

                let items = this.products;
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

        toggle(isSelected, select, e) {
            select(!isSelected)
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
                        this.products = data.data;
                        this.data = data.data;
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
            this.checkDiscount = false;
            this.discount = 0;
        },
        modalAddClose: function() {
            this.productCode = "";
            this.productName = "";
            this.productPrice = "";
            this.productDescription = "";
            if (this.alert == true) {
                this.modalAdd = true;
                this.snackbar = true;
                this.snackbarMessage = "<?= lang('App.disabledImgUploaded'); ?>";
            } else {
                this.modalAdd = false;
            }

            this.$refs.form.resetValidation();
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
                        this.mediaPath = null;
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
                        this.mediaPath1 = null;
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
                        this.mediaPath2 = null;
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
                        this.mediaPath3 = null;
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
                        this.mediaPath4 = null;
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
            if (this.checkDiscount == false) {
                var discount = 0;
            } else {
                var discount = this.discount;
            }
            axios.post(`<?= base_url() ?>api/product/save`, {
                    category_id: this.idCategory,
                    product_code: this.productCode,
                    product_name: this.productName,
                    product_price: parseInt(this.productPrice),
                    product_description: this.productDescription,
                    product_image: this.mediaID,
                    product_image1: this.media1,
                    product_image2: this.media2,
                    product_image3: this.media3,
                    product_image4: this.media4,
                    discount: discount,
                    link_demo: this.linkDemo,
                    stock: this.stock,
                    stock_min: this.stockMin
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getProducts();
                        this.productCode = "";
                        this.productName = "";
                        this.productPrice = 0;
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
                        this.checkDiscount = false;
                        this.discount = 0;
                        this.linkDemo = null;

                        this.image = null;
                        this.imagePreview = null;
                        this.overlay = false;
                        this.image1 = null;
                        this.imagePreview1 = null;
                        this.overlay1 = false;
                        this.image2 = null;
                        this.imagePreview2 = null;
                        this.overlay2 = false;
                        this.image3 = null;
                        this.imagePreview3 = null;
                        this.overlay3 = false;
                        this.image4 = null;
                        this.imagePreview4 = null;
                        this.overlay4 = false;

                        this.alert = false;
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                    } else {
                        //this.notifType = "error";
                        //this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
                    this.loading = false
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
            this.idCategoryEdit = product.category_id;
            this.productCodeEdit = product.product_code;
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
            this.discountEdit = product.discount;
            if (Number(this.discountEdit) > 0) {
                this.checkDiscount = true;
            }
            this.linkdemoEdit = product.link_demo;
            this.stock = product.stock;
            this.stockMin = product.stock_min;
        },

        // Get Item Edit Product
        editItem: function(product) {
            this.modalEdit = true;
            this.show = false;
            this.notifType = "";
            this.productIdEdit = product.product_id;
            this.productNameEdit = product.product_name;
            this.idCategoryEdit = product.category_id;
            this.productCodeEdit = product.product_code;
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
            this.discountEdit = product.discount;
            if (Number(this.discountEdit) > 0) {
                this.checkDiscount = true;
            }
            this.linkdemoEdit = product.link_demo;
            this.stockEdit = product.stock;
            this.stockMinEdit = product.stock_min;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update Product
        updateProduct: function() {
            this.loading = true;
            if (this.checkDiscount == false) {
                var discount = 0;
            } else {
                var discount = this.discountEdit;
            }
            axios.put(`<?= base_url() ?>api/product/update/${this.productIdEdit}`, {
                    category_id: this.idCategoryEdit,
                    product_code: this.productCodeEdit,
                    product_name: this.productNameEdit,
                    product_price: parseInt(this.productPriceEdit),
                    product_description: this.productDescriptionEdit,
                    product_image: this.mediaID,
                    product_image1: this.media1,
                    product_image2: this.media2,
                    product_image3: this.media3,
                    product_image4: this.media4,
                    discount: discount,
                    link_demo: this.linkdemoEdit,
                    stock: this.stockEdit,
                    stock_min: this.stockMinEdit
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image = null;
                        this.image1 = null;
                        this.image2 = null;
                        this.image3 = null;
                        this.image4 = null;
                        this.mediaID = "";
                        this.media1 = "";
                        this.media2 = "";
                        this.media3 = "";
                        this.media4 = "";
                        this.imagePreview = null;
                        this.imagePreview1 = null;
                        this.imagePreview2 = null;
                        this.imagePreview3 = null;
                        this.imagePreview4 = null;
                        this.checkDiscount = false;
                        this.discount = 0;
                        this.linkdemoEdit = "";
                        this.getProducts();
                        this.modalEdit = false;
                        this.alert = false;
                        this.$refs.form.resetValidation();
                    } else {
                        //this.notifType = "error";
                        //this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = true;
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
                    this.loading = true;
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
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalDelete = true;
                    }
                })
                .catch(err => {
                    // handle error
                    this.loading = true;
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

        // Get Category
        getCategory: function() {
            this.loading1 = true;
            axios.get('<?= base_url(); ?>api/category', options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataCategory = data.data;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataCategory = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading1 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Modal Category
        addCategory: function() {
            this.modalCategory = true;
        },
        modalCategoryClose: function() {
            this.modalCategory = false;
            this.$refs.form.resetValidation();
        },

        // Save Category
        saveCategory: function() {
            this.loading6 = true;
            axios.post(`<?= base_url(); ?>api/category/save`, {
                    category_name: this.nameCategory,
                }, options)
                .then(res => {
                    // handle success
                    this.loading6 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.nameCategory = "";
                        this.getCategory();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
                    console.log(err);
                    this.loading6 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Delete Category
        deleteCategory: function(item) {
            this.loading7 = true;
            axios.delete(`<?= base_url(); ?>api/category/delete/${item.category_id}`, options)
                .then(res => {
                    // handle success
                    this.loading7 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getCategory();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading7 = false;
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