<template>
  <div class="p-alert" :class="`p-alert--${variant}`">
    <slot />
  </div>
</template>

<script setup>
defineProps({
  variant: { type: String, default: 'info' } // success, warning, error, info
});
</script>
