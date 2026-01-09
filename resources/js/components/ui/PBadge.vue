<template>
  <span class="p-badge" :class="`p-badge--${variant}`">
    <slot />
  </span>
</template>

<script setup>
defineProps({
  variant: { type: String, default: 'muted' } // success, warning, error, info, muted
});
</script>
