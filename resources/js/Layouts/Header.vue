<template>
  <header class="header">
    <div class="header__container">
      <div class="header__logo">
        <router-link to="/">
          <img :src="logoConecta" />
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

      <nav class="header__nav">
        <router-link to="" class="header__nav-link">
           <img :src="isActive('') ? iconoPhoneWhite : iconoPhoneColor" /> 999 999 9999
        </router-link>
        <router-link to="" class="header__nav-link">
           <img :src="isActive('') ? iconoPlusWhite : iconoPlusColor " /> Regístrate
        </router-link>
        <router-link to="/login" class="header__nav-link"  :class="{ active: isActive('/login') }">
           <img :src="isActive('/login') ? iconoPhoneWhite : iconoPhoneColor " /> Inicia sesión
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
import { useRouter, useRoute  } from 'vue-router';
import { useAuthStore } from '../utils/auth';
import logoConecta from '../../images/Logo/logo-conecta.png';
import iconoPhoneColor from '../../images/Iconos/tel-color.png';
import iconoPhoneWhite from '../../images/Iconos/tel-blanco.png';
import iconoPlusColor from '../../images/Iconos/registro-color.png';
import iconoPlusWhite from '../../images/Iconos/registro-blanco.png';
import iconoLog from '../../images/Iconos/log-color.png';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const customer = computed(() => authStore.customer);

const isActive = (path) => route.path === path;

function handleLogout() {
  authStore.clearSession();
  router.push('/login');
}
</script>
