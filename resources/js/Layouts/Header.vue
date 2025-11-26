<template>
    <header class="header">
        <div class="header__container">

            <!-- LOGO -->
            <div class="header__logo">
                <router-link to="/">
                    <img :src="logoConecta" />
                </router-link>
            </div>

            <!-- NAV PRINCIPAL (tel, registrar, login) -->
            <nav v-if="!isAuthenticated" class="header__nav">
                <a href="tel:9999999999" class="header__nav-link" :class="{ active: isActive('') }"
                    @mouseenter="isHovered.phone = true" @mouseleave="isHovered.phone = false">
                    <img :src="isActive('') || isHovered.phone ? iconoPhoneWhite : iconoPhoneColor" />
                    999 999 9999
                </a>

                <router-link to="/registro" class="header__nav-link" :class="{ active: isActive('/registro') }"
                    @mouseenter="isHovered.register = true" @mouseleave="isHovered.register = false">
                    <img :src="isActive('/registro') || isHovered.register ? iconoPlusWhite : iconoPlusColor" />
                    Regístrate
                </router-link>

                <router-link to="/login" class="header__nav-link" :class="{ active: isActive('/login') }"
                    @mouseenter="isHovered.login = true" @mouseleave="isHovered.login = false">
                    <img :src="isActive('/login') || isHovered.login ? iconoLoginWhite : iconoLoginColor" />
                    Inicia sesión
                </router-link>
            </nav>

            <!-- NAV PARA USUARIOS LOGUEADOS -->
            <nav v-if="isAuthenticated" class="header__nav">
                <a href="tel:9999999999" class="header__nav-link" :class="{ active: isActive('') }"
                    @mouseenter="isHovered.phone = true" @mouseleave="isHovered.phone = false">
                    <img :src="isActive('') || isHovered.phone ? iconoPhoneWhite : iconoPhoneColor" />
                    999 999 9999
                </a>

                <a class="header__nav-link" @click="handleLogout()" :class="{ active: isActive('') }" @mouseenter="isHovered.login = true"
                    @mouseleave="isHovered.login = false">
                    <img :src="isActive('/login') || isHovered.login ? iconoLoginWhite : iconoLoginColor">
                    Salir
                </a>

            </nav>

            <div v-if="customer" class="header__user">
                <span class="header__username">
                    {{ customer.mailContactData }}
                </span>
            </div>

        </div>
    </header>
    <div>
        <nav v-if="isAuthenticated" class="nav-second">
            <router-link to="/home" class="nav-link-second" :class="{ active: isActive('/home') }">
                Inicio
            </router-link>
            <router-link to="/catalogo-premios" class="nav-link-second" :class="{ active: isActive('/catalogo-premios') }">
                Catálogo de Premios
            </router-link>

            <router-link to="/alianzas" class="nav-link-second" :class="{ active: isActive('/alianzas') }">
                Alianzas
            </router-link>
        </nav>
    </div>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../utils/auth';

import logoConecta from '../../images/Logo/logo-conecta.png';
import iconoPhoneColor from '../../images/Iconos/tel-color.png';
import iconoPhoneWhite from '../../images/Iconos/tel-blanco.png';
import iconoPlusColor from '../../images/Iconos/registro-color.png';
import iconoPlusWhite from '../../images/Iconos/registro-blanco.png';
import iconoLoginColor from '../../images/Iconos/log-color.png';
import iconoLoginWhite from '../../images/Iconos/log-blanco.png';


const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const customer = computed(() => authStore.customer);

const isHovered = reactive({
    phone: false,
    register: false,
    login: false,
    home: false,
    awards: false,
    alliances: false
});

const isActive = (path) => route.path === path;

function handleLogout() {
    authStore.clearSession();
    router.push('/login');
}
</script>
