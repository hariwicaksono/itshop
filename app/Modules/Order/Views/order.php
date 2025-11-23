<?php $this->extend("layouts/app-admin"); ?>
<?php $this->section("content"); ?>
<h1 class="mb-2 font-weight-medium"><?= lang('App.orderList') ?>&nbsp;<span class="font-weight-regular">{{startDate}} {{startDate != '' ? "&mdash;": ""}} {{endDate}}</span>
    <template>
        <v-menu v-model="menu" :close-on-content-click="false" offset-y>
            <template v-slot:activator="{ on, attrs }">
                <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-calendar-filter</v-icon>
                </v-btn>
            </template>
            <v-card width="250">
                <v-card-text>
                    <p class="mb-1"><strong>Filter:</strong></p>
                    <div class="mb-3">
                        <a @click="hariini" title="Hari Ini" alt="Hari Ini">Hari Ini</a> &bull;
                        <a @click="tujuhHari" title="7 Hari Kemarin" alt="7 Hari Kemarin">7 Hari Kemarin</a> &bull;
                        <a @click="bulanIni" title="Bulan Ini" alt="Bulan Ini">Bulan Ini</a> &bull;
                        <a @click="tahunIni" title="Tahun Ini" alt="Tahun Ini">Tahun Ini</a> &bull;
                        <a @click="reset" title="Reset" alt="Reset">Reset</a>
                    </div>
                    <p class="mb-1"><strong>Custom:</strong></p>
                    <p class="mb-1">Dari Tanggal - Sampai Tanggal</p>
                    <v-text-field v-model="startDate" type="date"></v-text-field>
                    <v-text-field v-model="endDate" type="date"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="menu = false">
                        <?= lang('App.close'); ?>
                    </v-btn>
                    <v-btn color="primary" text @click="handleSubmit" :loading="loading">
                        Filter
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-menu>
    </template>
