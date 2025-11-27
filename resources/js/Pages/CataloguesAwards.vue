<template>
  <div class="page">
    <Header />

    <main class="page__content">
      <div class="banner-hero">
        <img :src="bannerHome" alt="Banner" />
      </div>

      <div class="container">
        <div class="page-header">
          <h1 class="page-title">Catálogo de Premios</h1>
          <p class="page-subtitle">
            Te tenemos nuevas misiones cargadas de puntos. Gana, acumula y cambia tus puntos por bonos para pagar tu factura de energía y muchos premios más
          </p>
        </div>

        <div class="filters">
          <div class="filters__search">
            <TextInput
              v-model="searchQuery"
              type="text"
              placeholder="Buscar premio..."
              @input="handleSearch"
            />
            <small v-if="searchQuery.length > 0 && searchQuery.length < minSearchChars" class="hint">
              Escribe al menos {{ minSearchChars }} caracteres para buscar
            </small>
          </div>

          <div class="filters__order">
            <label for="order">Ordenar por:</label>
            <div class="filters__order-wrapper">
              <select id="order" v-model="selectedOrder" @change="handleOrderChange">
                <option value="">Sin ordenar</option>
                <option value="money_asc">De menor a mayor costo</option>
                <option value="money_desc">De mayor a menor costo</option>
                <option value="name_asc">Nombre: A - Z</option>
                <option value="name_desc">Nombre: Z - A</option>
              </select>
              <button
                v-if="selectedOrder || (searchQuery && searchQuery.length >= minSearchChars)"
                type="button"
                class="filters__clear-btn"
                @click="clearFilters"
                aria-label="Limpiar filtros"
                title="Limpiar filtros y búsqueda"
              >
                ✕
              </button>
            </div>
          </div>
        </div>

        <!-- Resultado de búsqueda -->
        <div v-if="searchQuery && searchQuery.length >= minSearchChars" class="search-result">
          <h2 class="search-result__title">Resultado de búsqueda</h2>
          <p class="search-result__term">{{ searchQuery }}</p>
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
          <AwardCard
            v-for="award in awards"
            :key="award.id"
            :premio="award"
            @ver-mas="handleViewMore"
          />
        </div>

        <Pagination
          v-if="pagination.total_pages > 1"
          :current-page="pagination.current_page"
          :total-pages="pagination.total_pages"
          @change="handlePageChange"
        />
      </div>
    </main>

    <Footer />

    <Modal v-model="showDetailModal" :title="selectedAward?.name" show-close>
      <div v-if="selectedAward" class="prize-detail">
        <img :src="selectedAward.image" :alt="selectedAward.name" class="prize-detail__image" />
        <p class="prize-detail__points">{{ selectedAward.points }} puntos</p>
        <p class="prize-detail__description">{{ selectedAward.description }}</p>
        <p class="prize-detail__description">{{ formatMoney(selectedAward.moneyValue) }} MXN</p>
      </div>

      <template #footer>
        <Button variant="primary" @click="handleRedeem" track-name="canjear_premio">
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
import bannerHome from '../../images/Banners/banner-home1.jpg';
import TextInput from '../components/TextInput.vue';
import Swal from 'sweetalert2';

const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const awards = ref([]);
const searchQuery = ref('');
const selectedOrder = ref('');
const showDetailModal = ref(false);
const selectedAward = ref(null);

const minSearchChars = 3;
const searchTimeout = ref(null);
const searchDebounceDelay = 500;

const pagination = reactive({
  current_page: 1,
  per_page: 8,
  total: 0,
  total_pages: 0
});

onMounted(() => {
  trackPageView('Catálogo de Premios', window.location.pathname);
  pagination.current_page = 1;
  loadAwards();
});

async function loadAwards() {
  loading.value = true;
  error.value = '';

  // Parsear el orden seleccionado
  let sort_by = 'name';
  let order = 'asc';

  if (selectedOrder.value && selectedOrder.value.trim() !== '') {
    const parts = selectedOrder.value.split('_');
    if (parts.length >= 2) {
      sort_by = parts[0];
      order = parts[1];
    }
  }

  const requestedPage = Number(pagination.current_page) && Number(pagination.current_page) >= 1
    ? Number(pagination.current_page)
    : 1;

  // Construir parámetros
  const params = {
    page: requestedPage,
    limit: pagination.per_page,
    sort_by: sort_by,
    order: order
  };


  if (searchQuery.value && searchQuery.value.length >= minSearchChars) {
    params.search = searchQuery.value;
  }

  try {
    const response = await getAwards(authStore.session, params);

    if (response.success) {
      awards.value = response.prizes || [];

      const rp = response.pagination || {};
      const respCurrent = Number(rp.current_page);
      const validatedCurrent = Number.isInteger(respCurrent) && respCurrent >= 1 ? respCurrent : requestedPage;

      Object.assign(pagination, {
        current_page: validatedCurrent,
        per_page: Number(rp.per_page) || pagination.per_page,
        total: Number(rp.total) || 0,
        total_pages: Number(rp.total_pages) || 0
      });

      if (pagination.total_pages > 0 && pagination.current_page > pagination.total_pages) {
        pagination.current_page = pagination.total_pages;
      }

    } else {
      error.value = response.message || 'Error al cargar los premios';
    }
  } catch (err) {
    error.value = 'Error al conectar con el servidor';
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  clearTimeout(searchTimeout.value);

  if (searchQuery.value.length === 0) {
    pagination.current_page = 1;
    loadAwards();
    return;
  }

  if (searchQuery.value.length < minSearchChars) {
    return;
  }

  searchTimeout.value = setTimeout(() => {
    pagination.current_page = 1;
    loadAwards();
  }, searchDebounceDelay);
}

function handleOrderChange() {
  
  pagination.current_page = 1;
  loadAwards();
}

function clearFilters() {
  selectedOrder.value = '';
  searchQuery.value = '';
  clearTimeout(searchTimeout.value);
  pagination.current_page = 1;
  loadAwards();
}

function handlePageChange(page) {
  const p = Number(page);
  if (!Number.isInteger(p)) return;
  pagination.current_page = p;
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

  const success = Math.random() > 0.3;

  if (success) {
    trackSuccessRedeem(selectedAward.value.name, 'premio');
    Swal.fire({
      title: '',
      text: '¡Premio canjeado exitosamente!',
      icon: 'success'
    });
  } else {
    trackErrorRedeem(selectedAward.value.name, 'premio', 'Puntos insuficientes');
    Swal.fire({
      title: '',
      text: '¡No tienes suficientes puntos!',
      icon: 'info'
    });
  }

  showDetailModal.value = false;
}
   
function formatMoney(value) {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(value);
}
</script>