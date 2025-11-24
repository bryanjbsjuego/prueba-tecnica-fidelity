<script setup>
import { onMounted } from 'vue';
import { initGTM } from './utils/gtm';

onMounted(() => {
  initGTM();
});
</script>

<template>
  <div id="app">
    <router-view />
  </div>
</template>


