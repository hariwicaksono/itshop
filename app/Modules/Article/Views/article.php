<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="mb-3 font-weight-medium"><?= $title; ?></h1>
    <v-card>
        <v-card-title>
            <!-- Button Add New Article -->
            <v-btn large color="primary" dark @click="modalAddOpen" elevation="1"><v-icon>mdi-plus</v-icon> <?= lang('App.add') ?></v-btn>
            &nbsp;
            <!-- Button AI Article Generator -->
            <v-btn large color="deep-purple darken-1" dark @click="modalAIOpen" elevation="1">
                <v-icon class="mr-1">mdi-robot-outline</v-icon> Generate AI Article
            </v-btn>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" v-on:keydown.enter="getArticles" @click:clear="getArticles" append-icon="mdi-magnify" label="<?= lang('App.search'); ?>" single-line hide-details clearable>
            </v-text-field>
        </v-card-title>
        <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading">
            <template v-slot:item="{ item }">
                <tr>
                    <td>
                        <v-avatar size="100" rounded>
                            <v-img lazy-src="<?= base_url('images/no-image.png') ?>" :src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                            <v-img lazy-src="<?= base_url('images/no-image.png') ?>" src="<?= base_url('images/no-image.png') ?>" v-else></v-img>
                        </v-avatar>
                    </td>
                    <td>{{item.article_title}}<br />
                        <em>{{item.article_title_en}}</em>
                    </td>
                    <td>{{item.category_name}}</td>
                    <td>{{item.updated_at}}</td>
                    <td>{{RibuanNoRp(item.views)}}</td>
                    <td><v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setActive(item)" title="" alt=""></v-switch></td>
                    <td>
                        <v-btn color="primary" @click="editItem(item)" icon class="mr-2" title="Edit" alt="Edit">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn color="error" @click="deleteItem(item)" icon class="mr-2" title="Delete" alt="Delete">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <v-btn color="info" link :href="'<?= base_url('read/'); ?>' + item.category_slug + '/' + item.slug" target="_blank" icon title="Show" alt="Show">
                            <v-icon>mdi-arrow-right</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-card>
</template>

