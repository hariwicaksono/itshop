<?php $this->extend("layouts/app-member"); ?>
<?php $this->section("content"); ?>
<v-row>
    <v-col lg="12" cols="sm" class="pb-2">
        <v-card elevation="2">
            <v-row class="no-gutters">
                <div class="col-auto">
                    <div class="green darken-2 fill-height">&nbsp;</div>
                </div>
                <div class="col pa-3 py-4">
                    <h3 class="text-truncate text-uppercase"><?= lang('App.order'); ?></h3>
                    <h1 class="text-h3"><?= $jmlOrder; ?></h1>
                </div>
            </v-row>
        </v-card>
    </v-col>
</v-row>
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
    }

    createdVue = function() {
       
    }

    methodsVue = {
        ...methodsVue,
        
    }
</script>
<?php $this->endSection("js") ?>