<?php
// Memanggil library
use App\Libraries\Settings;
use App\Libraries\Language;

$language = new Language();
$request = \Config\Services::request();
$agent = $request->getUserAgent();
$isMobile = $agent->getMobile();

$uri = new \CodeIgniter\HTTP\URI(current_url());
$setting = new Settings();
$appName = $setting->info['app_name'];
$appDesc = $setting->info['app_description'];
$snackbarsPosition = $setting->info['snackbars_position'];
$companyNama = $setting->info['company_nama'];
$companyAlamat = $setting->info['company_alamat'];
$companyEmail1 = $setting->info['company_email1'];
$companyEmail2 = $setting->info['company_email2'];
$companyTelp = $setting->info['company_telepon'];
$navbarColor = $setting->info['navbar_color'];
$tawkToScript = $setting->info['tawkto_script'];
$imgLogo = $setting->info['img_logo'];
$imgNavbar = $setting->info['img_navbar'];
?>
<!--
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (Tokopedia.com/itshoppwt, Shopee.co.id/itshoppwt, Toco.id/store/itshop-purwokerto)
Dibuat oleh: Hari Wicaksono, S.Kom
Created: 11-2021
Modified: 07-2023
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <?php if ($uri->getSegment(1) == "") { ?>
        <title><?= $title; ?> - <?= $appName; ?></title>
        <meta name="description" content="<?= $appDesc; ?> - <?= $appName; ?> by <?= env('appCompany'); ?>">
    <?php } else if ($uri->getSegment(1) == "source-code") { ?>
        <title>Jual <?= $title; ?> - <?= $appName; ?></title>
        <meta name="description" content="Jual <?= $title; ?> - <?= $appName; ?> by <?= env('appCompany'); ?>">
    <?php } else { ?>
        <title><?= $title; ?> - <?= $appName; ?></title>
        <meta name="description" content="<?= $title; ?> - <?= $appName; ?> by <?= env('appCompany'); ?>">
    <?php } ?>
    <meta name="theme-color" content="#1976D2" />
    <link rel="apple-touch-icon" href="<?= base_url('images/') . $imgLogo; ?>">
    <link rel="shortcut icon" href="<?= base_url('images/') . $imgLogo; ?>">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?= current_url(); ?>" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="<?= base_url('assets/css/materialdesignicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/vuetify.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet">
    <?= $this->renderSection('opengraph') ?>
</head>

<body>
    <!-- ========================= preloader start ========================= -->
    <div class="preloader" :value="overlay">
        <div class="loader">
            <div class="loader-logo"><img src="<?= base_url('images/') . $imgLogo; ?>" alt="Preloader" width="64" style="margin-top: 5px;"></div>
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
            <v-app-bar app color="<?= $navbarColor; ?>" <?= ($navbarColor == 'white' ? 'light' : 'dark'); ?> elevation="2">
                <v-toolbar-title class="text-h6 font-weight-medium" style="cursor: pointer;">
                    <a href="<?= base_url() ?>" class="text-decoration-none" title="<?= env('appName') ?>" alt="<?= env('appName') ?>">
                        <v-img src="<?= base_url('images/') . $imgNavbar; ?>" width="160" class="d-none d-sm-none d-md-flex d-lg-flex d-xl-flex"></v-img>
                        <v-img src="<?= base_url('images/') . $imgLogo; ?>" width="50" class="d-flex d-sm-flex d-md-none d-lg-none d-xl-none"></v-img>
                    </a>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <?php if ($isMobile == true) { ?>
                    <?= $this->include('App\Views\partials/searchMobile'); ?>
                <?php } else { ?>
                    <?= $this->include('App\Views\partials/search'); ?>
                <?php } ?>
                <v-spacer></v-spacer>
                <v-btn icon class="mr-1" href="<?= base_url('cart') ?>" elevation="0">
                    <v-badge :content="cartCounter" :value="cartCounter" color="red" overlap>
                        <v-icon>mdi-cart</v-icon>
                    </v-badge>
                </v-btn>
                <?php
                if (session()->get('role') == 1) {
                    $url = base_url('admin/orders');
                } else {
                    $url = base_url('member/order-list');
                }
                ?>
                <v-btn icon class="mr-1" href="<?= $url; ?>" elevation="0">
                    <v-badge :content="orderCounter" :value="orderCounter" color="error" overlap>
                        <v-icon>
                            mdi-bell
                        </v-icon>
                    </v-badge>
                </v-btn>
                <?php if (empty(session()->get('username'))) : ?>
                    <v-btn text class="mr-1" href="<?= base_url('login') ?>" elevation="0">
                        <v-icon>mdi-login-variant</v-icon> <span class="d-none d-sm-flex d-md-flex d-lg-flex d-xl-flex">Login</span>
                    </v-btn>
                <?php endif; ?>

                <?php if (!empty(session()->get('username'))) : ?>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn text class="mr-1" v-bind="attrs" v-on="on">
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
                                        Hai, <?= session()->get('first_name') . ' ' . session()->get('last_name') ?>
                                    </v-list-item-title>
                                    <v-list-item-subtitle><?= session()->get('email') ?></v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-subheader>Login: &nbsp;<v-chip color="primary" small><?= session()->get('role') == 1 ? 'admin' : 'member'; ?></v-chip>
                            </v-subheader>
                            <v-list-item link href="<?= base_url(); ?><?= session()->get('role') == 1 ? 'admin' : 'member'; ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-view-dashboard</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title><?= session()->get('role') == 2 ? 'Member' : ''; ?> <?= lang('App.dashboard') ?></v-list-item-title>
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
                <v-btn icon @click.stop="rightMenu = !rightMenu">
                    <v-icon>mdi-cog-outline</v-icon>
                </v-btn>
            </v-app-bar>

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
                            <v-btn text small link href="<?= base_url('lang/id') ?>">
                                ID
                            </v-btn>
                            <v-btn text small link href="<?= base_url('lang/en') ?>">
                                EN
                            </v-btn>
                        </v-btn-toggle>
                    </v-list-item-action>
                </v-list-item>
            </v-navigation-drawer>

            <v-main>
                <?= $this->renderSection('content') ?>
            </v-main>

            <v-divider></v-divider>

            <v-footer padless>
                <v-card flat tile width="100%" class="flex py-3">
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col>
                                    <h2 class="font-weight-medium subheading mb-3">Temukan Toko Online Official kami:</h2>
                                    <v-list flat class="mb-3">
                                        <v-list-item-group>
                                            <v-list-item v-for="(item, i) in items" :key="i" link :href="item.link" target="_blank">
                                                <v-list-item-icon>
                                                    <v-img :src="item.icon" width="40"></v-img>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.text"></v-list-item-title>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <!-- Social Media -->
                                    <!-- <v-btn icon large link href="<?= $setting->info['link_facebook']; ?>"><v-icon large color="primary" l>mdi-facebook</v-icon></v-btn>
                                    <v-btn icon large link href="<?= $setting->info['link_instagram']; ?>"><v-icon large color="pink" l>mdi-instagram</v-icon></v-btn>
                                    <v-btn icon large link href="<?= $setting->info['link_youtube']; ?>"><v-icon large color="red">mdi-youtube</v-icon></v-btn> -->
                                    <!-- -->
                                </v-col>
                                <v-col>
                                    <h2 class="font-weight-medium subheading mb-3"><?= lang('App.aboutUs'); ?>:</h2>
                                    <h2><?= $appName; ?></h2>
                                    <h3 class="font-weight-regular"><?= $companyNama; ?></h3>
                                    <p><?= $companyAlamat; ?>, Indonesia</p>
                                    <h3 class="font-weight-medium subheading mb-2 mt-3"><?= lang('App.contactUs'); ?>: </h3>
                                    <p>Email 1: <?= $companyEmail1; ?> <br />
                                        Email 2: <?= $companyEmail2; ?></p>
                                    Telp/WA: <v-btn small rounded link elevation="0" href="https://wa.me/<?= $companyTelp; ?>"><v-icon small color="green">mdi-whatsapp</v-icon> Message us</v-btn><br />
                                    <h3 class="font-weight-medium subheading mb-2 mt-3">Jam Kerja: </h3>
                                    <p>Office: Senin - Jum'at: 09.00 - 16.00 WIB, Sabtu - Minggu: Libur<br />
                                        Pengiriman: Buka 24 Jam</p>
                                    <h3 class="font-weight-medium subheading mb-2 mt-3"><?= lang('App.payment'); ?>: </h3>
                                    <p>Payment Gateway &amp; Transfer (Konfirmasi Manual)</p>
                                    <v-img src="<?= base_url('images/midtrans.png'); ?>" width="80" ratio="1" class="float-left mr-3"></v-img>
                                    <v-img src="<?= base_url('images/bri.jpg'); ?>" width="100" ratio="1" class="float-left mr-3"></v-img>
                                    <v-img src="<?= base_url('images/bca.png'); ?>" width="80" ratio="1"></v-img>
                                    <br />
                                    <v-btn small v-for="link in links" :key="link" text rounded class="my-2" link :href="link.link">
                                        {{ link.text }}
                                    </v-btn>
                                </v-col>
                            </v-row>
                            &copy; {{ new Date().getFullYear() }} <?= env('appCompany'); ?>, Jawa Tengah, Indonesia
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-footer>

            <?= $this->renderSection('dialog') ?>

            <v-snackbar v-model="snackbar" :timeout="timeout" <?= $snackbarsPosition; ?> <?php if ($snackbarsPosition == 'top') { ?> style="top: 70px;" <?php } else { ?> style="bottom: 40px;" <?php } ?>>
                <span v-if="snackbar">{{snackbarMessage}}</span>
                <template v-slot:action="{ attrs }">
                    <v-btn text v-bind="attrs" @click="snackbar = false">
                        ok
                    </v-btn>
                </template>
            </v-snackbar>

            <!-- Push Order terbaru -->
            <v-snackbar v-model="snackbarNew" timeout="10000" bottom left max-width="350" style="bottom: 30px;" v-for="i in [currentNumber]" :key="i" v-show="dataOrderan != ''">
                <div v-if="newOrderan">
                    <v-icon color="red">mdi-fire</v-icon> {{dayjs(newOrderan.created_at).fromNow()}}<br />
                    <strong>{{newOrderan.first_name}} {{newOrderan.last_name}}</strong> telah memesan Produk:<br /><strong>{{newOrderan.product_name}}</strong>
                </div>
                <template v-slot:action="{ attrs }">
                    <v-btn small icon v-bind="attrs" @click="snackbarNew = false">
                        <v-icon small>mdi-close</v-icon>
                    </v-btn>
                </template>
            </v-snackbar>
            <!-- -->
        </v-app>
    </div>

    <script src="<?= base_url('assets/js/preloader.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify-image-input.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/axios.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuejs-paginate.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue-masonry-plugin-window.js') ?>"></script>
    <script src="<?= base_url('assets/js/vue-carousel.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/pusher.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs-locale-id.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs-relativeTime.js') ?>"></script>

    <script>
        dayjs.locale('id');
        dayjs().locale('id').format();
        dayjs.extend(window.dayjs_plugin_relativeTime);

        // Pusher
        // Enable pusher logging - don't include this in production
        //Pusher.logToConsole = true;
        var pusher = new Pusher('<?= env('PUSHER_APP_KEY'); ?>', {
            cluster: 'ap1'
        });
        var channel = pusher.subscribe('my-channel');
        // End Pusher
    </script>

    <script>
        const setIntervalX = (fn, delay, times) => {
            if (!times) return

            setTimeout(() => {
                fn()
                setIntervalX(fn, delay, times - 1)
            }, delay)
        }

        var computedVue = {
            mini: {
                get() {
                    return this.$vuetify.breakpoint.smAndDown || !this.toggleMini;
                },
                set(value) {
                    this.toggleMini = value;
                }
            },
            themeText() {
                return this.$vuetify.theme.dark ? '<?= lang("App.dark") ?>' : '<?= lang("App.light") ?>'
            },
            newOrderan() {
                return this.dataOrderan[Math.abs(this.currentNumber) % this.dataOrderan.length];
            }
        }
        var createdVue = function() {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        }
        var mountedVue = function() {
            this.getCartCount();
            this.getOrderCount();
            setTimeout(() => this.pushNewOrder(), 4000);
            setTimeout(() => this.startNotification(), 5000);
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

            // Pusher Client
            // Disini fungsi auto refresh menggunakan Pusher saat admin melakukan data insert, update, delete
            channel.bind('my-event', (data) => {
                if (data.event == 'new_order') {
                    //this.snackbar = true;
                    //this.snackbarMessage = data.message;
                    this.stopNotification();
                    setTimeout(() => this.pushNewOrder(), 1000);
                    setTimeout(() => this.startNotification(), 2000);
                }
            });
        }
        var updatedVue = function() {}
        var watchVue = {}
        var dataVue = {
            overlay: true,
            sidebarMenu: true,
            rightMenu: false,
            toggleMini: false,
            dark: false,
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
            items: [{
                text: 'Toko Tokopedia',
                icon: '<?= base_url('images/tokopedia-icon512.png'); ?>',
                link: 'https://www.tokopedia.com/itshoppwt'
            }, {
                text: 'Toko Shopee',
                icon: '<?= base_url('images/shopee-logo-31405.png'); ?>',
                link: 'https://www.shopee.co.id/itshoppwt'
            }, {
                text: 'Toko Toco',
                icon: '<?= base_url('images/toco.png'); ?>',
                link: 'https://toco.id/store/itshop-purwokerto'
            }, {
                text: 'Toko Lazada',
                icon: '<?= base_url('images/lazada.png'); ?>',
                link: 'https://www.lazada.co.id/shop/varia-it-shop'
            }, ],
            links: [{
                text: '<?= lang('App.aboutUs'); ?>',
                link: '<?= base_url('about'); ?>'
            }, {
                text: 'Syarat & Ketentuan',
                link: '<?= base_url('terms'); ?>'
            }, {
                text: 'Kebijakan Privasi',
                link: '<?= base_url('privacy'); ?>'
            }, {
                text: 'Legal',
                link: '<?= base_url('legal'); ?>'
            }, ],
            dataOrderan: [],
            snackbarNew: false,
            currentNumber: 0,
            timer: null
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
                axios.get(`<?= base_url(); ?>openapi/order/count/pending_processed`)
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

            pushNewOrder() {
                axios.get(`<?= base_url(); ?>openapi/order/push_neworder`)
                    .then(res => {
                        // handle success
                        var data = res.data;
                        this.dataOrderan = data.data;
                    })
                    .catch(err => {
                        // handle error
                        console.log(err.response);
                    })
            },
            startNotification: function() {
                //setIntervalX(() => this.nextNewOrder(), 6000, 5);
                //setInterval(this.nextNewOrder, 6000);
                this.snackbarNew = true;
                this.timer = setInterval(this.nextNewOrder, 11000);
            },
            stopNotification: function() {
                clearTimeout(this.timer);
                this.timer = null;
                this.snackbarNew = false;
                this.currentNumber = 0;
            },
            nextNewOrder: function() {
                this.currentNumber += 1;
                this.snackbarNew = true;
            },
            prevNewOrder: function() {
                this.currentNumber -= 1;
                this.snackbarNew = true;
            },

            formatNumber(number) {
                const formattedNumber = new Intl.NumberFormat('<?= $language->siteLang; ?>', {
                    notation: 'compact',
                    compactDisplay: 'short',
                }).format(number);
                return formattedNumber;
            },
        }
        Vue.component('paginate', VuejsPaginate)
        var VueMasonryPlugin = window["vue-masonry-plugin"].VueMasonryPlugin;
        Vue.use(VueMasonryPlugin);
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
            updated: updatedVue,
            watch: watchVue,
            methods: methodsVue,
            components: {
                'carousel': VueCarousel.Carousel,
                'slide': VueCarousel.Slide,
            }
        })
    </script>
    <!--Start of Tawk.to Script-->
    <?= $tawkToScript; ?>
    <!--End of Tawk.to Script-->
</body>

</html>