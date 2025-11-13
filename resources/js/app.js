import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // includes Popper
import 'vue-select/dist/vue-select.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router'
import Swal from 'sweetalert2';
import vSelect from 'vue-select';

const app = createApp(App);

app.config.globalProperties.$swal = Swal;

app.component('v-select', vSelect);
app.use(router);
app.mount('#app');
