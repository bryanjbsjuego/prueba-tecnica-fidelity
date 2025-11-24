
<template>
  <div class="input-group">
    <label v-if="label" :for="id" class="input-label">
      {{ label }}
      <span v-if="required" class="input-label__required">*</span>
    </label>

    <div class="input-wrapper">
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="['input-field', { 'input-field--error': error }]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      />

      <button
        v-if="type === 'password' && showToggle"
        type="button"
        class="input-toggle"
        @click="togglePasswordVisibility"
      >
        👁️
      </button>
    </div>

    <span v-if="error" class="input-error">{{ error }}</span>
    <span v-else-if="hint" class="input-hint">{{ hint }}</span>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  type: {
    type: String,
    default: 'text'
  },
  label: String,
  placeholder: String,
  error: String,
  hint: String,
  required: Boolean,
  disabled: Boolean,
  showToggle: Boolean,
  id: String
});

defineEmits(['update:modelValue', 'blur', 'focus']);

const inputType = ref(props.type);

function togglePasswordVisibility() {
  inputType.value = inputType.value === 'password' ? 'text' : 'password';
}
</script>
