<template>
  <footer class="footer">
    <div class="footer__container">
      <p class="footer__text">
        © {{ currentYear }} Rewards System. Todos los derechos reservados.
      </p>
      <div class="footer__links">
        <a href="#" class="footer__link">Términos y Condiciones</a>
        <a href="#" class="footer__link">Política de Privacidad</a>
        <a href="#" class="footer__link">Contacto</a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script>
