<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<template>
    <?php if (session()->getFlashdata('success')) { ?>
        <v-alert text outlined type="success" dense dismissible>
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
                                    <th rowspan="2">
                                        User
                                    </th>
                                    <th class="text-center" colspan="2">
                                        Waktu
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-center">
                                        Login
                                    </th>
                                    <th class="text-center">
                                        Logout
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col>
            <v-card height="500px">
                <v-card-title>
                    Produk Terlaris
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
                                        Harga
                                    </th>
                                    <th class="text-left">
                                        Stok
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<!--<template>
<h1 class="mt-4 mb-3 font-weight-regular"><?= lang('App.latestProduct') ?></h1>
<v-row v-if="show == true" class="mb-2">
    <v-col v-for="n in 4" :key="n" cols="12" sm="3">
        <v-card elevation="1">
            <v-card-text>
                <v-skeleton-loader class="mx-auto" max-width="300" type="paragraph, heading"></v-skeleton-loader>
            </v-card-text>
        </v-card>
    </v-col>
</v-row>
<v-row v-if="show == false" class="mb-2">
    <v-col v-for="item in products" :key="item.product_id">
        <v-card elevation="1">
            <v-card-title>{{ item.product_name }}</v-card-title>
            <v-card-text>
                Rp.{{ item.product_price }}
            </v-card-text>
        </v-card>
    </v-col>
</v-row>

<paginate :page-count="pageCount" :no-li-surround="true" :container-class="'v-pagination theme--light'" :page-link-class="'v-pagination__item v-btn primary--text'" :active-class="'v-pagination__item--active primary white--text'" :disabled-class="'v-pagination__navigation--disabled'" :prev-link-class="'v-pagination__navigation'" :next-link-class="'v-pagination__navigation'" :click-handler="handlePagination">
</paginate>
</template>-->
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
        products: [],
        sparklineLabel: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        sparklineData: JSON.parse("<?= json_encode($transaksi) ?>"),
        pageCount: 0,
        currentPage: 1,
        tanggal: "",
    }
    createdVue = function() {
        setInterval(this.getDayDate, 1000);

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
                    console.log(err.response);
                })
        },
        // Get Product
        getProducts: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>api/product/all?page=${this.currentPage}`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.expired == true) {
                        this.snackbar = true;
                        this.snackbarType = "warning";
                        this.snackbarMessage = data.message;
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    }
                    if (data.status == true) {
                        this.products = data.data;
                        this.pageCount = Math.ceil(data.total_page / data.per_page);
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarType = "warning";
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        },
        handlePagination: function(pageNumber) {
            this.show = true;
            axios.get(`<?= base_url() ?>api/product/all?page=${pageNumber}`, options)
                .then((res) => {
                    var data = res.data;
                    this.products = data.data;
                    this.pageCount = Math.ceil(data.total_page / data.per_page);
                    this.show = false;
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                })
        }
    }
</script>
<?php $this->endSection("js") ?>