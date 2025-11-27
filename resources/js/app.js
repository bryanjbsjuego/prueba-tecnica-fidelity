import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia'
import router from './router';
import App from './App.vue';
import Swal from "sweetalert2";
window.Swal = Swal;

// Importar estilos SASS
import '../sass/app.scss';

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount('#app');
