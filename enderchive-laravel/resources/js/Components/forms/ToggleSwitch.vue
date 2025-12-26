<template>
  <button
    @click="$emit('update:modelValue', !modelValue)"
    class="relative w-12 h-6 rounded-full transition-all duration-200"
    :style="{
      background: modelValue ? '#15803d' : 'rgba(107, 114, 128, 0.3)',
      boxShadow: modelValue ? '0 0 10px rgba(21, 128, 61, 0.5)' : 'none'
    }"
  >
    <div
      class="absolute top-1 w-4 h-4 rounded-full bg-white transition-all duration-200"
      :style="{
        left: modelValue ? '26px' : '4px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.2)'
      }"
    />
  </button>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])
</script>

