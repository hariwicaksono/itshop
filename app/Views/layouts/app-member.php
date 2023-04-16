<?php
/*
PT. GLOBAL ITSHOP PURWOKERTO
Toko Online: ITShop Purwokerto (Tokopedia, Shopee, Bukalapak, Blibli)
Dibuat oleh: Hari Wicaksono, S.Kom
03-2023
*/

// Memanggil library Setting
use App\Libraries\Settings;

$setting = new Settings();
$snackbarsPosition = $setting->info['snackbars_position'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <title>Member | <?= env('appName'); ?></title>
    <meta name="theme-color" content="#FFFFFF" />
    <link rel="apple-touch-icon" href="<?= base_url('images/logo.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo.png') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="<?= base_url('assets/css/materialdesignicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/vuetify.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet">
</head>

<body>
    <!-- ========================= preloader start ========================= -->
    <div class="preloader">
        <div class="loader">
            <div class="loader-logo"><img src="<?= base_url('images/logo.png') ?>" alt="Preloader" width="65" style="margin-top: 5px;"></div>
            <div class="spinner">
                <div class="spinner-container">
                    <div class="spinner-rotator">
                        <div class="spinner-left">
                            <div class="spinner-circle"></div>
                        </div>
                        <div class="spinner-right">
                            <div class="spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- preloader end -->
    <div id="app">
        <v-app>
            <v-app-bar app color="primary" dark>
                <v-app-bar-nav-icon @click.stop="sidebarMenu = !sidebarMenu"></v-app-bar-nav-icon>
                <v-toolbar-title></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon class="mr-3" href="<?= base_url('cart') ?>" elevation="0">
                    <v-badge :content="cartCounter" :value="cartCounter" color="red" overlap>
                        <v-icon>mdi-cart</v-icon>
                    </v-badge>
                </v-btn>
                <v-btn icon class="mr-3" href="<?= base_url('member/order-list') ?>" elevation="0">
                    <v-badge :content="orderCounter" :value="orderCounter" color="error" overlap>
                        <v-icon>
                            mdi-bell
                        </v-icon>
                    </v-badge>
                </v-btn>
                <?php if (!empty(session()->get('username'))) : ?>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn text class="mr-3" v-bind="attrs" v-on="on">
                                <v-icon>mdi-account-circle</v-icon>&nbsp;<span class="d-none d-sm-flex d-md-flex d-lg-flex d-xl-flex"><?= session()->get('email') ?></span> <v-icon>mdi-chevron-down</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item class="d-flex justify-center">
                                <v-list-item-avatar size="100">
                                    <v-img src="<?= base_url('assets/images/default.png'); ?>"></v-img>
                                </v-list-item-avatar>
                            </v-list-item>
                            <v-list-item link>
                                <v-list-item-content>
                                    <v-list-item-title class="text-h6">
                                        Hallo, <?= session()->get('first_name') . ' ' . session()->get('last_name') ?>
                                    </v-list-item-title>
                                    <v-list-item-subtitle><?= session()->get('email') ?></v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-subheader>Login: &nbsp;<v-chip color="primary" small><?= session()->get('role') == 1 ? 'admin' : 'user'; ?></v-chip>
                            </v-subheader>
                            <v-list-item link href="<?= base_url(); ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-home</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.backtoHome') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item link href="<?= base_url('logout'); ?>" @click="localStorage.removeItem('access_token')">
                                <v-list-item-icon>
                                    <v-icon>mdi-logout</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Logout</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                <?php endif; ?>
                <v-divider class="mx-1" vertical></v-divider>
                <v-btn icon @click.stop="rightMenu = !rightMenu">
                    <v-icon>mdi-cog-outline</v-icon>
                </v-btn>
            </v-app-bar>

            <v-navigation-drawer light class="elevation-3" v-model="sidebarMenu" app floating :permanent="sidebarMenu" :mini-variant.sync="mini" v-if="!isMobile">
                <v-list color="primary" dark dense elevation="1">
                    <v-list-item>
                        <v-list-item-action>
                            <v-icon @click.stop="toggleMini = !toggleMini">mdi-chevron-left</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>
                                <?= APP_WEB; ?>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>


                <v-list nav>
                    <?php $uri = new \CodeIgniter\HTTP\URI(current_url()); ?>

                    <?php if (session()->get('role') == 2) : ?>
                        <v-list-item link href="<?= base_url('member'); ?>" <?php if ($uri->getSegment(2) == "") { ?><?= 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-view-dashboard</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title><?= lang('App.dashboard') ?></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item link href="<?= base_url('member/order-list'); ?>" <?php if ($uri->getSegment(2) == "order-list") { ?> <?= 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-receipt-text</v-icon><v-badge color="error" dot overlap v-show="orderCounter > 0"></v-badge>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title><?= lang('App.orderList') ?></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item link href="<?= base_url('member/profile'); ?>" <?php if ($uri->getSegment(2) == "profile") { ?> <?= 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-account</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content class="text-truncate">
                                <?= session()->get('email') ?>
                            </v-list-item-content>
                        </v-list-item>
                    <?php endif; ?>

                </v-list>

                <template v-slot:append>
                    <v-divider></v-divider>
                    <div class="pa-3 text-center text-caption">
                        <span>&copy; {{ new Date().getFullYear() }} <?= COMPANY_NAME; ?></span>
                    </div>
                </template>

            </v-navigation-drawer>

            <v-navigation-drawer v-model="rightMenu" app right bottom temporary>
                <template v-slot:prepend>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>Settings</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>

                <v-divider></v-divider>

                <v-list-item>
                    <v-list-item-avatar>
                        <v-icon>mdi-theme-light-dark</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        Tema {{themeText}}
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-switch v-model="dark" inset @click="toggleTheme"></v-switch>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item>
                    <v-list-item-avatar>
                        <v-icon>mdi-earth</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        Lang
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-btn-toggle>
                            <v-btn text small link href="<?= base_url('lang/id'); ?>">
                                ID
                            </v-btn>
                            <v-btn text small link href="<?= base_url('lang/en'); ?>">
                                EN
                            </v-btn>
                        </v-btn-toggle>
                    </v-list-item-action>
                </v-list-item>
            </v-navigation-drawer>

            <v-main>
                <v-container class="px-5 py-1" fluid>
                    <div class="py-4">
                        <?= $this->renderSection('content') ?>
                    </div>
                </v-container>
            </v-main>

            <v-snackbar v-model="snackbar" :timeout="timeout" style="bottom:20px;">
                <span v-if="snackbar">{{snackbarMessage}}</span>
                <template v-slot:action="{ attrs }">
                    <v-btn text v-bind="attrs" @click="snackbar = false">
                        ok
                    </v-btn>
                </template>
            </v-snackbar>
        </v-app>
    </div>

    <script src="<?= base_url('assets/js/vue.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify-image-input.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/axios.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuejs-paginate.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/main.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/dayjs.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs-locale-id.js') ?>"></script>

    <script>
        dayjs.locale('id');
        dayjs().locale('id').format();
    </script>

    <script>
        var vue = null;
        var computedVue = {
            mini: {
                get() {
                    return this.$vuetify.breakpoint.xsOnly || this.toggleMini;
                },
                set(value) {
                    this.toggleMini = value;
                }
            },
            isMobile() {
                if (this.$vuetify.breakpoint.smAndDown) {
                    return this.sidebarMenu = false
                }
            },
            themeText() {
                return this.$vuetify.theme.dark ? '<?= lang('App.dark') ?>' : '<?= lang('App.light') ?>'
            }
        }
        var createdVue = function() {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }
        var mountedVue = function() {
            this.getCartCount();
            this.getOrderCount();
            const theme = localStorage.getItem("dark_theme");
            if (theme) {
                if (theme === "true") {
                    this.$vuetify.theme.dark = true;
                    this.dark = true;
                } else {
                    this.$vuetify.theme.dark = false;
                    this.dark = false;
                }
            } else if (
                window.matchMedia &&
                window.matchMedia("(prefers-color-scheme: dark)").matches
            ) {
                this.$vuetify.theme.dark = false;
                localStorage.setItem(
                    "dark_theme",
                    this.$vuetify.theme.dark.toString()
                );
            }

            const mini = localStorage.getItem("toggle_mini");
            if (mini) {
                if (mini === "true") {
                    this.toggleMini = true;
                } else {
                    this.toggleMini = false;
                }
            }
        }
        var watchVue = {
            toggleMini: function() {
                localStorage.setItem("toggle_mini", this.toggleMini);
            },
        }
        var dataVue = {
            sidebarMenu: true,
            rightMenu: false,
            toggleMini: false,
            dark: false,
            group: null,
            search: '',
            loader: false,
            loading: false,
            loading2: false,
            loading3: false,
            valid: true,
            notifMessage: '',
            notifType: '',
            snackbar: false,
            timeout: 4000,
            snackbarType: '',
            snackbarMessage: '',
            show: false,
            show1: false,
            show2: false,
            cartCounter: 0,
            orderCounter: 0,
            rules: {
                email: v => !!(v || '').match(/@/) || '<?= lang('App.emailValid'); ?>',
                length: len => v => (v || '').length <= len || `<?= lang('App.invalidLength'); ?> ${len}`,
                password: v => !!(v || '').match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/) ||
                    '<?= lang('App.strongPassword'); ?>',
                min: v => v.length >= 8 || '<?= lang('App.minChar'); ?>',
                required: v => !!v || '<?= lang('App.isRequired'); ?>',
                number: v => Number.isInteger(Number(v)) || "<?= lang('App.isNumber'); ?>",
                zero: v => v > 0 || "<?= lang('App.isZero'); ?>"
            },
        }
        var methodsVue = {
            toggleTheme() {
                this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
                localStorage.setItem("dark_theme", this.$vuetify.theme.dark.toString());
            },

            // Format Ribuan Rupiah versi 1
            RibuanLocale(key) {
                const rupiah = 'Rp' + Number(key).toLocaleString('id-ID');
                return rupiah
            },
            RibuanLocaleNoRp(key) {
                const rupiah = Number(key).toLocaleString('id-ID');
                return rupiah
            },
            // Format Ribuan Rupiah versi 2
            Ribuan(key) {
                // versi 1
                /* var number_string = key.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                } */

                const format = key.toString().split('').reverse().join('');
                const convert = format.match(/\d{1,3}/g);
                const rupiah = 'Rp' + convert.join('.').split('').reverse().join('');
                return rupiah;
            },

            getCartCount() {
                axios.get(`<?= base_url(); ?>openapi/cart/count`)
                    .then(res => {
                        // handle success
                        var data = res.data;
                        this.cartCounter = data.data;
                    })
                    .catch(err => {
                        // handle error
                        console.log(err.response);
                    })
            },

            getOrderCount() {
                axios.get(`<?= base_url(); ?>api/order/count/pending_processed`)
                    .then(res => {
                        // handle success
                        var data = res.data;
                        this.orderCounter = data.data;
                    })
                    .catch(err => {
                        // handle error
                        console.log(err.response);
                    })
            },
        }
        Vue.component('paginate', VuejsPaginate)
    </script>
    <?= $this->renderSection('js') ?>
    <script>
        new Vue({
            el: '#app',
            vuetify: new Vuetify(),
            computed: computedVue,
            data: dataVue,
            mounted: mountedVue,
            created: createdVue,
            watch: watchVue,
            methods: methodsVue
        })
    </script>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/642f992d31ebfa0fe7f6f669/1gtcusneh';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
</body>

</html>