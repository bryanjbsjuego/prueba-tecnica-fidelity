<template>
  <div :class="['card', { 'card--hover': hover, 'card--clickable': clickable }]" @click="handleClick">
    <div v-if="$slots.image" class="card__image">
      <slot name="image" />
    </div>

    <div class="card__content">
      <slot />
    </div>

    <div v-if="$slots.footer" class="card__footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  hover: Boolean,
  clickable: Boolean
});

const emit = defineEmits(['click']);

function handleClick() {
  if (props.clickable) {
    emit('click');
  }
}
</script>
