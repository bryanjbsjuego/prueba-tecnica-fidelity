<template>
    <div class="page">
        <Header />

        <main class="page__content">
            <div>
                <img :src="bannerAlliance"></img>
            </div>
            <div class="container">
                <div class="page-header">
                    <h1 class="page-title">Alianzas</h1>
                    <p class="page-subtitle">Con las Alianzas Conecta encontrarás mútiples opciones para cada momento de tu vida. Podrás: hacer, comprar, ir y saber. ¡Descrúbrelas y disfruta!</p>
                </div>

                <Loader v-if="loading" overlay message="Cargando alianzas..." />

                <div v-else-if="error" class="alert alert--error">
                    {{ error }}
                </div>

                <div v-else-if="alianzas.length === 0" class="empty-state">
                    <p>No hay alianzas disponibles para tu categoría</p>
                </div>

                <div v-else class="alianzas-grid">
                    <AllianceCard v-for="alianza in alianzas" :key="alianza.alianza_id" :alianza="alianza"
                        @obtener="handleObtenerAlianza" />
                </div>
                <Pagination v-if="pagination.total_pages > 1" :current-page="pagination.current_page"
                    :total-pages="pagination.total_pages" @change="handlePageChange" />
            </div>
        </main>

        <Footer />

        <Modal v-model="showSuccessModal" title="¡Alianza Obtenida!" :show-close="true" @close="handleModalClose">
            <div class="success-message">
                <div class="success-icon">✓</div>
                <p class="success-text">
                    Has obtenido exitosamente la alianza:<br>
                    <strong>{{ obtainedAlianza?.nombre }}</strong>
                </p>
                <p class="success-description">
                    {{ obtainedAlianza?.descripcion }}
                </p>
            </div>
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
import bannerAlliance from '../../images/Banners/banner-alianza.jpg.jpg'

const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const alianzas = ref([]);
const showSuccessModal = ref(false);
const obtainedAlianza = ref(null);

const pagination = reactive({
    current_page: 1,
    per_page: 6,
    total: 0,
    total_pages: 0
});


// Credenciales predefinidas del operador (backend interno)
const OPERATOR_CREDENTIALS = {
  usuario: 'operador1',
  contrasena: 'Password123'
};

// Obtener categoría del cliente desde customer data
const categoriaClienteId = computed(() => {
    return authStore.customer?.category || 1;
});

onMounted(async () => {
  trackPageView('Alianzas', window.location.pathname);

  // Auto-login de operador si no existe
  await ensureOperatorSession();

  // Cargar alianzas
  await loadAlianzas(); // ← Agregar await aquí también
});

/**
 * Asegurar que existe una sesión de operador válida
 */
async function ensureOperatorSession() {
  // Si ya existe sesión de operador, no hacer nada
  if (authStore.operatorUuid && authStore.operatorToken) {
    return;
  }

  try {

    const response = await operatorLogin(
      OPERATOR_CREDENTIALS.usuario,
      OPERATOR_CREDENTIALS.contrasena
    );

    if (response.success) {
      // Guardar en store
      authStore.setOperatorSession(
        response.token,
        response.uuid,
        response.operador
      );
    } else {
      console.error('❌ Login falló:', response);
      throw new Error('Login no exitoso');
    }
  } catch (err) {
    console.error('❌ Error al iniciar sesión de operador:', err);
    error.value = 'Error al conectar con el servicio de alianzas';
  }
}

async function loadAlianzas() {
  loading.value = true;
  error.value = '';

  try {
    const response = await getAlliances(
      authStore.operatorUuid,
      categoriaClienteId.value,
      pagination.current_page,
      pagination.per_page
    );

    if (response.success) {
      alianzas.value = response.alianzas;
      Object.assign(pagination, response.pagination);
    } else {
      error.value = 'Error al cargar las alianzas';
    }
  } catch (err) {
    console.error('Load alianzas error:', err);

    if (err.response?.status === 401) {
      // Token expiró, intentar re-login
      await ensureOperatorSession();
      // Reintentar carga CON AWAIT
      await loadAlianzas();  // ✅ AGREGAR AWAIT
      return; // ← Importante: salir para evitar el finally
    } else {
      error.value = 'Error al conectar con el servidor';
    }
  } finally {
    loading.value = false;
  }
}

async function handleObtenerAlianza(alianza) {
    trackCTAClick(`obtener_alianza_${alianza.nombre}`, 'alianzas');

    try {
        const uuid = authStore.operatorUuid || 'demo-uuid';

        const response = await usedWingAlliance(uuid, alianza.id);

        if (response.success) {
            trackSuccessRedeem(alianza.nombre, 'alianza');

            // Remover alianza de la lista
            alianzas.value = alianzas.value.filter(a => a.id !== alianza.id);

            // Mostrar modal de éxito
            obtainedAlianza.value = alianza;
            showSuccessModal.value = true;

            // Si la lista queda vacía, recargar
            if (alianzas.value.length === 0 && pagination.current_page > 1) {
                pagination.current_page--;
                loadAlianzas();
            }
        } else {
            trackErrorRedeem(alianza.nombre, 'alianza', response.message);
            alert('Error al obtener la alianza: ' + response.message);
        }
    } catch (err) {
        console.error('Obtener alianza error:', err);
        trackErrorRedeem(alianza.nombre, 'alianza', 'Error de conexión');
        alert('Error al obtener la alianza. Intenta nuevamente.');
    }
}

function handlePageChange(page) {
    pagination.current_page = page;
    loadAlianzas();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleModalClose() {
    showSuccessModal.value = false;
    obtainedAlianza.value = null;
}
</script>