<!-- Modal -->
<!-- Modal Save -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" fullscreen persistent scrollable>
            <v-card>
                <v-card-title>
                    <v-btn icon @click="modalAddClose" class="mr-3">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <?= lang('App.add') ?> Article
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveArticle" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col class="mb-n5" cols="12" md="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.category') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" md="9">
                                <v-select v-model="idCategory" label="<?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" :error-messages="category_idError" :loading="loading3" outlined append-outer-icon="mdi-plus-thick" @click:append-outer="addCategory"></v-select>
                            </v-col>
                        </v-row>

                        <v-row class="mt-n5">
                            <v-col class="mb-n10" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold">Image</p>
                                <p class="text-caption">Format gambar .jpg .jpeg .png </p>
                            </v-col>
                            <v-col cols="12" sm="9">
                                <v-file-input v-model="image" single-line label="Cover" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" :loading="loading1" class="mb-0" :error-messages="article_imageError" v-if="imagePreview == null"></v-file-input>

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
                        </v-row>

                        <v-tabs>
                            <v-tab>
                                Indonesian
                            </v-tab>
                            <v-tab>
                                English
                            </v-tab>

                            <v-tab-item class="mt-4">
                                <v-text-field v-model="articleTitle" label="Article Title" :error-messages="article_titleError" outlined></v-text-field>

                                <v-textarea v-model="articleHeadline" label="Article Headline" :error-messages="article_headlineError" rows="2" counter :rules="[rules.length(255)]" outlined></v-textarea>

                                <quill-editor v-model="articleBody" ref="quillEditor" :options="editorOption" :error-messages="article_bodyError"></quill-editor>
                            </v-tab-item>
                            <v-tab-item class="mt-4">
                                <v-text-field v-model="articleTitleEn" label="Article Title (En)" :error-messages="article_title_enError" outlined></v-text-field>

                                <v-textarea v-model="articleHeadlineEn" label="Article Headline (En)" :error-messages="article_headline_enError" rows="2" counter :rules="[rules.length(255)]" outlined></v-textarea>

                                <quill-editor v-model="articleBodyEn" ref="quillEditor" :options="editorOption" :error-messages="article_body_enError"></quill-editor>

                            </v-tab-item>
                        </v-tabs>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Save -->

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" fullscreen persistent scrollable>
            <v-card>
                <v-card-title>
                    <v-btn icon @click="modalEditClose" class="mr-3">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <?= lang('App.edit') ?> Article
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateArticle" :loading="loading2">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col class="mb-n5" cols="12" md="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.category') ?>*</p>
                                <p class="text-caption"></p>
                            </v-col>
                            <v-col cols="12" md="9">
                                <v-select v-model="idCategory" label="<?= lang('App.category'); ?>" :items="dataCategory" item-text="category_name" item-value="category_id" :error-messages="category_idError" :loading="loading3" outlined append-outer-icon="mdi-plus-thick" @click:append-outer="addCategory"></v-select>
                            </v-col>
                        </v-row>

                        <v-row class="mt-n5">
                            <v-col class="mb-n10" cols="12" sm="3">
                                <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.productImg') ?></p>
                                <p class="text-caption">Format gambar .jpg .jpeg .png </p>
                            </v-col>
                            <v-col cols="12" sm="9">
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
                                    <v-file-input v-model="image" single-line label="Cover" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" :loading="loading1" class="mb-0" :error-messages="article_imageError" v-if="imagePreview == null"></v-file-input>

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
                        </v-row>

                        <v-text-field v-model="slug" label="Slug" :error-messages="slugError" filled disabled></v-text-field>

                        <v-tabs>
                            <v-tab>
                                Indonesian
                            </v-tab>
                            <v-tab>
                                English
                            </v-tab>

                            <v-tab-item class="mt-4">
                                <v-text-field v-model="articleTitle" label="Article Title" :error-messages="article_titleError" outlined></v-text-field>

                                <v-textarea v-model="articleHeadline" label="Article Headline" :error-messages="article_headlineError" rows="2" counter :rules="[rules.length(255)]" outlined></v-textarea>

                                <quill-editor v-model="articleBody" ref="quillEditor" :options="editorOption" :error-messages="article_bodyError"></quill-editor>
                            </v-tab-item>
                            <v-tab-item class="mt-4">
                                <v-text-field v-model="articleTitleEn" label="Article Title (En)" :error-messages="article_title_enError" outlined></v-text-field>

                                <v-textarea v-model="articleHeadlineEn" label="Article Headline (En)" :error-messages="article_headline_enError" rows="2" counter :rules="[rules.length(255)]" outlined></v-textarea>

                                <quill-editor v-model="articleBodyEn" ref="quillEditor" :options="editorOption" :error-messages="article_body_enError"></quill-editor>
                            </v-tab-item>
                        </v-tabs>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<!-- Modal Delete -->
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
                    <v-btn large text @click="modalDelete = false"><?= lang("App.no") ?></v-btn>
                    <v-btn large color="error" dark @click="deleteArticle" :loading="loading"><?= lang("App.yes") ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

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

