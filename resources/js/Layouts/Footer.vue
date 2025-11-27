<template>
    <footer class="footer">
        <div class="footer__container">

            <!-- IZQUIERDA: TEXTOS -->
            <div class="footer__left">
                <div class="footer__top">
                    <p class="footer__text">
                        Fidelity {{ currentYear }} | Conecta | Todos los derechos reservados
                    </p>
                </div>

                <div class="footer__bottom">
                    <a href="#" class="footer__link">Preguntas Frecuentes</a>
                    <span class="line"> | </span>

                    <a href="#" class="footer__link">Términos y Condiciones</a>
                    <span class="line"> | </span>

                    <a href="#" class="footer__link">Política de privacidad de datos</a>
                    <span class="line"> | </span>

                    <a href="#" class="footer__link">Política de Cookies</a>
                </div>
            </div>

            <!-- CENTRO: REDES SOCIALES -->
            <div class="footer__icons">
                <p class="footer__text">Síguenos en:</p>
                <div class="icon-row">
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <img :src="logoFacebook" class="logo" alt="Facebook" />
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                        <img :src="logoLinkedin" class="logo" alt="LinkedIn" />
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <img :src="logoYoutube" class="logo" alt="YouTube" />
                    </a>
                </div>
            </div>

            <!-- DERECHA: AYUDA (FLOTANTE) -->
            <div class="footer__help">
                <a href="#" aria-label="Ayuda">
                    <img :src="logoHelp" class="logo-help" alt="¿Necesitas ayuda?" />
                </a>
            </div>

        </div>
    </footer>
</template>


<script setup>
import { computed } from 'vue';
import logoFacebook from '../../images/redes/facebook1.png'
import logoLinkedin from '../../images/redes/linkedin.png'
import logoYoutube from '../../images/redes/youtube.png'
import logoHelp from '../../images/Iconos/btn-ayuda.png'

const currentYear = computed(() => new Date().getFullYear());
</script>


<style lang="scss" scoped>

</style>
