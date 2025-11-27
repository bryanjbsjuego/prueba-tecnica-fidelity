<template>
  <div class="alianza-card" @mouseenter="isFlipped = true" @mouseleave="isFlipped = false">
    <div :class="['alianza-card__inner', { 'is-flipped': isFlipped }]">
      <!-- Front -->
      <div class="alianza-card__face alianza-card__face--front">
        <div class="alianza-card__image-container">
          <img
            :src="alianza.imagen_url || defaultImage"
            :alt="alianza.nombre"
            class="alianza-card__image"
          />
        </div>
        <div class="alianza-card__content">
          <h3 class="alianza-card__title">{{ alianza.nombre }}</h3>
          <p class="alianza-card__vigencia" v-if="alianza.vigencia_fin">
            Válido hasta: {{ formatDate(alianza.vigencia_fin) }}
          </p>
        </div>
      </div>

      <!-- Back -->
      <div class="alianza-card__face alianza-card__face--back">
        <div class="alianza-card__back-content">
          <h3 class="alianza-card__title">{{ alianza.nombre }}</h3>
          <p class="alianza-card__description">{{ alianza.descripcion }}</p>

          <Button
            variant="primary"
            class="alianza-card__btn"
            @click="handleObtener"
          >
            OBTENER ALIANZA
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Button from '../components/Button.vue';

const props = defineProps({
  alianza: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['obtener']);

const isFlipped = ref(false);
import defaultImage from '../../images/Alianza/alianza.jpg';

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function handleObtener() {
  emit('obtener', props.alianza);
}
</script>
