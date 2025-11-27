<template>
    <div class="page">
        <Header />

        <main class="page__content">
            <div>
                <img :src="bannerAlliance" alt="Banner Alianzas" />
            </div>
            <div class="container">
                <div class="page-header">
                    <h1 class="page-title">Alianzas</h1>
                    <p class="page-subtitle">Con las Alianzas Conecta encontrarás múltiples opciones para cada momento
                        de tu vida. Podrás: hacer, comprar, ir y saber. ¡Descúbrelas y disfruta!</p>
                </div>

                <Loader v-if="loading" overlay message="Cargando alianzas..." />

                <div v-else-if="error" class="alert alert--error">
                    {{ error }}
                </div>

                <div v-else-if="alliances.length === 0" class="empty-state">
                    <p>No hay alianzas disponibles para tu categoría</p>
                </div>

                <div v-else class="alianzas-grid">
                    <AllianceCard v-for="alliance in alliances" :key="alliance.alianza_id" :alianza="alliance"
                        @obtener="handleGetAlliance" />
                </div>

                <Pagination v-if="pagination.totalPages > 1" :current-page="pagination.currentPage"
                    :total-pages="pagination.totalPages" @change="handlePageChange" />
            </div>
        </main>

        <Footer />

        <Modal v-model="showSuccessModal" title="Alianza Obtenida" :show-close="true" @close="handleModalClose">
            <div class="success-message">

                <p class="success-text">
                    <strong>{{ obtainedAlliance?.nombre }}</strong>
                </p>
                <p class="success-description">
                    {{ obtainedAlliance?.descripcion }}
                </p>
            </div>
            <template #footer>
                <Button variant="primary" @click="handleModalClose">
                    Cerrar
                </Button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useAuthStore } from '../utils/auth';
import { operatorLogin } from '../services/auth';
import { getAlliances, usedWingAlliance } from '../services/alliances';
import { trackPageView, trackCTAClick, trackSuccessRedeem, trackErrorRedeem } from '../utils/gtm';
import Header from '../Layouts/Header.vue';
import Footer from '../Layouts/Footer.vue';
import AllianceCard from '../components/AllianceCard.vue';
import Pagination from '../components/Pagination.vue';
import Modal from '../components/Modal.vue';
import Loader from '../components/Loader.vue';
import bannerAlliance from '../../images/Banners/banner-alianza.jpg.jpg';
import Swal from 'sweetalert2';
import Button from '../components/Button.vue';

const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const alliances = ref([]);
const showSuccessModal = ref(false);
const obtainedAlliance = ref(null);

const pagination = reactive({
    currentPage: 1,
    perPage: 6,
    total: 0,
    totalPages: 0
});


const OPERATOR_CREDENTIALS = {
    username: 'operador1',
    password: 'Password123'
};

const customerCategoryId = computed(() => {
    return authStore.customer?.category || 1;
});

onMounted(async () => {
    trackPageView('Alianzas', window.location.pathname);

    await ensureOperatorSession();

    await loadAlliances();
});


async function ensureOperatorSession() {
    if (authStore.operatorUuid && authStore.operatorToken) {
        return;
    }

    try {
        const response = await operatorLogin(
            OPERATOR_CREDENTIALS.username,
            OPERATOR_CREDENTIALS.password
        );

        if (response.success) {

            authStore.setOperatorSession(
                response.token,
                response.uuid,
                response.operador
            );
        } else {
            console.error(' Login failed:', response);
            throw new Error('Login not successful');
        }
    } catch (err) {
        console.error('Error logging in operator:', err);
        error.value = 'Error al conectar con el servicio de alianzas';
    }
}

async function loadAlliances() {
    loading.value = true;
    error.value = '';

    try {
        const response = await getAlliances(
            authStore.operatorUuid,
            customerCategoryId.value,
            pagination.currentPage,
            pagination.perPage
        );

        if (response.success) {
            alliances.value = response.alianzas;


            pagination.currentPage = response.pagination.current_page;
            pagination.perPage = response.pagination.per_page;
            pagination.total = response.pagination.total;
            pagination.totalPages = response.pagination.total_pages;
        } else {
            error.value = 'Error al cargar las alianzas';
        }
    } catch (err) {
        console.error('Load alliances error:', err);

        if (err.response?.status === 401) {

            await ensureOperatorSession();

            await loadAlliances();
            return;
        } else {
            error.value = 'Error al conectar con el servidor';
        }
    } finally {
        loading.value = false;
    }
}

async function handleGetAlliance(alliance) {


    trackCTAClick(`obtener_alianza_${alliance.nombre}`, 'alianzas');

    try {
        const uuid = authStore.operatorUuid || 'demo-uuid';

        const allianceId = alliance.alianza_id || alliance.id;

        if (!allianceId) {
            Swal.fire({
                title: '',
                text: '¡No se pudo identificar la alianza!',
                icon: 'info'
            });
            return;
        }


        const response = await usedWingAlliance(uuid, allianceId);

        if (response.success) {
            trackSuccessRedeem(alliance.nombre, 'alianza');


            alliances.value = alliances.value.filter(a =>
                (a.alianza_id || a.id) !== allianceId
            );

            obtainedAlliance.value = alliance;
            showSuccessModal.value = true;


            if (alliances.value.length === 0 && pagination.currentPage > 1) {
                pagination.currentPage--;
                loadAlliances();
            }
        } else {
            trackErrorRedeem(alliance.nombre, 'alianza', response.message);
            Swal.fire({
                title: '',
                text: `Error al obtener la alianza: ${response.message}`,
                icon: 'info'
            });
        }
    } catch (err) {
        console.error('Get alliance error:', err);
        trackErrorRedeem(alliance.nombre, 'alianza', 'Error de conexión');
        Swal.fire({
            title: '',
            text: 'Error al obtener la alianza. Intenta nuevamente.',
            icon: 'info'
        });
    }
}

function handlePageChange(page) {
    pagination.currentPage = page;
    loadAlliances();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleModalClose() {
    showSuccessModal.value = false;
    obtainedAlliance.value = null;
}
</script>
