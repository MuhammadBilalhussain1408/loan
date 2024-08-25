import './bootstrap';
import '../css/app.css';


// Import modules...
import {createApp, h} from 'vue';
import {createInertiaApp, InertiaLink, InertiaHead} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import {library} from '@fortawesome/fontawesome-svg-core'
import {fas} from '@fortawesome/free-solid-svg-icons'
import {far} from '@fortawesome/free-regular-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import VueSweetalert2 from 'vue-sweetalert2';
import {Head, Link} from '@inertiajs/inertia-vue3'
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';
import CKEditor from '@ckeditor/ckeditor5-vue';
import Toaster from "@meforma/vue-toaster";

import numeral from 'numeral';

library.add(fas)
library.add(far)
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'YoPractice';
import VueGridLayout from 'vue-grid-layout'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, app, props, plugin}) {
        const inertiaApp = createApp({render: () => h(app, props)})
            .use(plugin)
            .use(ZiggyVue, Ziggy);
        inertiaApp.component('font-awesome-icon', FontAwesomeIcon)
        inertiaApp.component('Multiselect', Multiselect)

        inertiaApp.component('flat-pickr', flatPickr)
        inertiaApp.component('InertiaHead', Head)
        inertiaApp.component('InertiaLink', Link)
        inertiaApp.use(VueSweetalert2);
        inertiaApp.use(CKEditor);
        inertiaApp.use(VueGridLayout);
        inertiaApp.use(Toaster);
        inertiaApp.mixin({
            methods: {
                moment: function (date = '') {
                    return moment(date);
                },
                can: function (permission) {
                    if (permission === '') {
                        return true
                    }
                    return this.$page.props.user.can.includes(permission);
                },
                hasAnyPermission: function (permissions) {

                    var allPermissions = this.$page.props.user.can;
                    var hasPermission = false;
                    permissions.forEach(function (item) {
                        if (allPermissions[item]) hasPermission = true;
                    });
                    return hasPermission;
                },
            }
        })
        inertiaApp.config.globalProperties.$filters = {
            currency(value, currency = null, decimals = 2) {
                if (!currency) {
                    return new Intl.NumberFormat('en-IN', {
                        maximumSignificantDigits: 20,
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals,
                    }).format(value)
                }

            },
            formatNumber(value, decimals = 2) {
                if (!decimals) {
                    return numeral(value).format('0,0.00')
                }
                return numeral(value).format('0,0.00')
            },
            timeAgo(date) {
                return moment(date).fromNow()
            },
            date(date, format = "YYYY-MM-DD") {
                return moment(date).format(format)
            },
            time(time, format = "YYYY-MM-DD HH:mm") {
                return moment(time).format(format)
            },
        }
        inertiaApp.mount(el);
        return inertiaApp;
    },
});

InertiaProgress.init({color: '#4B5563'});



