<?php
// Memanggil library
use App\Libraries\Settings;
use App\Libraries\Language;

$language = new Language();
$setting = new Settings();
$appName = $setting->info['app_name'];
$companyNama = $setting->info['company_nama'];
$snackbarsPosition = $setting->info['snackbars_position'];
$navbarColor = $setting->info['navbar_color'];
$sidebarColor = $setting->info['sidebar_color'];
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <title><?= $title ?? ""; ?> - <?= env('appName'); ?></title>
    <meta name="theme-color" content="#1976D2" />
    <link rel="apple-touch-icon" href="<?= base_url('images/') . $imgLogo; ?>">
    <link rel="shortcut icon" href="<?= base_url('images/') . $imgLogo; ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="<?= base_url('assets/css/materialdesignicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/vuetify.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/quill.core.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/quill.snow.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/quill.bubble.css') ?>" rel="stylesheet">
    <style>
        .ql-tooltip {
            left: 25px !important;
        }
    </style>
</head>

<body>
    <!-- ========================= preloader start ========================= -->
    <div class="preloader">
        <div class="loader">
            <div class="loader-logo"><img src="<?= base_url('images/') . $imgLogo; ?>" alt="Preloader" width="65" style="margin-top: 5px;"></div>
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
            <v-app-bar app color="<?= $navbarColor; ?>" <?= ($navbarColor == 'white' ? 'light' : 'dark'); ?>>
                <v-app-bar-nav-icon @click.stop="sidebarMenu = !sidebarMenu"></v-app-bar-nav-icon>
                <v-toolbar-title></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon class="mr-2" href="<?= base_url('admin/orders') ?>" elevation="0">
                    <v-badge :content="orderCounter" :value="orderCounter" color="error" overlap>
                        <v-icon>
                            mdi-bell
                        </v-icon>
                    </v-badge>
                </v-btn>
                <?php if (!empty(session()->get('username'))) : ?>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn text class="mr-2" v-bind="attrs" v-on="on">
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
                            <v-subheader>Login: &nbsp;<v-chip color="primary" small><?= session()->get('role') == 1 ? 'admin' : 'user'; ?></v-chip>
                            </v-subheader>
                            <v-list-item link href="<?= base_url(); ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-home</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Back to Home</v-list-item-title>
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

            <v-navigation-drawer color="<?= $sidebarColor; ?>" <?= ($sidebarColor == 'white' ? 'light' : 'dark'); ?> class="elevation-3" v-model="sidebarMenu" app floating :permanent="sidebarMenu" :mini-variant.sync="mini" v-if="!isMobile">
                <v-list dark dense elevation="1">
                    <v-list-item>
                        <v-list-item-action>
                            <v-icon @click.stop="toggleMini = !toggleMini">mdi-chevron-left</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title class="text-h6">
                                <?= env('appName'); ?>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
                <v-divider></v-divider>
                <v-list nav>
                    <?php $uri = new \CodeIgniter\HTTP\URI(current_url()); ?>

                    <?php if (session()->get('role') == 1) : ?>
                        <v-list-item color="white" link href="<?= base_url('admin'); ?>" <?php if ($uri->getSegment(2) == "") { ?> <?php echo 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-home</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title><?= lang('App.dashboard') ?></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item color="white" link href="<?= base_url('admin/orders'); ?>" <?php if ($uri->getSegment(2) == "orders") { ?> <?php echo 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-receipt-text</v-icon><v-badge color="error" dot overlap v-show="orderCounter > 0"></v-badge>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title><?= lang('App.order') ?></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item color="white" link href="<?= base_url('admin/products'); ?>" <?php if ($uri->getSegment(2) == "products") { ?> <?php echo 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-package-variant-closed</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title><?= lang('App.product') ?></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-divider></v-divider>

                        <v-list-group color="white" v-for="(item, i) in pages" :key="item.title" v-model="item.active" :prepend-icon="item.action">
                            <template v-slot:activator>
                                <v-list-item-content>
                                    <v-list-item-title v-text="item.title"></v-list-item-title>
                                </v-list-item-content>
                            </template>

                            <v-list-item v-for="child in item.items" :key="child.title" link :href="child.url" v-model="child.active">
                                <v-list-item-icon>
                                    <v-icon>{{child.icon}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="child.title"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group>

                        <v-list-group color="white" v-for="(item, i) in masters" :key="item.title" v-model="item.active" :prepend-icon="item.action">
                            <template v-slot:activator>
                                <v-list-item-content>
                                    <v-list-item-title v-text="item.title"></v-list-item-title>
                                </v-list-item-content>
                            </template>

                            <v-list-item v-for="child in item.items" :key="child.title" link :href="child.url" v-model="child.active">
                                <v-list-item-icon>
                                    <v-icon>{{child.icon}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="child.title"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group>

                        <v-divider></v-divider>

                        <!-- <v-list-group color="white" v-for="(item, i) in exports" :key="item.title" v-model="item.active" :prepend-icon="item.action">
                            <template v-slot:activator>
                                <v-list-item-content>
                                    <v-list-item-title v-text="item.title"></v-list-item-title>
                                </v-list-item-content>
                            </template>

                            <v-list-item v-for="child in item.items" :key="child.title" link :href="child.url" v-model="child.active">
                                <v-list-item-icon>
                                    <v-icon>{{child.icon}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="child.title"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group> -->

                        <v-divider></v-divider>

                        <v-list-item color="white" link href="<?= base_url('admin/users'); ?>" <?php if ($uri->getSegment(2) == "user") { ?> <?php echo 'class="v-item--active v-list-item--active"'; ?> <?php } ?>>
                            <v-list-item-icon>
                                <v-icon>mdi-account-multiple</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>Users</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-group color="white" v-for="(item, i) in settings" :key="item.title" v-model="item.active" :prepend-icon="item.action">
                            <template v-slot:activator>
                                <v-list-item-content>
                                    <v-list-item-title v-text="item.title"></v-list-item-title>
                                </v-list-item-content>
                            </template>

                            <v-list-item v-for="child in item.items" :key="child.title" link :href="child.url" v-model="child.active">
                                <v-list-item-icon>
                                    <v-icon>{{child.icon}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="child.title"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group>

                    <?php endif; ?>

                </v-list>

                <template v-slot:append>
                    <v-divider></v-divider>
                    <div class="pa-3 text-center text-caption white--text">
                        <img src="<?= base_url('images/') . $imgLogo; ?>" alt="Logo" width="35">
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
                <v-container class="px-5 py-5" fluid>
                    <div class="mb-10">
                        <?= $this->renderSection('content') ?>
                    </div>
                    <br />
                    <v-footer absolute>
                        <p class="text-caption my-3">
                            &copy; 2020-{{ new Date().getFullYear() }} <?= env('appCompany'); ?> Software <?= env('appVersion'); ?> All rights reserved
                        </p>
                    </v-footer>
                </v-container>
            </v-main>

            <v-snackbar v-model="snackbar" :timeout="timeout" <?= $snackbarsPosition; ?> <?php if ($snackbarsPosition == 'top') { ?> style="top: 70px;" <?php } else { ?> style="bottom: 40px;" <?php } ?>>
                <span v-if="snackbar">{{snackbarMessage}}</span>
                <template v-slot:action="{ attrs }">
                    <v-btn text v-bind="attrs" @click="snackbar = false">
                        ok
                    </v-btn>
                </template>
            </v-snackbar>
        </v-app>
    </div>

    <script src="<?= base_url('assets/js/preloader.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify-image-input.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/axios.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuejs-paginate.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/Chart.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue-chartjs.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/quill.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue-quill-editor.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/dayjs.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs-locale-id.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs-relativeTime.js') ?>"></script>

    <script>
        dayjs.locale('id');
        dayjs().locale('id').format();
        dayjs.extend(window.dayjs_plugin_relativeTime);
    </script>

    <script>
        var vue = null;
        var computedVue = {
            mini: {
                get() {
                    return this.$vuetify.breakpoint.xsOnly || !this.toggleMini;
                },
                set(value) {
                    this.toggleMini = value;
                }
            },
            isMobile() {
                if (this.$vuetify.breakpoint.xsOnly) {
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
        var updatedVue = function() {}
        var watchVue = {
            toggleMini: function() {
                localStorage.setItem("toggle_mini", this.toggleMini);
            },
        }
        var dataVue = {
            sidebarMenu: true,
            rightMenu: false,
            toggleMini: true,
            dark: false,
            group: null,
            search: '',
            loading: false,
            loading1: false,
            loading2: false,
            loading3: false,
            loading4: false,
            loading5: false,
            loading6: false,
            loading7: false,
            loading8: false,
            loading9: false,
            loading10: false,
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
            editorOption: {
                theme: 'snow',
                modules: {
                    'toolbar': [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'font': []
                        }],
                        ['bold', 'italic', 'underline'], // toggled buttons
                        ['blockquote', 'code-block'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'align': []
                        }],
                        ['link', 'image'],
                        ['clean']
                    ],
                },
            },
            pages: [{
                title: 'Pages & Articles',
                action: 'mdi-file-document',
                active: <?php if ($uri->getSegment(2) == "pages" || $uri->getSegment(2) == "articles") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                items: [{
                    title: 'Pages',
                    icon: 'mdi-text-box-multiple',
                    url: '<?= base_url('admin/pages'); ?>',
                    active: <?php if ($uri->getSegment(2) == "pages") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                }, {
                    title: 'Articles',
                    icon: 'mdi-file-document-multiple',
                    url: '<?= base_url('admin/articles'); ?>',
                    active: <?php if ($uri->getSegment(2) == "articles") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                }, ],

            }, ],
            masters: [{
                title: 'Master',
                action: 'mdi-application-edit',
                active: <?php if ($uri->getSegment(2) == "payment" || $uri->getSegment(2) == "shipment") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                items: [{
                        title: '<?= lang('App.payment') ?>',
                        icon: 'mdi-application-edit',
                        url: '<?= base_url('admin/payment'); ?>',
                        active: <?php if ($uri->getSegment(2) == "payment") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                    },
                    {
                        title: '<?= lang('App.shipment') ?>',
                        icon: 'mdi-application-edit',
                        url: '<?= base_url('admin/shipment'); ?>',
                        active: <?php if ($uri->getSegment(2) == "shipment") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                    },

                ],

            }, ],
            exports: [{
                title: 'Export',
                action: 'mdi-file',
                active: <?php if ($uri->getSegment(2) == "export" || $uri->getSegment(2) == "export-excel") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                items: [{
                        title: 'PDF',
                        icon: 'mdi-file-pdf-box',
                        url: '<?= base_url('admin/export'); ?>',
                        active: <?php if ($uri->getSegment(2) == "export") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                    },
                    {
                        title: 'Excel',
                        icon: 'mdi-file-excel-box',
                        url: '<?= base_url('admin/export-excel'); ?>',
                        active: <?php if ($uri->getSegment(2) == "export-excel") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                    },

                ],

            }, ],
            settings: [{
                title: 'Settings',
                action: 'mdi-cog',
                active: <?php if ($uri->getSegment(2) == "settings" || $uri->getSegment(2) == "backup") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                items: [{
                        title: 'Settings',
                        icon: 'mdi-cog-outline',
                        url: '<?= base_url('admin/settings'); ?>',
                        active: <?php if ($uri->getSegment(2) == "settings") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                    },
                    {
                        title: 'Backup DB',
                        icon: 'mdi-database',
                        url: '<?= base_url('admin/backup'); ?>',
                        active: <?php if ($uri->getSegment(2) == "backup") { ?><?php echo 'true'; ?><?php } else { ?><?php echo 'false'; ?><?php } ?>,
                    },
                ],

            }, ],
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
                const format = key.toString().split('').reverse().join('');
                const convert = format.match(/\d{1,3}/g);
                const rupiah = 'Rp' + convert.join('.').split('').reverse().join('');
                return rupiah;
            },
            RibuanNoRp(key) {
                const format = key.toString().split('').reverse().join('');
                const convert = format.match(/\d{1,3}/g);
                const rupiah = convert.join('.').split('').reverse().join('');
                return rupiah;
            },

            getOrderCount() {
                axios.get(`<?= base_url(); ?>api/order/count/new_order`)
                    .then(res => {
                        // handle success
                        var data = res.data;
                        this.orderCounter = data.data;
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

            formatNumber(number) {
                const formattedNumber = new Intl.NumberFormat('<?= $language->siteLang; ?>', {
                    notation: 'compact',
                    compactDisplay: 'short',
                }).format(number);
                return formattedNumber;
            },
        }
        Vue.component('paginate', VuejsPaginate)
        Vue.use(VueQuillEditor);
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
            methods: methodsVue
        })
    </script>
</body>

</html>