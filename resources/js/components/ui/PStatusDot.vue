<template>
  <span class="p-status" :class="`p-status--${status}`">
    <span class="p-status__dot"></span>
    <slot />
  </span>
</template>

<script setup>
defineProps({
  status: { type: String, default: 'muted' } // success, warning, error, info, muted
});
</script>
