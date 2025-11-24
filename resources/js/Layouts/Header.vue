<template>
  <header class="header">
    <div class="header__container">
      <div class="header__logo">
        <router-link to="/">
          🎁 Rewards System
        </router-link>
      </div>

      <nav v-if="isAuthenticated" class="header__nav">
        <router-link to="/catalogo-premios" class="header__nav-link">
          Premios
        </router-link>
        <router-link to="/alianzas" class="header__nav-link">
          Alianzas
        </router-link>
      </nav>

      <div v-if="customer" class="header__user">
        <span class="header__username">
          {{ customer.firstName }} {{ customer.lastName }}
        </span>
        <button class="header__logout" @click="handleLogout">
          Salir
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const customer = computed(() => authStore.customer);

function handleLogout() {
  authStore.clearSession();
  router.push('/login');
}
</script>
