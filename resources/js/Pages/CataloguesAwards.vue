<template>
    <div class="page">
        <Header />

        <main class="page__content">
            <div>
                <img :src="bannerHome"></img>
            </div>
            <div class="container">
                <div class="page-header">
                    <h1 class="page-title">Catálogo de Premios</h1>
                    <p class="page-subtitle">Te tenemos nuevas misiones cargadas de puntos. Gana, acumula y cambia tus puntos por bonos para pagar tu factura de energía y muchos premios más</p>
                </div>

                <div class="filters">
                    <div class="filters__search">
                        <TextInput v-model="searchQuery" type="text" placeholder="Buscar premio..." class=""
                            @input="handleSearch" />
                    </div>

                    <div class="filters__order">
                        <label for="order">Ordenar por:</label>
                        <select id="order" v-model="orderBy" @change="handleOrderChange">
                            <option value="asc">De menor a mayor costo</option>
                            <option value="asc">De mayor a menor costo</option>
                            <option value="asc">A - Z</option>
                            <option value="desc">Z - A</option>
                        </select>
                    </div>
                </div>

                <Loader v-if="loading" overlay message="Cargando premios..." />

                <div v-else-if="error" class="alert alert--error">
                    {{ error }}
                </div>

                <div v-else-if="awards.length === 0" class="empty-state">
                    <p>No hay premios que mostrar</p>
                    <p>Parece que no hay coincidencias. Intenta con otros filtros o palabras</p>
                </div>

                <div v-else class="prizes-grid">
                    <AwardCard v-for="award in awards" :key="award.id" :premio="award" @ver-mas="handleViewMore" />
                </div>

                <Pagination v-if="pagination.total_pages > 1" :current-page="pagination.current_page"
                    :total-pages="pagination.total_pages" @change="handlePageChange" />
            </div>
        </main>

        <Footer />

        <Modal v-model="showDetailModal" :title="selectedAward?.name" show-close>
            <div v-if="selectedAward" class="prize-detail">
                <img :src="selectedAward.image" :alt="selectedAward.name" class="prize-detail__image" />
                <p class="prize-detail__points">{{ selectedAward.points }} puntos</p>
                <p class="prize-detail__description">{{ selectedAward.description }}</p>
            </div>

            <template #footer>
                <Button @click="handleRedeem" track-name="canjear_premio">
                    Canjear Premio
                </Button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '../utils/auth';
import { getAwards } from '../services/awards';
import { trackPageView, trackCTAClick, trackSuccessRedeem, trackErrorRedeem } from '../utils/gtm';
import Header from '../Layouts/Header.vue';
import Footer from '../Layouts/Footer.vue';
import AwardCard from '../components/AwardCard.vue';
import Pagination from '../components/Pagination.vue';
import Modal from '../components/Modal.vue';
import Button from '../components/Button.vue';
import Loader from '../components/Loader.vue';
import bannerHome from '../../images/Banners/banner-home1.jpg'
import TextInput from '../components/TextInput.vue';

const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const awards = ref([]);
const searchQuery = ref('');
const orderBy = ref('asc');
const showDetailModal = ref(false);
const selectedAward = ref(null);

const pagination = reactive({
    current_page: 1,
    per_page: 8,
    total: 0,
    total_pages: 0
});

onMounted(() => {
    trackPageView('Catálogo de Premios', window.location.pathname);
    loadAwards();
});

async function loadAwards() {
    loading.value = true;
    error.value = '';

    try {
        const response = await getAwards(authStore.session, {
            page: pagination.current_page,
            limit: pagination.per_page,
            search: searchQuery.value,
            order: orderBy.value
        });

        if (response.success) {
            awards.value = response.prizes;
            Object.assign(pagination, response.pagination);
        } else {
            error.value = 'Error al cargar los premios';
        }
    } catch (err) {
        console.error('Load premios error:', err);
        error.value = 'Error al conectar con el servidor';
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    pagination.current_page = 1;
    loadAwards();
}

function handleOrderChange() {
    pagination.current_page = 1;
    loadAwards();
}

function handlePageChange(page) {
    pagination.current_page = page;
    loadAwards();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleViewMore(award) {
    selectedAward.value = award;
    showDetailModal.value = true;
    trackCTAClick(`ver_mas_${award.name}`, 'catalogo_premios');
}

function handleRedeem() {
    if (!selectedAward.value) return;

    // Simulación de canje (aquí iría la lógica real)
    const success = Math.random() > 0.3;

    if (success) {
        trackSuccessRedeem(selectedAward.value.name, 'premio');
        alert('¡Premio canjeado exitosamente!');
    } else {
        trackErrorRedeem(selectedAward.value.name, 'premio', 'Puntos insuficientes');
        alert('Error: No tienes suficientes puntos');
    }

    showDetailModal.value = false;
}
</script>
