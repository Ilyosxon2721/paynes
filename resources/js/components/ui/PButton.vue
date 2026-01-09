<template>
  <button 
    class="p-btn" 
    :class="[`p-btn--${variant}`, size ? `p-btn--${size}` : '']"
    :disabled="disabled || loading"
    :type="type"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="p-btn__loader">⏳</span>
    <slot />
  </button>
</template>

<script setup>
defineProps({
  variant: { type: String, default: 'primary' }, // primary, secondary, danger, success, ghost
  size: { type: String, default: null }, // sm, lg
  type: { type: String, default: 'button' },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false }
});

defineEmits(['click']);
</script>

<style scoped>
.p-btn__loader {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
