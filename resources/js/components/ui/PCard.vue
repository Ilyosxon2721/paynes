<template>
  <div class="p-card">
    <div v-if="title || $slots.header" class="p-card__header">
      <slot name="header">
        <div class="p-card__title">{{ title }}</div>
        <div v-if="subtitle" class="p-card__subtitle">{{ subtitle }}</div>
      </slot>
    </div>
    <div class="p-card__body">
      <slot />
    </div>
    <div v-if="$slots.footer" class="p-card__footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: null },
  subtitle: { type: String, default: null }
});
</script>