<!-- Modal AI Article Generator -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAI" fullscreen persistent scrollable>
            <v-card style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
                <v-card-title class="white--text py-4" style="background: rgba(103,58,183,0.85); backdrop-filter: blur(10px);">
                    <v-btn icon @click="modalAIClose" class="mr-3" dark>
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-icon class="mr-2" color="amber">mdi-robot-outline</v-icon>
                    <span class="headline font-weight-bold">AI Article Generator</span>
                    <v-chip class="ml-3" color="amber" small label>Powered by Gemini</v-chip>
                    <v-spacer></v-spacer>
                    <v-btn large color="amber darken-2" dark @click="useAIResult" :disabled="!aiResult.title_id" elevation="2">
                        <v-icon class="mr-1">mdi-check-circle</v-icon> Gunakan Artikel Ini
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-row>
                        <!-- Panel Kiri: Form Setting -->
                        <v-col cols="12" md="4">
                            <v-card dark color="rgba(255,255,255,0.07)" class="pa-4" style="border: 1px solid rgba(255,255,255,0.15); border-radius:16px;">
                                <div class="mb-4">
                                    <div class="d-flex align-center mb-3">
                                        <v-icon color="deep-purple lighten-2" class="mr-2">mdi-lightbulb-on</v-icon>
                                        <span class="subtitle-1 font-weight-bold white--text">Pengaturan Artikel</span>
                                    </div>

                                    <p class="caption grey--text text--lighten-1 mb-4">Isi pengaturan di bawah ini, lalu klik <strong>Generate</strong> untuk membuat artikel dengan AI.</p>

                                    <v-textarea
                                        v-model="aiTopic"
                                        label="Topik / Judul Artikel *"
                                        rows="3"
                                        outlined
                                        dark
                                        color="deep-purple lighten-2"
                                        placeholder="Contoh: Cara mempercepat laptop Windows yang lemot, Tutorial instalasi XAMPP di Windows 11..."
                                        :error-messages="aiTopicError"
                                        counter="300"></v-textarea>

                                    <v-select
                                        v-model="aiType"
                                        :items="aiTypeOptions"
                                        item-text="label"
                                        item-value="value"
                                        label="Jenis Artikel"
                                        outlined
                                        dark
                                        color="deep-purple lighten-2"></v-select>

                                    <v-select
                                        v-model="aiTone"
                                        :items="aiToneOptions"
                                        item-text="label"
                                        item-value="value"
                                        label="Gaya Penulisan"
                                        outlined
                                        dark
                                        color="deep-purple lighten-2"></v-select>

                                    <v-select
                                        v-model="aiCategory"
                                        label="Kategori Artikel"
                                        :items="dataCategory"
                                        item-text="category_name"
                                        item-value="category_id"
                                        outlined
                                        dark
                                        color="deep-purple lighten-2"
                                        clearable></v-select>

                                    <!-- Bahasa Generate (Bilingual by default) -->
                                    <p class="caption grey--text text--lighten-1 mb-2">Bahasa: <strong>Bilingual (ID & EN)</strong></p>

                                    <div class="mt-5">
                                        <v-btn
                                            block
                                            x-large
                                            color="deep-purple"
                                            dark
                                            @click="generateAIArticle"
                                            :loading="loadingAI"
                                            elevation="3"
                                            style="border-radius:12px;">
                                            <v-icon class="mr-2">mdi-creation</v-icon>
                                            Generate Artikel
                                        </v-btn>
                                    </div>

                                    <!-- Topik Populer -->
                                    <div class="mt-5">
                                        <p class="caption grey--text text--lighten-1 mb-2"><v-icon x-small color="amber">mdi-fire</v-icon> Topik Populer:</p>
                                        <v-chip-group column>
                                            <v-chip v-for="t in aiPopularTopics" :key="t" small outlined color="deep-purple lighten-3" @click="aiTopic = t" class="mb-1">{{t}}</v-chip>
                                        </v-chip-group>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>

                        <!-- Panel Kanan: Preview Hasil -->
                        <v-col cols="12" md="8">
                            <!-- State: Belum generate -->
                            <v-card v-if="!aiResult.title_id && !loadingAI" dark color="rgba(255,255,255,0.05)" class="pa-6 text-center" style="border: 1px dashed rgba(255,255,255,0.2); border-radius:16px; min-height:400px; display:flex; align-items:center; justify-content:center;">
                                <div>
                                    <v-icon size="80" color="deep-purple lighten-3" class="mb-4">mdi-robot-excited-outline</v-icon>
                                    <h3 class="white--text mb-2">Siap Generate Artikel!</h3>
                                    <p class="grey--text">Isi topik dan pengaturan di sebelah kiri, lalu klik <strong>Generate Artikel</strong> untuk membuat artikel berkualitas tinggi dengan AI.</p>
                                    <div class="mt-3">
                                        <v-chip small color="deep-purple darken-1" dark class="ma-1"><v-icon x-small left>mdi-check</v-icon>Humanisasi Tinggi</v-chip>
                                        <v-chip small color="deep-purple darken-1" dark class="ma-1"><v-icon x-small left>mdi-check</v-icon>SEO Friendly</v-chip>
                                        <v-chip small color="deep-purple darken-1" dark class="ma-1"><v-icon x-small left>mdi-check</v-icon>Bilingual Support</v-chip>
                                    </div>
                                </div>
                            </v-card>

                            <!-- State: Loading -->
                            <v-card v-if="loadingAI" dark color="rgba(255,255,255,0.05)" class="pa-6 text-center" style="border: 1px solid rgba(103,58,183,0.5); border-radius:16px; min-height:400px; display:flex; align-items:center; justify-content:center;">
                                <div>
                                    <v-progress-circular indeterminate color="deep-purple lighten-2" size="80" width="4" class="mb-4"></v-progress-circular>
                                    <h3 class="white--text mb-2">AI sedang menulis artikel...</h3>
                                    <p class="grey--text">Mohon tunggu, Gemini AI sedang membuat artikel yang menarik dan terhumanisasi untuk Anda.</p>
                                    <v-progress-linear indeterminate color="deep-purple lighten-2" class="mt-4" rounded></v-progress-linear>
                                </div>
                            </v-card>

                            <!-- State: Hasil AI -->
                            <v-card v-if="aiResult.title_id && !loadingAI" dark color="rgba(255,255,255,0.07)" style="border: 1px solid rgba(103,58,183,0.6); border-radius:16px;">
                                <v-card-title class="py-3 px-5" style="background: rgba(103,58,183,0.3); border-radius:16px 16px 0 0;">
                                    <v-icon color="amber" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="white--text subtitle-1">Artikel Berhasil Dibuat!</span>
                                    <v-spacer></v-spacer>
                                    <v-btn small text color="grey" @click="clearAIResult"><v-icon small>mdi-refresh</v-icon> Reset</v-btn>
                                </v-card-title>
                                <v-tabs background-color="transparent" color="amber" class="px-5" dark>
                                    <v-tab>🇮🇩 Indonesian</v-tab>
                                    <v-tab>🇬🇧 English</v-tab>

                                    <v-tab-item class="mt-4" style="background-color: transparent !important;">
                                        <v-card-text class="pa-0 pt-2">
                                            <!-- Preview Title ID -->
                                            <div class="mb-4" style="background: rgba(255,255,255,0.05); border-left: 3px solid #7c4dff; padding: 12px 16px; border-radius:0 8px 8px 0;">
                                                <p class="caption amber--text mb-1">📌 JUDUL ARTIKEL (ID)</p>
                                                <h3 class="grey--text text--darken-3">{{aiResult.title_id}}</h3>
                                            </div>

                                            <!-- Headline ID -->
                                            <div class="mb-4" style="background: rgba(255,255,255,0.05); border-left: 3px solid #26c6da; padding: 12px 16px; border-radius:0 8px 8px 0;">
                                                <p class="caption cyan--text mb-1">📝 HEADLINE (ID)</p>
                                                <p class="grey--text text--darken-3 mb-0">{{aiResult.headline_id}}</p>
                                            </div>

                                            <!-- Preview Body ID -->
                                            <div class="mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 16px; border-radius:8px; max-height: 350px; overflow-y: auto;">
                                                <p class="caption green--text mb-2">📄 ISI ARTIKEL (ID Preview)</p>
                                                <div class="grey--text text--darken-3 ai-article-preview" v-html="aiResult.body_id" style="font-size:0.92rem; line-height:1.7;"></div>
                                            </div>
                                        </v-card-text>
                                    </v-tab-item>

                                    <v-tab-item class="mt-4">
                                        <v-card-text class="pa-0 pt-2">
                                            <!-- Preview Title EN -->
                                            <div class="mb-4" style="border-left: 3px solid #7c4dff; padding: 12px 16px; border-radius:0 8px 8px 0;">
                                                <p class="caption amber--text mb-1">📌 ARTICLE TITLE (EN)</p>
                                                <h3 class="grey--text text--darken-3">{{aiResult.title_en}}</h3>
                                            </div>

                                            <!-- Headline EN -->
                                            <div class="mb-4" style="border-left: 3px solid #26c6da; padding: 12px 16px; border-radius:0 8px 8px 0;">
                                                <p class="caption cyan--text mb-1">📝 HEADLINE (EN)</p>
                                                <p class="grey--text text--darken-3 mb-0">{{aiResult.headline_en}}</p>
                                            </div>

                                            <!-- Preview Body EN -->
                                            <div class="mb-4" style="border: 1px solid rgba(255,255,255,0.1); padding: 16px; border-radius:8px; max-height: 350px; overflow-y: auto;">
                                                <p class="caption green--text mb-2">📄 ARTICLE BODY (EN Preview)</p>
                                                <div class="grey--text text--darken-3 ai-article-preview" v-html="aiResult.body_en" style="font-size:0.92rem; line-height:1.7;"></div>
                                            </div>
                                        </v-card-text>
                                    </v-tab-item>
                                </v-tabs>
                                <v-card-actions class="px-5 pb-4">
                                    <v-btn color="deep-purple" dark @click="generateAIArticle" :loading="loadingAI">
                                        <v-icon class="mr-1">mdi-refresh</v-icon> Generate Ulang
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                    <v-btn x-large color="amber darken-2" dark @click="useAIResult" elevation="2" style="border-radius:10px;">
                                        <v-icon class="mr-2">mdi-arrow-right-circle</v-icon> Gunakan Artikel Ini
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal AI Article Generator -->