</h1>
<v-row>
    <v-col>
        <!-- Table List Order -->
        <v-card>
            <v-card-title>
                <!-- Button Add New Order -->
                <v-btn large color="primary" dark @click="modalAddOpen" elevation="1"><v-icon>mdi-plus</v-icon> <?= lang('App.add') ?></v-btn>
                <v-spacer></v-spacer>
                <v-text-field v-model="search" v-on:keydown.enter="getOrder" @click:clear="getOrder" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details>
                </v-text-field>
            </v-card-title>
            <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading">
                <template v-slot:item="{ item, isSelected, select}">
                    <tr>
                        <td>{{item.no_order}}</td>
                        <td>{{item.first_name}} {{item.last_name}}<br />{{item.email}}<br />{{item.phone}}</td>
                        <td>{{item.created_at}}</td>
                        <td>{{RibuanLocale(item.total)}}</td>
                        <td>
                            {{item.payment_name}}
                            <a @click="showConfirmation(item)"><?= lang('App.see'); ?></a>
                        </td>
                        <td>
                            <v-select v-model="item.status" name="status" :items="list_status" item-text="label" item-value="value" label="Select Status" single-line @change="setStatus(item)"></v-select>
                        </td>
                        <td>
                            <v-select v-model="item.status_payment" name="status_payment" :items="list_payment" item-text="label" item-value="value" label="Select Status Payment" single-line @change="setStatusPayment(item)"></v-select>
                        </td>
                        <td>
                            <v-btn icon color="primary" class="mr-2" @click="showOrder(item)" title="Detail" alt="Detail">
                                <v-icon>mdi-information-outline</v-icon>
                            </v-btn>
                            <v-btn icon link :href="'<?= base_url('admin/orders/invoice/'); ?>' + item.no_order" target="_blank">
                                <v-icon>mdi-printer</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
                <template slot="body.append">
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right">Jumlah Total</td>
                        <td>{{ Ribuan(sumTotal('total')) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <!-- End Table List -->
    </v-col>
</v-row>

<!-- Modal -->
<!-- Modal Save -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" scrollable persistent width="900px">
            <v-card>
                <v-card-title>
                    <?= lang('App.add') ?> Order (Manual)
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-autocomplete v-model="idUser" :items="dataUser" :item-text="dataUser =>`${dataUser.first_name} ${dataUser.last_name} (${dataUser.phone})`" item-value="user_id" label="Customer" :error-messages="user_idError" :loading="loading2" outlined>
                            <template v-slot:prepend-item>
                                <v-subheader class="mt-n3 mb-n3">{{ dataUser.length }} customer found</v-subheader>
                                <v-list-item ripple @click="modalAddUserOpen">
                                    <v-icon>mdi-account-plus</v-icon> &nbsp;<?= lang('App.add'); ?> Customer
                                </v-list-item>
                            </template>
                        </v-autocomplete>

                        <v-autocomplete v-model="selectedProduct" label="<?= lang('App.product'); ?>" :items="dataProduct" :item-text="dataProduct =>`${dataProduct.product_name} - Rp${dataProduct.product_price}`" item-value="product_id" @change="getProductTotal" multiple chips attach :loading="loading2" outlined></v-autocomplete>

                        <v-row>
                            <v-col cols="12" md="6">
                                <v-select v-model="shipment" :items="dataShipment" item-text="shipment" item-value="shipment_id" label="<?= lang('App.shipment'); ?>" :error-messages="shipmentError" :loading="loading2" outlined></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select v-model="payment" :items="dataPayment" item-text="payment" item-value="payment_id" label="<?= lang('App.payment'); ?>" :error-messages="paymentError" :loading="loading2" outlined></v-select>
                            </v-col>
                        </v-row>

                        <v-row class="mt-n5">
                            <v-col cols="12" md="4">
                                <v-text-field type="number" v-model="totalRp" label="Total (Rp)" :error-messages="totalError" :loading="loading3" outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-select v-model="status" :items="list_status" item-text="label" item-value="value" label="Status Pesanan" :error-messages="statusError" outlined>
                                </v-select>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-select v-model="statusPayment" :items="list_payment" item-text="label" item-value="value" label="Status Pembayaran" :error-messages="status_paymentError" outlined>
                                </v-select>
                            </v-col>
                        </v-row>

                        <v-textarea v-model="note" label="<?= lang('App.note') ?> Order" rows="2" :error-messages="noteError" outlined></v-textarea>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveOrder" :loading="loading4">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Save -->

<!-- Modal Item Order -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalOrder" scrollable persistent width="1000px">
            <v-card>
                <v-card-title>
                    <?= lang('App.order') ?> #{{noOrder}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalOrderClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5 mb-3">
                    <div v-if="loading3 == true">
                        <v-skeleton-loader type="heading,list-item-two-line"></v-skeleton-loader>
                    </div>
                    <div v-else-if="loading3 == false">
                        <h3 class="font-weight-regular mb-3"><strong>{{userOrder.status==0?"Belum Diproses":""}}{{userOrder.status==1?"Sedang Diproses":""}}{{userOrder.status==2?"Dikirim":""}}{{userOrder.status==3?"Dibatalkan":""}}</strong> / {{userOrder.no_order}} / {{userOrder.email}} / {{userOrder.created_at}}</h3>
                        <v-row>
                            <v-col>
                                <strong><?= lang('App.product'); ?></strong><br />
                                <div v-if="show == true">
                                    <v-skeleton-loader type="list-item-avatar-three-line"></v-skeleton-loader>
                                </div>
                                <div v-for="row in itemOrder" :key="row.cart_id" v-if="show == false && userOrder.order_id == row.order_id">
                                    <v-list-item class="ma-n3 pa-n3" two-line>
                                        <v-list-item-avatar size="50" rounded>
                                            <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url() ?>' + row.media_path" v-if="row.media_path != null"></v-img>
                                            <v-img src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                        </v-list-item-avatar>
                                        <v-list-item-content>
                                            <p class="font-weight-medium black--text">{{row.product_name}} - {{row.product_code ?? "-"}}</p>
                                            <p v-if="row.discount > 0">
                                                {{row.qty}} x {{ RibuanLocale(row.price) }}
                                                <span>
                                                    <span class="text-decoration-line-through">{{ RibuanLocale(row.price_normal) }}</span> <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{row.discount_percent}}%</v-chip>
                                                </span>
                                            </p>
                                            <p v-else>{{row.qty}} x {{ RibuanLocale(row.price) }}</p>
                                        </v-list-item-content>
                                    </v-list-item>
                                </div>
                                <br />
                                <em>Note: " {{userOrder.note}} "</em>
                            </v-col>
                            <v-col>
                                <strong><?= lang('App.shipment'); ?></strong><br />
                                {{userOrder.shipment}}<br />
                                <strong><?= lang('App.payment'); ?></strong><br />
                                {{userOrder.payment_name}} / <em>{{userOrder.status_payment}}</em>
                                <p class="text-subtitle-2 mb-0"><strong>Total <?= lang('App.order'); ?></strong><br />
                                    <span class="text-h6"><strong>{{RibuanLocale(userOrder.total)}}</strong></span>
                                </p>
                            </v-col>
                        </v-row>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-form ref="form" v-model="valid">
                        <v-textarea rows="1" label="Link GDrive" v-model="linkGdrive" :error-messages="link_gdriveError" class="mr-3"></v-textarea>
                    </v-form>
                    <v-btn color="primary" @click="updateLinkGdrive" elevation="1" :loading="loading1"><v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?></v-btn>
                    <v-spacer></v-spacer>
                    <v-btn small color="success" outlined @click="modalTrackingOpen" class="py-4" elevation="1">
                        <?= lang('App.trackOrders') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal -->

<template>
    <v-row justify="center">
        <v-dialog v-model="modalConfirm" persistent scrollable width="700px">
            <v-card>
                <v-card-title>
                    <?= lang('App.confirm') ?> Manual
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalConfirmationClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-subtitle>#{{ noOrder }}</v-card-subtitle>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <h2 class="font-weight-medium mb-3">Histori Konfirmasi</h2>
                    <v-simple-table class="mb-3">
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Bank</th>
                                    <th>Nama</th>
                                    <th>Rekening</th>
                                    <th>Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading3 == true">
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                    <td>
                                        <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                    </td>
                                </tr>
                                <tr v-for="item in dataPaymentConfirm" :key="item.confirm_id" v-if="loading3 == false">
                                    <td>{{item.created_at}}</td>
                                    <td>{{item.bank}}</td>
                                    <td>{{item.nama}}</td>
                                    <td>{{item.norekening}}</td>
                                    <td>{{RibuanLocale(item.nominal)}}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Confirm -->

<!-- Modal Tracking -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalTracking" persistent scrollable width="600px">
            <v-card>
                <v-card-title>
                    <?= lang('App.trackOrders'); ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalTracking = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-subtitle>#{{ noOrder }}</v-card-subtitle>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <div v-if="loading == true">
                        <v-row justify="space-between">
                            <v-col cols="3">
                                <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                <v-skeleton-loader type="list-item"></v-skeleton-loader>
                            </v-col>
                            <v-col cols="9">
                                <v-skeleton-loader type="list-item"></v-skeleton-loader>
                                <v-skeleton-loader type="list-item"></v-skeleton-loader>
                            </v-col>
                        </v-row>
                    </div>
                    <div v-else>
                        <v-timeline dense clipped>
                            <v-timeline-item v-for="(item, i) in dataTracking" :key="i" small :color="i == 0 ? 'primary':'grey'">
                                <v-row justify="space-between">
                                    <v-col cols="3">
                                        {{dayjs(item.created_at).format('HH:mm')}}<br />
                                        {{dayjs(item.created_at).format('DD-MM-YYYY')}}
                                    </v-col>
                                    <v-col cols="9">
                                        {{item.tracking_information}}
                                    </v-col>
                                </v-row>
                            </v-timeline-item>
                        </v-timeline>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>

                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Tracking -->

<!-- Modal Add -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAddUser" scrollable persistent max-width="700px">
            <v-card>
                <v-card-title><?= lang('App.add') ?> User
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddUserClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form v-model="valid" ref="form">
                        <v-text-field v-model="email" :rules="[rules.email]" label="E-mail" :error-messages="emailError" outlined></v-text-field>

                        <v-text-field v-model="userName" label="Username" :error-messages="usernameError" outlined required></v-text-field>

                        <v-text-field v-model="firstName" label="First Name *" :error-messages="first_nameError" outlined></v-text-field>

                        <v-text-field v-model="lastName" label="Last Name *" :error-messages="last_nameError" outlined></v-text-field>

                        <v-text-field v-model="company" label="Perusahaan" :error-messages="companyError" outlined></v-text-field>

                        <v-text-field v-model="phone" v-on:keyup="changeNumber" label="Telepon *" :error-messages="phoneError" outlined></v-text-field>

                        <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.min]" :type="show1 ? 'text' : 'password'" label="Password" hint="<?= lang('App.minChar') ?>" counter @click:append="show1 = !show1" :error-messages="passwordError" outlined></v-text-field>

                        <v-text-field block v-model="verify" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[passwordMatch]" :type="show1 ? 'text' : 'password'" label="Confirm Password" counter @click:append="show1 = !show1" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveUser" :loading="loading5">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Add -->

