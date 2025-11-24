<template>
  <button
    :class="['btn', `btn--${variant}`, { 'btn--loading': loading, 'btn--disabled': disabled }]"
    :disabled="disabled || loading"
    :type="type"
    @click="handleClick"
  >
    <span v-if="loading" class="btn__spinner"></span>
    <span :class="{ 'btn__text--hidden': loading }">
      <slot />
    </span>
  </button>
</template>

<script setup>
import { trackCTAClick } from '../utils/gtm';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'text'].includes(value)
  },
  type: {
    type: String,
    default: 'button'
  },
  loading: Boolean,
  disabled: Boolean,
  trackName: String
});

const emit = defineEmits(['click']);

function handleClick(event) {
  if (props.trackName) {
    trackCTAClick(props.trackName, window.location.pathname);
  }
  emit('click', event);
}
</script>
