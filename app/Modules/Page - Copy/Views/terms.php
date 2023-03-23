<?php $this->extend("layouts/app-front"); ?>
<?php $this->section("content"); ?>
<template>
    
</template>



<v-dialog v-model="loading2" hide-overlay persistent width="300">
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
        settingData: [],
        dataTable: [{
            text: '#',
            value: 'setting_id'
        }, {
            text: 'Variable',
            value: 'setting_variable'
        }, {
            text: 'Value',
            value: 'setting_value'
        }, {
            text: 'Deskripsi',
            value: 'setting_description'
        }, {
            text: 'Tgl Update',
            value: 'updated_at'
        }, {
            text: 'Aksi',
            value: 'actions',
            sortable: false
        }, ],
        settingId: "",
        groupEdit: "",
        variableEdit: "",
        deskripsiEdit: "",
        valueEdit: "",
        setting_descriptionError: "",
        setting_valueError: "",
        image: null,
        imagePreview: null,
        overlay: false,
        dataYesNo: [{
            text: 'Ya',
            value: 'yes'
        }, {
            text: 'Tidak',
            value: 'no'
        }],
        dataPosition: [{
            text: 'Atas (Top)',
            value: 'top'
        }, {
            text: 'Bawah (Bottom)',
            value: 'bottom'
        }],
        dataKabupatenKota: [],
        kota: "",
        dataProvinsi: [],
        provinsi: "",
    }

    var errorKeys = []

    createdVue = function() {
        axios.defaults.headers['Authorization'] = 'Bearer ' + token;
       
    }

    computedVue = {
        ...computedVue,
      
    }

    watchVue = {
        ...watchVue,

    }

    methodsVue = {
        ...methodsVue,
       

    }
</script>
<?php $this->endSection("js") ?>