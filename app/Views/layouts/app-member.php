<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <title>App Member</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet"> 
    <link href="<?= base_url('assets/css/materialdesignicons.min.css') ?>" rel="stylesheet"> 
    <link href="<?= base_url('assets/css/vuetify.min.css') ?>" rel="stylesheet"> 
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet"> 
    <style>
        .v-data-table>.v-data-table__wrapper>table>thead>tr>th {
            font-weight: normal;
            font-size: 18px;
        }

        .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
            font-weight: normal;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <!-- ========================= preloader start ========================= -->
    <div class="preloader">
        <div class="loader">
            <div class="loader-logo"><img src="<?= base_url('images/Logo.jpg') ?>" alt="Preloader" width="64"></div>
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
                <v-app-bar-nav-icon @click.stop="sidebarMenu = !sidebarMenu"></v-app-bar-nav-icon>
                <v-toolbar-title>Member</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-badge :content="cartCounter" :value="cartCounter" color="red" class="mr-3" overlap>
                    <v-btn icon small href="<?= base_url('/cart') ?>" elevation="0">
                        <v-icon>mdi-cart</v-icon>
                    </v-btn>
                </v-badge>
                <?php if (!empty(session()->get('username'))) : ?>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn text v-bind="attrs" v-on="on">
                                <?= session()->get('username') ?> <v-icon>mdi-chevron-down</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-subheader>Login: &nbsp;<v-chip color="primary" small><?= session()->get('role') == 1 ? 'admin' : 'user'; ?></v-chip>
                            </v-subheader>
                            <v-list-item>Email: <?= session()->get('email') ?></v-list-item>
                            <v-list-item link href="/">
                                <v-list-item-icon>
                                    <v-icon>mdi-home</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.backtoHome') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item link href="/logout" @click="localStorage.removeItem('access_token')">
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

            <v-navigation-drawer v-model="sidebarMenu" app floating :permanent="sidebarMenu" :mini-variant.sync="mini" v-if="!isMobile">
                <v-list dense elevation="2">
                    <v-list-item>
                        <v-list-item-action>
                            <v-icon @click.stop="toggleMini = !toggleMini">mdi-chevron-left</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>

                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
                <v-list-item class="px-2" link href="<?= base_url('member/profile'); ?>">
                    <v-list-item-avatar>
                        <v-icon>mdi-account</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content class="text-truncate">
                        <?= session()->get('email') ?>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
                <v-list>
                    <?php $uri = new \CodeIgniter\HTTP\URI(current_url());?>
                    <v-list-item-group color="orange darken-3">
                        <?php if (session()->get('role') == 2) : ?>
                            <v-list-item link href="<?= base_url('member'); ?>" <?php if($uri->getSegment(2)==""){echo 'class="v-item--active v-list-item--active"';}?> >
                                <v-list-item-icon>
                                    <v-icon>mdi-view-dashboard</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.dashboard') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item link href="<?= base_url('member/order-list'); ?>" <?php if($uri->getSegment(2)=="order-list"){echo 'class="v-item--active v-list-item--active"';}?> >
                                <v-list-item-icon>
                                    <v-icon>mdi-receipt</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.orderList') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item link href="<?= base_url('cart'); ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-cart</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.cart') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        <?php endif; ?>
                    </v-list-item-group>
                </v-list>

                <template v-slot:append>
                    <v-divider></v-divider>
                    <div class="pa-3 text-center">
                        <span>&copy; {{ new Date().getFullYear() }}</span>
                    </div>
                </template>

            </v-navigation-drawer>

            <v-navigation-drawer v-model="rightMenu" app right bottom temporary>
                <template v-slot:prepend>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>Pengaturan</v-list-item-title>
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
                            <v-btn text small link href="/lang/id">
                                ID
                            </v-btn>
                            <v-btn text small link href="/lang/en">
                                EN
                            </v-btn>
                        </v-btn-toggle>
                    </v-list-item-action>
                </v-list-item>
            </v-navigation-drawer>

            <v-main>
                <v-container class="px-5 py-1" fluid>
                    <?= $this->renderSection('content') ?>
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

    <script>
        var vue = null;
        var computedVue = {
            mini: {
                get() {
                    return this.$vuetify.breakpoint.smAndDown || !this.toggleMini;
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
            getCartCount() {
                axios.get(`/openapi/cart/count`)
                    .then(res => {
                        // handle success
                        var data = res.data;
                        this.cartCounter = data.data;
                    })
                    .catch(err => {
                        // handle error
                        console.log(err.response);
                    })
            }
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
</body>

</html>