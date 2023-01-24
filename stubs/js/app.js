import './bootstrap';
import '../css/multiselect.css';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import ZTable from 'zedeks-vue-inertia-datatable'
import Multiselect from 'vue-multiselect'
import {createPinia} from "pinia";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

const pinia = createPinia()

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .component('ZTable', ZTable)
            .component('Multiselect', Multiselect)
            .use(ZiggyVue, Ziggy)
            .use(pinia)
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
