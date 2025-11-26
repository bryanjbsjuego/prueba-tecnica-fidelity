<template>
  <div v-if="totalPages > 1" class="pagination">
    <button
      class="pagination__btn"
      :disabled="currentPage === 1"
      @click="changePage(currentPage - 1)"
    >
      <
    </button>

    <div class="pagination__pages">
      <button
        v-for="page in visiblePages"
        :key="page"
        class="pagination__page"
        :class="{ 'is-active': page === currentPage }"
        @click="changePage(page)"
      >
        {{ page }}
      </button>
    </div>

    <button
      class="pagination__btn"
      :disabled="currentPage === totalPages"
      @click="changePage(currentPage + 1)"
    >
      >
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  maxVisible: {
    type: Number,
    default: 5
  }
});

const emit = defineEmits(['change']);

const visiblePages = computed(() => {
  const pages = [];
  const half = Math.floor(props.maxVisible / 2);

  let start = Math.max(1, props.currentPage - half);
  let end = Math.min(props.totalPages, start + props.maxVisible - 1);

  if (end - start + 1 < props.maxVisible) {
    start = Math.max(1, end - props.maxVisible + 1);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

function changePage(page) {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('change', page);
  }
}
</script>