<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    function randomString(L) {
        var s = '';
        var randomchar = function() {
            var n = Math.floor(Math.random() * 62);
            if (n < 10) return n; //1-10
            if (n < 36) return String.fromCharCode(n + 55); //A-Z
            return String.fromCharCode(n + 61); //a-z
        }
        while (s.length < L) s += randomchar();
        return s;
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

    const randNumber = Math.floor(Math.random() * 10000);
    const randPass = randomString(12);

    dataVue = {
        ...dataVue,
        search: "",
        menu: false,
        startDate: "",
        endDate: "",
        dataTable: [{
            text: 'No. Order',
            value: 'no_order'
        }, {
            text: 'User',
            value: 'email'
        }, {
            text: 'Tanggal',
            value: 'created_at'
        }, {
            text: 'Total',
            value: 'total'
        }, {
            text: '<?= lang('App.payment'); ?>',
            value: 'payment_name'
        }, {
            text: '<?= lang('App.status'); ?>',
            value: 'status'
        }, {
            text: '<?= lang('App.status'); ?> Payment',
            value: 'status_payment'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        list_status: [{
            label: 'Pending',
            value: '0'
        }, {
            label: 'Processed',
            value: '1'
        }, {
            label: 'Delivered',
            value: '2'
        }, {
            label: 'Cancel',
            value: '3'
        }, ],
        list_payment: [{
            label: 'Pending',
            value: 'pending'
        }, {
            label: 'Success',
            value: 'success'
        }, {
            label: 'Settlement',
            value: 'settlement'
        }, {
            label: 'Canceled',
            value: 'canceled'
        }, {
            label: 'Denied',
            value: 'denied'
        }, {
            label: 'Expired',
            value: 'expired'
        }, ],
        dataOrder: [],
        totalData: 0,
        data: [],
        options: {},
        userOrder: [],
        itemOrder: [],
        noOrder: "",
        idOrder: "",
        qty: "",
        qtyError: "",
        total: "",
        totalRp: "",
        totalError: "",
        payment: "",
        paymentError: "",
        shipment: "",
        shipmentError: "",
        status: "",
        statusError: "",
        statusPayment: "",
        status_paymentError: "",
        modalAdd: false,
        modalEdit: false,
        modalOrder: false,
        modalConfirm: false,
        modalTracking: false,
        bank: "",
        nama: "",
        norekening: "",
        nominal: "",
        tanggal: "",
        dataPaymentConfirm: [],
        dataTracking: [],
        linkGdrive: "",
        link_gdriveError: "",

        dataProduct: [],
        product: [],
        selectedProduct: [],
        dataUser: [],
        idUser: "",
        user_idError: "",
        dataPayment: [],
        dataShipment: [],
        note: "",
        noteError: "",

        modalAddUser: false,
        userName: 'user' + randNumber,
        usernameError: "",
        email: 'user' + randNumber + "@gmail.com",
        emailError: "",
        firstName: "",
        first_nameError: "",
        lastName: "",
        last_nameError: "",
        company: "",
        companyError: "",
        phone: "",
        phoneError: "",
        show1: false,
        password: randPass,
        passwordError: "",
        verify: randPass,
        verifyError: "",
    }

    createdVue = function() {
        this.getOrder();
    }

    // Vue Computed
    // Computed: Properti-properti terolah (computed) yang kemudian digabung kedalam Vue instance
    computedVue = {
        ...computedVue,
        passwordMatch() {
            return () => this.password === this.verify || "<?= lang('App.samePassword') ?>";
        }
    }

    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataOrder: function() {
            if (this.dataOrder != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
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

                let items = this.dataOrder
                const total = items.length

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

        // Filter Date
        reset: function() {
            this.startDate = "";
            this.endDate = "";
        },
        tujuhHari: function() {
            this.startDate = "<?= $tujuhHari; ?>";
            this.endDate = "<?= $hariini; ?>";
        },
        hariini: function() {
            this.startDate = "<?= $hariini; ?>";
            this.endDate = "<?= $hariini; ?>";
        },
        bulanIni: function() {
            this.startDate = "<?= $awalBulan; ?>";
            this.endDate = "<?= $akhirBulan; ?>";
        },
        tahunIni: function() {
            this.startDate = "<?= $awalTahun; ?>";
            this.endDate = "<?= $akhirTahun; ?>";
        },

        // Handle Submit Filter
        handleSubmit: function() {
            if (this.startDate != '' && this.endDate != '') {
                this.getOrderFiltered();
                this.menu = false;
            } else {
                this.getOrder();
                this.startDate = "";
                this.endDate = "";
                this.menu = false;
            }
        },

        // Get Order
        getOrder: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/order', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataOrder = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataOrder = data.data;
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

        // Get Order Filtered
        getOrderFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/order?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataOrder = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataOrder = data.data;
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

        // Jumlah Total
        sumTotal(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.data.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        modalAddOpen: function() {
            this.modalAdd = true;
            this.shipment = "1";
            this.status = "2";
            this.statusPayment = "settlement";
            this.getUser();
            this.getProduct();
            this.getPayment();
            this.getShipment();
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },

        getUser: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/user?role=2`, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    this.dataUser = data.data;
                    this.loading = false;
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
        getProduct: function() {
            this.loading2 = true;
            axios.get('<?= base_url() ?>api/product', options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataProduct = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataProduct = data.data;
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
        getProductTotal: function() {
            this.loading3 = true;
            axios.post(`<?= base_url() ?>api/product/total`, {
                    data: this.selectedProduct
                }, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.totalRp = data.data;
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

        getPayment: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/payment`, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    this.dataPayment = data.data;
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

        getShipment: function() {
            this.loading2 = true;
            axios.get(`<?= base_url() ?>api/shipment`, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    this.dataShipment = data.data;
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

        // Save Order
        saveOrder: function() {
            this.loading4 = true;
            axios.post(`<?= base_url() ?>api/order/save_manual`, {
                    user_id: this.idUser,
                    total: this.totalRp,
                    username: this.userName,
                    payment: this.payment,
                    shipment: this.shipment,
                    note: this.note,
                    status: this.status,
                    status_payment: this.statusPayment,
                    data: this.selectedProduct
                }, options)
                .then(res => {
                    // handle success
                    this.loading4 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.idUser = "";
                        this.shipment = "1";
                        this.payment = "";
                        this.totalRp = "";
                        this.status = "2";
                        this.statusPayment = "settlement";
                        this.note = "";
                        this.selectedProduct = [];

                        if (this.startDate != '' && this.endDate != '') {
                            this.getOrderFiltered();
                        } else {
                            this.getOrder();
                        }
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
                    this.loading4 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        //Show Order
        showOrder: function(item) {
            this.loading3 = true;
            this.modalOrder = true;
            this.idOrder = item.order_id;
            this.noOrder = item.no_order;
            this.linkGdrive = item.link_gdrive;
            setTimeout(() => this.getUserOrder(), 100);
        },
        modalOrderClose: function() {
            this.modalOrder = false;
            this.userOrder = [];
        },

        //Get Order
        getUserOrder: function() {
            this.loading3 = true;
            axios.get(`<?= base_url() ?>api/order/${this.idOrder}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.userOrder = data.data;
                        this.getItemOrder();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.userOrder = [];
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

        //Get Item Order
        getItemOrder: function() {
            this.show = true;
            axios.get(`<?= base_url() ?>api/cart`, options)
                .then(res => {
                    // handle success
                    this.show = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.itemOrder = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.itemOrder = [];
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response;
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Status
        setStatus: function(item) {
            this.loading = true;
            this.idOrder = item.order_id;
            this.status = item.status;
            axios.put(`<?= base_url() ?>api/order/setstatus/${this.idOrder}`, {
                    status: this.status,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getOrder();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Status Payment
        setStatusPayment: function(item) {
            this.loading = true;
            this.idOrder = item.order_id;
            this.statusPayment = item.status_payment;
            axios.put(`<?= base_url() ?>api/order/setstatus/payment/${this.idOrder}`, {
                    status_payment: this.statusPayment,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getOrder();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err.response);
                    this.loading = false
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        //Show Payment Confirm
        showConfirmation: function(item) {
            this.loading3 = true;
            this.modalConfirm = true;
            this.idOrder = item.order_id;
            this.noOrder = item.no_order;
            setTimeout(() => this.getConfirmation(), 100);
        },
        modalConfirmationClose: function() {
            this.modalConfirm = false;
            this.dataConfirm = [];
        },

        //Get Payment Confirm
        getConfirmation: function() {
            this.loading3 = true;
            axios.get(`<?= base_url() ?>api/payment/get/${this.idOrder}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataPaymentConfirm = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPaymentConfirm = data.data;
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

        modalTrackingOpen: function() {
            this.modalTracking = true;
            this.getTracking();
        },

        //Get Tracking Orders
        getTracking: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/tracking/${this.idOrder}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataTracking = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataTracking = data.data;
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

        // Update Link GDrive
        updateLinkGdrive: function() {
            this.loading1 = true;
            axios.put(`<?= base_url() ?>api/order/update_link_gdrive/${this.idOrder}`, {
                    link_gdrive: this.linkGdrive,
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.linkGdrive = "";
                        this.getUserOrder()
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
                    console.log(err.response);
                    this.loading1 = false
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        changeNumber() {
            // Buang spasi, +, dan -
            let cleaned = this.phone.replace(/[\s+-]/g, '');

            // Kalau masih ada leading 0 di depan 62 → hapus
            // Contoh: 62813 menjadi 62813 (aman)
            // Contoh: 062813 → jadi 62813
            if (cleaned.startsWith('0')) {
                cleaned = '62' + cleaned.substring(1);
            }

            // Minimal harus '62'
            if (cleaned.length < 2) {
                cleaned = '62';
            }

            this.phone = cleaned;
        },

        modalAddUserOpen: function() {
            this.modalAddUser = true;
            this.notifType = "";
            const newrandNumber = Math.floor(Math.random() * 10000);
            const newrandPass = randomString(12);
            this.userName = 'user' + newrandNumber;
            this.email = 'user' + newrandNumber + "@gmail.com";
            this.password = newrandPass;
            this.verify = newrandPass;
        },
        modalAddUserClose: function() {
            this.modalAddUser = false;
            this.$refs.form.resetValidation();
        },

        // Save User
        saveUser: function() {
            this.loading5 = true;
            axios.post(`<?= base_url() ?>api/user/save`, {
                    email: this.email,
                    username: this.userName,
                    password: this.password,
                    first_name: this.firstName,
                    last_name: this.lastName,
                    company: this.company,
                    phone: this.phone
                }, options)
                .then(res => {
                    // handle success
                    this.loading5 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.firstName = "";
                        this.lastName = "";
                        this.company = "";
                        this.phone = "";
                        this.getUser();
                        this.modalAddUser = false;
                        this.$refs.form.resetValidation();
                    } else {
                        this.notifType = "error";
                        this.notifMessage = data.message;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalAddUser = true;
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
                    this.loading5 = false;
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