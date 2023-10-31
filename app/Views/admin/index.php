<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<template>
    <?php if (session()->getFlashdata('success')) { ?>
        <v-alert type="success" dismissible v-model="alert">
            <?= session()->getFlashdata('success') ?>
        </v-alert>
    <?php } ?>
    <v-row>
        <v-col lg="4" cols="sm" class="pb-2">
            <v-card link href="<?= base_url('admin/order'); ?>" min-height="130px">
                <div class="pa-5">
                    <h2 class="text-h5 font-weight-medium mb-2"><?= lang('App.order'); ?>
                        <v-icon x-large class="green--text text--lighten-1 float-right">mdi-cart</v-icon>
                    </h2>
                    <h1 class="text-h3"><?= $jmlOrder; ?></h1>
                </div>
            </v-card>
        </v-col>
        <v-col lg="4" cols="sm" class="pb-2">
            <v-card link href="<?= base_url('admin/product'); ?>" min-height="130px">
                <div class="pa-5">
                    <h2 class="text-h5 font-weight-medium mb-2"><?= lang('App.product'); ?>
                        <v-icon x-large class="primary--text text--lighten-1 float-right">mdi-package-variant-closed</v-icon>
                    </h2>
                    <h1 class="text-h3"><?= $jmlProduct; ?></h1>
                </div>
            </v-card>
        </v-col>
        <v-col lg="4" cols="sm" class="pb-2">
            <v-card link href="<?= base_url('admin/user'); ?>" min-height="130px">
                <div class="pa-5">
                    <h2 class="text-h5 font-weight-medium mb-2">User
                        <v-icon x-large class="warning--text text--lighten-1 float-right">mdi-account-multiple</v-icon>
                    </h2>
                    <h1 class="text-h3"><?= $jmlUser; ?></h1>
                </div>
            </v-card>
        </v-col>
    </v-row>
</template>

<br />

<template>
    <v-card>
        <v-card-title><?= lang('App.todayTrx'); ?></v-card-title>
        <v-card-subtitle>{{ tanggal }}</v-card-subtitle>
        <v-card-text>
            <bar-chart1></bar-chart1>
        </v-card-text>
    </v-card>
</template>

<template>
    <v-card class="mx-auto text-center mt-5" elevation="1">
        <v-card-title>
            <?= lang('App.sales') ?> <?= date('Y'); ?>
        </v-card-title>
        <v-sparkline :labels="sparklineLabel" :value="sparklineData" padding="18" label-size="4" color="grey" :gradient="['#007bff','cyan']" :line-width="2" :stroke-linecap="'round'">
    </v-card>
</template>

<br />

<template>
    <v-row>
        <v-col>
            <v-card height="500px">
                <v-card-title>Last Login Member</v-card-title>
                <v-card-text class="overflow-auto" style="height: 400px;">
                    <v-simple-table dense>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>
                                        User
                                    </th>
                                    <th class="text-center" colspan="2">
                                        Waktu
                                    </th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="text-center">
                                        Login
                                    </th>
                                    <th class="text-center">
                                        Logout
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in dataLog" :key="i">
                                    <td>{{i+1}}</td>
                                    <td>{{ item.email }}<br />{{ item.first_name }} {{ item.last_name }}</td>
                                    <td>{{ item.logged_in_at }}</td>
                                    <td>
                                        <div v-if="item.logged_out_at != null">
                                            {{item.logged_out_at}}
                                        </div>
                                        <div v-else>
                                            <v-chip color="green" text-color="white" label><v-icon small left>mdi-information-outline</v-icon> Online</v-chip>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col>
            <v-card height="500px">
                <v-card-title>
                    <?= lang('App.products'); ?> <?= lang('App.bestSeller'); ?>
                </v-card-title>
                <v-card-text class="overflow-auto" style="height: 400px;">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left">
                                        Nama
                                    </th>
                                    <th class="text-left">
                                        Total
                                    </th>
                                    <th class="text-left">
                                        Harga
                                    </th>
                                    <th class="text-left">
                                        Stok
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in products" :key="i">
                                    <td><strong>{{ item.product_name }}</strong><br />Kode: {{ item.product_code }}</td>
                                    <td>{{ item.qty }}</td>
                                    <td>{{ RibuanLocale(item.product_price) }}</td>
                                    <td>{{ item.stock }}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    // function date
    function addZeroBefore(n) {
        return (n < 10 ? '0' : '') + n;
    }

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
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
        alert: false,
        products: [],
        sparklineLabel: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        sparklineData: JSON.parse("<?= json_encode($transaksi) ?>"),
        tanggal: "",
        dataLog: []
    }
    createdVue = function() {
        this.alert = true;
        setTimeout(() => {
            this.alert = false
        }, 5000)

        setInterval(this.getDayDate, 1000);

        this.getLoginLog();
        this.getProducts();

        // Chart.js 1
        Vue.component('bar-chart1', {
            extends: VueChartJs.Bar,
            mounted() {
                this.renderChart({
                    labels: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '00'],
                    datasets: [{
                        data: JSON.parse('<?= json_encode($harian) ?>'),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)'
                        ],
                        borderWidth: 1
                    }]
                }, {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        xAxes: [{
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: true,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            },
                            ticks: {
                                maxTicksLimit: 31
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: true,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + '<?= lang('App.transaction'); ?>: ' + number_format(tooltipItem.yLabel);
                            }
                        }
                    }
                })
            }

        })
    }
    methodsVue = {
        ...methodsVue,
        //Get Tanggal
        getDayDate: function() {
            const weekday = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const today = new Date();
            const date = addZeroBefore(today.getDate()) + ' ' + month[today.getMonth()] + ' ' + today.getFullYear();
            let Hari = weekday[today.getDay()];
            const Tanggal = date;
            this.tanggal = Hari + ', ' + Tanggal;
        },

        // Get Login Log
        getLoginLog: function() {
            this.show = true;
            axios.get(`<?= base_url(); ?>api/loginlog/last10`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.status == true) {
                        this.dataLog = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataLog = data.data;
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

        // Get Charts
        getChart1: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>api/chart1`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        var datas = data.data;
                        this.sparklineLabel = datas.map(x => (x.tahun));
                        this.sparklineData = datas.map(x => (parseInt(x.jumlah)));
                        console.log(this.sparklineLabel)
                        console.log(this.sparklineData)
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

        // Get Product
        getProducts: function() {
            axios.get(`<?= base_url() ?>api/product/sold/best_seller`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.status == true) {
                        this.products = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.products = data.data;
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
    }
</script>
<?php $this->endSection("js") ?>