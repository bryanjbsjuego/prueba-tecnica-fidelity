<template>
  <div v-if="numericTotalPages > 1" class="pagination" role="navigation" aria-label="Paginación">
    <!-- Botón ir a primera página -->
    <button
      class="btn_previous_next"
      :disabled="numericCurrentPage === 1"
      @click="changePage(1)"
      aria-label="Primera página"
    >
      «
    </button>

    <!-- Botón página anterior -->
    <button
      class="btn_previous_next"
      :disabled="numericCurrentPage === 1"
      @click="changePage(numericCurrentPage - 1)"
      aria-label="Página anterior"
    >
      ‹
    </button>

    <!-- Números de página -->
    <div class="pagination__pages">
      <button
        v-for="p in pages"
        :key="p"
        class="pagination__page"
        :class="{ 'is-active': p === numericCurrentPage }"
        :aria-current="p === numericCurrentPage ? 'page' : null"
        @click="changePage(p)"
      >
        {{ p }}
      </button>
    </div>

    <!-- Botón página siguiente -->
    <button
      class="btn_previous_next"
      :disabled="numericCurrentPage === numericTotalPages"
      @click="changePage(numericCurrentPage + 1)"
      aria-label="Página siguiente"
    >
      ›
    </button>

    <!-- Botón ir a última página -->
    <button
      class="btn_previous_next"
      :disabled="numericCurrentPage === numericTotalPages"
      @click="changePage(numericTotalPages)"
      aria-label="Última página"
    >
      »
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentPage: { type: [Number, String], required: true },
  totalPages: { type: [Number, String], required: true },
  maxVisible: { type: Number, default: 5 }
});

const emit = defineEmits(['change']);

const numericCurrentPage = computed(() => {
  const n = Number(props.currentPage);
  return Number.isInteger(n) && n >= 1 ? n : 1;
});

const numericTotalPages = computed(() => {
  const n = Number(props.totalPages);
  return Number.isInteger(n) && n >= 0 ? n : 0;
});

const pages = computed(() => {
  const total = numericTotalPages.value;
  const visible = Math.max(1, Math.min(props.maxVisible, total));
  const current = Math.min(Math.max(1, numericCurrentPage.value), Math.max(1, total));

  if (total <= visible) {
    return Array.from({ length: total }, (_, i) => i + 1);
  }

  const half = Math.floor(visible / 2);
  let start = current - half;
  start = Math.max(1, Math.min(start, total - visible + 1));
  const end = start + visible - 1;

  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});

function changePage(page) {
  const p = Number(page);
  if (!Number.isInteger(p)) return;
  if (p < 1 || p > numericTotalPages.value) return;
  if (p === numericCurrentPage.value) return;
  emit('change', p);
}
</script>