<v-dialog v-model="loading" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
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
            //"Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    dataVue = {
        ...dataVue,
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        dataArticles: [],
        totalData: 0,
        data: [],
        options: {},
        dataTable: [{
            text: 'Image',
            value: 'article_image'
        }, {
            text: 'Title',
            value: 'article_title'
        }, {
            text: '<?= lang('App.category') ?>',
            value: 'category_name'
        }, {
            text: 'Date',
            value: 'updated_at'
        }, {
            text: 'View',
            value: 'views'
        }, {
            text: 'Active',
            value: 'active'
        }, {
            text: 'Aksi',
            value: 'actions',
            sortable: false
        }, ],
        articleId: "",
        articleTitle: "",
        article_titleError: "",
        articleTitleEn: "",
        article_title_enError: "",
        articleHeadline: "",
        article_headlineError: "",
        articleHeadlineEn: "",
        article_headline_enError: "",
        articleBody: "",
        article_bodyError: "",
        articleBodyEn: "",
        article_body_enError: "",
        active: "",
        slug: "",
        slugError: "",
        views: 0,
        userId: "<?= session()->get('id'); ?>",

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

        articleImage: null,
        article_imageError: "",
        mediaID: "",
        image: null,
        mediaPath: null,
        imagePreview: null,
        overlay: false,

        // AI Article Generator
        modalAI: false,
        loadingAI: false,
        aiTopic: "",
        aiTopicError: "",
        aiType: "tutorial",
        aiTone: "friendly",
        aiCategory: "",
        aiResult: {
            title_id: "",
            headline_id: "",
            body_id: "",
            title_en: "",
            headline_en: "",
            body_en: ""
        },
        aiTypeOptions: [{
                label: "Tutorial (Langkah demi langkah)",
                value: "tutorial"
            },
            {
                label: "Tips & Trik Praktis",
                value: "tips"
            },
            {
                label: "Review / Ulasan",
                value: "review"
            },
            {
                label: "Berita & Info Terkini",
                value: "news"
            },
            {
                label: "Penjelasan Konsep",
                value: "explanation"
            },
        ],
        aiToneOptions: [{
                label: "Friendly & Ramah (Default)",
                value: "friendly"
            },
            {
                label: "Profesional & Formal",
                value: "professional"
            },
            {
                label: "Santai & Casual",
                value: "casual"
            },
            {
                label: "Edukatif & Informatif",
                value: "educational"
            },
        ],
        aiPopularTopics: [
            "Cara mempercepat laptop Windows yang lemot",
            "Tutorial instalasi XAMPP di Windows 11",
            "Panduan memilih VPS untuk website bisnis",
            "Cara install WordPress di cPanel",
            "Perbedaan SSD dan HDD untuk laptop",
            "Apa itu AI dan bagaimana cara kerjanya",
            "Tutorial Git untuk pemula",
            "Cara setting DNS domain di Cloudflare",
            "Linux vs Windows untuk server",
            "Tips keamanan jaringan komputer kantor",
        ],
    }

    var errorKeys = []

    createdVue = function() {
        axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        this.getArticles();
        this.getCategory();
    }

    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataArticles: function() {
            if (this.dataArticles != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        }
    }

    computedVue = {
        ...computedVue,

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

                let items = this.dataArticles
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

        // Get Articles
        getArticles: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/articles')
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataArticles = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataArticles = data.data;
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
            this.articleTitle = "";
            this.articleTitleEn = "";
            this.articleHeadline = "";
            this.articleHeadlineEn = "";
            this.articleBody = "";
            this.articleBodyEn = "";
        },
        modalAddClose: function() {
            this.modalAdd = false;
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
            formData.append('articleImage', blob);
            axios.post(`<?= base_url() ?>api/media/save_article`, formData, options)
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

        // Save Article
        saveArticle: function() {
            this.loading = true;
            axios.post(`<?= base_url() ?>api/article/save`, {
                    category_id: this.idCategory,
                    article_image: this.mediaID,
                    article_title: this.articleTitle,
                    article_headline: this.articleHeadline,
                    article_headline_en: this.articleHeadlineEn,
                    article_title_en: this.articleTitleEn,
                    article_body: this.articleBody,
                    article_body_en: this.articleBodyEn,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.articleTitle = "";
                        this.articleTitleEn = "";
                        this.articleHeadline = "";
                        this.articleHeadlineEn = "";
                        this.articleBody = "";
                        this.articleBodyEn = "";
                        this.getArticles();
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                    } else {
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
        editItem: function(item) {
            this.modalEdit = true;
            this.notifType = "";
            this.articleId = item.article_id;
            this.idCategory = item.category_id;
            this.articleTitle = item.article_title;
            this.articleTitleEn = item.article_title_en;
            this.articleHeadline = item.article_headline;
            this.articleHeadlineEn = item.article_headline_en;
            this.articleBody = item.article_body;
            this.articleBodyEn = item.article_body_en;
            this.active = item.active;
            this.slug = item.slug;
            this.mediaID = item.article_image;
            this.mediaPath = item.media_path;
        },

        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updateArticle: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/article/update/${this.articleId}`, {
                    category_id: this.idCategory,
                    article_image: this.mediaID,
                    article_title: this.articleTitle,
                    article_headline: this.articleHeadline,
                    article_headline_en: this.articleHeadlineEn,
                    article_title_en: this.articleTitleEn,
                    article_body: this.articleBody,
                    article_body_en: this.articleBodyEn
                })
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = false;
                        this.getArticles();
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

        // Set Item Active article
        setActive: function(item) {
            this.loading = true;
            this.articleId = item.article_id;
            this.active = item.active;
            axios.put(`<?= base_url() ?>api/article/setactive/${this.articleId}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getArticles();
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

        // Get Item Delete
        deleteItem: function(item) {
            this.modalDelete = true;
            this.articleId = item.article_id;
        },

        // Delete
        deleteArticle: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/article/delete/${this.articleId}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getArticles();
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

        // Get Category
        getCategory: function() {
            this.loading3 = true;
            axios.get('<?= base_url(); ?>api/category', options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
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
                    this.loading3 = false;
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

        // AI Article Generator
        modalAIOpen: function() {
            this.modalAI = true;
            this.aiTopicError = "";
        },
        modalAIClose: function() {
            this.modalAI = false;
        },
        clearAIResult: function() {
            this.aiResult = {
                title_id: "",
                headline_id: "",
                body_id: "",
                title_en: "",
                headline_en: "",
                body_en: ""
            };
            this.aiTopic = "";
        },
        generateAIArticle: function() {
            if (!this.aiTopic.trim()) {
                this.aiTopicError = "Topik artikel wajib diisi!";
                return;
            }
            this.aiTopicError = "";
            this.loadingAI = true;
            this.aiResult = {
                title_id: "",
                headline_id: "",
                body_id: "",
                title_en: "",
                headline_en: "",
                body_en: ""
            };

            axios.post(`<?= base_url() ?>api/article/generate-ai`, {
                    topic: this.aiTopic,
                    type: this.aiType,
                    tone: this.aiTone,
                }, options)
                .then(res => {
                    this.loadingAI = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.aiResult = data.data;
                        this.snackbar = true;
                        this.snackbarMessage = '✅ Artikel AI berhasil dibuat!';
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = '❌ ' + data.message;
                    }
                })
                .catch(err => {
                    this.loadingAI = false;
                    console.log(err);
                    this.snackbar = true;
                    this.snackbarMessage = '❌ Gagal menghubungi AI. Coba lagi.';
                });
        },
        useAIResult: function() {
            if (!this.aiResult.title_id) return;

            this.articleTitle = this.aiResult.title_id;
            this.articleHeadline = this.aiResult.headline_id;
            this.articleBody = this.aiResult.body_id;

            this.articleTitleEn = this.aiResult.title_en;
            this.articleHeadlineEn = this.aiResult.headline_en;
            this.articleBodyEn = this.aiResult.body_en;

            // Isi kategori jika dipilih
            if (this.aiCategory) {
                this.idCategory = this.aiCategory;
            }

            // Tutup modal AI, buka modal Add
            this.modalAI = false;
            this.modalAdd = true;

            this.snackbar = true;
            this.snackbarMessage = '✅ Artikel AI berhasil dipindahkan ke form!';
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