<?php
/*
PT. GLOBAL ITSHOP PURWOKERTO
Toko Online: ITShop Purwokerto (Tokopedia, Shopee, Bukalapak, Blibli)
Dibuat oleh: Hari Wicaksono, S.Kom
03-2023
*/

$uri = new \CodeIgniter\HTTP\URI(current_url());

// Memanggil library Setting
use App\Libraries\Settings;

$setting = new Settings();
$snackbarsPosition = $setting->info['snackbars_position'];
$companyName = $setting->info['company_name'];
$companyAddress = $setting->info['company_address'];
$companyEmail1 = $setting->info['company_email1'];
$companyEmail2 = $setting->info['company_email2'];
$companyTelp = $setting->info['company_telp'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <?php if ($uri->getSegment(1) == "") { ?>
        <title><?= COMPANY_NAME; ?> Digital Store - <?= env('appName'); ?></title>
        <meta name="description" content="Toko Online <?= COMPANY_NAME; ?> - <?= env('appName'); ?>">
    <?php } else if ($uri->getSegment(1) == "source-code") { ?>
        <title>Source Code <?= $title ?? ""; ?> - <?= env('appName'); ?></title>
        <meta name="description" content="Source Code <?= $title ?? ""; ?> produk dari <?= COMPANY_NAME; ?> - <?= env('appName'); ?>">
    <?php } else { ?>
        <title><?= $title ?? ""; ?> | <?= COMPANY_NAME; ?> Digital Store - <?= env('appName'); ?></title>
        <meta name="description" content="<?= $title ?? ""; ?> | <?= COMPANY_NAME; ?> Digital Store - <?= env('appName'); ?>">
    <?php } ?>

    <meta name="theme-color" content="#FFFFFF" />
    <link rel="apple-touch-icon" href="<?= base_url('images/logo.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('images/logo.png') ?>">
    <meta name="robots" content="index,follow">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="<?= base_url('assets/css/materialdesignicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/vuetify.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet">
</head>

<body>
    <!-- ========================= preloader start ========================= -->
    <div class="preloader">
        <div class="loader">
            <div class="loader-logo"><img src="<?= base_url('images/logo.png') ?>" alt="Preloader" width="64" style="margin-top: 5px;"></div>
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
            <v-app-bar app color="white" elevation="2">
                <v-btn href="<?= base_url() ?>" text>
                    <v-toolbar-title style="cursor: pointer"><?= env('appName'); ?></v-toolbar-title>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn icon class="mr-3" href="<?= base_url('cart') ?>" elevation="0">
                    <v-badge :content="cartCounter" :value="cartCounter" color="red" overlap>
                        <v-icon>mdi-cart</v-icon>
                    </v-badge>
                </v-btn>
                <?php if (empty(session()->get('username'))) : ?>
                    <v-btn text class="mr-3" href="<?= base_url('login') ?>" elevation="0">
                        <v-icon>mdi-login-variant</v-icon> Login
                    </v-btn>
                <?php endif; ?>

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
                                        Hallo, <?= session()->get('username') ?>
                                    </v-list-item-title>
                                    <v-list-item-subtitle><?= session()->get('email') ?></v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-subheader>Login: &nbsp;<v-chip color="primary" small><?= session()->get('role') == 1 ? 'admin' : 'user'; ?></v-chip>
                            </v-subheader>
                            <v-list-item link href="<?= base_url(); ?><?= session()->get('role') == 1 ? 'admin' : 'member'; ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-view-dashboard</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title><?= session()->get('role') == 2 ? 'Member' : ''; ?> <?= lang('App.dashboard') ?> App</v-list-item-title>
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

            <v-footer dark padless>
                <v-card flat tile width="100%" class="flex py-4">
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col>
                                    <h2 class="font-weight-medium subheading">Temukan Toko Online Official kami:</h2>
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
                                </v-col>
                                <v-col>
                                    <h2 class="font-weight-medium subheading mb-3"><?= lang('App.aboutUs'); ?>:</h2>
                                    <h3 class="mb-3"><?= $companyName; ?> (<?= COMPANY_NAME; ?>)</h3>
                                    <h4 class="font-weight-regular mb-3"><?= $companyAddress; ?></h4>
                                    Email: <?= $companyEmail1; ?> / <?= $companyEmail2; ?><br />
                                    Telp: <?= $companyTelp; ?><br />

                                    <h3 class="font-weight-medium subheading mb-3 mt-3"><?= lang('App.payment'); ?>:</h3>
                                    1. Payment Gateway<br />
                                    <v-img src="<?= base_url('images/midtrans.png'); ?>" width="80" ratio="1"></v-img><br />
                                    2. Transfer Manual<br />
                                    <v-img src="<?= base_url('images/bri.jpg'); ?>" width="80" ratio="1"></v-img>

                                    <v-divider class="mt-5 mb-3"></v-divider>
                                    <v-btn v-for="link in links" :key="link" color="white" text rounded class="my-2" link :href="link.link">
                                        {{ link.text }}
                                    </v-btn>
                                </v-col>
                            </v-row>
                            &copy; {{ new Date().getFullYear() }} — <?= COMPANY_NAME; ?>, Jawa Tengah, Indonesia
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-footer>

            <v-snackbar v-model="snackbar" :timeout="timeout" <?= $snackbarsPosition; ?> <?php if ($snackbarsPosition == 'top') { ?> style="top: 30px;" <?php } else { ?> style="bottom: 40px;" <?php } ?>>
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
    <script src="<?= base_url('assets/js/vue-masonry-plugin-window.js') ?>"></script>
    <script src="<?= base_url('assets/js/vue-carousel.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/pusher.min.js') ?>"></script>

    <script>
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
            }
        }
        var createdVue = function() {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }
        var mountedVue = function() {
            this.getCartCount();
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
        }
        var watchVue = {}
        var dataVue = {
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
                },
                {
                    text: 'Toko Shopee',
                    icon: '<?= base_url('images/shopee-logo-31405.png'); ?>',
                    link: 'https://www.shopee.co.id/itshoppwt'
                },
                {
                    text: 'Toko Bukalapak',
                    icon: '<?= base_url('images/bukalapak-icon-png-6.png'); ?>',
                    link: 'https://www.bukalapak.com/itshoppwt'
                },
                {
                    text: 'Toko BliBli',
                    icon: '<?= base_url('images/blibli.png'); ?>',
                    link: 'https://www.blibli.com/merchant/IT-Shop-Purwokerto/ITS-70007'
                },
            ],
            links: [{
                    text: 'Syarat dan Ketentuan',
                    link: '<?= base_url('terms'); ?>'
                },
                {
                    text: 'Kebijakan Privasi',
                    link: '<?= base_url('privacy'); ?>'
                },
            ],
        }
        var methodsVue = {
            toggleTheme() {
                this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
                localStorage.setItem("dark_theme", this.$vuetify.theme.dark.toString());
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

            // Format Ribuan Rupiah versi 1
            RibuanLocale(key) {
                const rupiah = 'Rp ' + Number(key).toLocaleString('id-ID');
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
                const rupiah = 'Rp ' + convert.join('.').split('').reverse().join('');
                return rupiah;
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
            watch: watchVue,
            methods: methodsVue,
            components: {
                'carousel': VueCarousel.Carousel,
                'slide': VueCarousel.Slide,
            }
        })
    </script>
</body>

</html>