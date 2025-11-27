<script setup lang="ts">
import Footer from '../Layouts/Footer.vue';
import Header from '../Layouts/Header.vue';
import bannerImage from '../../images/Banners/banner-home2.jpg'; 
</script>

<template>
    <div class="home">
        <Header />

        <main class="home__content">
            <!-- Banner Principal -->
            <section class="banner">
                <div class="banner__overlay"></div>
                <img :src="bannerImage" alt="Banner principal" class="banner__image" />
                <div class="banner__content">
                    <h1 class="banner__title">Bienvenido a Fidelity Conecta</h1>
                    <p class="banner__subtitle">Conectamos tu futuro con beneficios exclusivos</p>
                    <!-- <button class="banner__cta">Descubre más</button> -->
                </div>
            </section>

            <!-- Sección de contenido adicional (opcional) -->
            <section class="content-section">
                <div class="content-section__container">
                    <h2>Nuestros Servicios</h2>

                </div>
            </section>
        </main>

        <Footer />
    </div>
</template>

