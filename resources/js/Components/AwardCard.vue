<template>
  <Card hover clickable class="premio-card">
    <template #image>
      <img :src="premio.image" :alt="premio.name" class="premio-card__image" />
    </template>

    <div class="premio-card__body">
      <h3 class="premio-card__title">{{ premio.name }}</h3>
      <p class="premio-card__points">{{ premio.points }} puntos</p>
    </div>

    <template #footer>
      <Button variant="outline" @click="$emit('ver-mas', premio)">
        Ver más
      </Button>
    </template>
  </Card>
</template>

<script setup>
import Card from '../components/Card.vue';
import Button from '../components/Button.vue';

defineProps({
  premio: {
    type: Object,
    required: true
  }
});

defineEmits(['ver-mas']);
</script>
