<template>
  <div class="p-form-group">
    <label v-if="label" class="p-label" :class="{ 'p-label--required': required }">
      {{ label }}
    </label>
    <input 
      class="p-input"
      :class="{ 'p-input--error': error }"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <div v-if="error" class="p-form-error">{{ error }}</div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: null },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: null },
  disabled: { type: Boolean, default: false },
  required: { type: Boolean, default: false }
});

defineEmits(['update:modelValue']);
</script>
