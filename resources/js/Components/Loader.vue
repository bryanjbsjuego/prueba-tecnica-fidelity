<template>
  <div :class="['loader', { 'loader--overlay': overlay }]">
    <div class="loader__spinner"></div>
    <p v-if="message" class="loader__message">{{ message }}</p>
  </div>
</template>

<script setup>
defineProps({
  message: String,
  overlay: Boolean
});
</script>
