<template>
  <div class="w-full">
    <label v-if="label" class="block mb-2 text-sm text-white/80">
      {{ label }}
    </label>
    <input
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      :class="[
        'w-full px-4 py-3 rounded-md',
        'text-white placeholder:text-white/40',
        'focus:outline-none focus:border-[#7c3aed]',
        'transition-all duration-200',
        className
      ]"
      :style="{
        background: 'rgba(27, 22, 38, 0.6)',
        backdropFilter: 'blur(10px)',
        border: '2px solid rgba(43, 29, 58, 0.7)',
        boxShadow: 'inset 2px 2px 4px rgba(0, 0, 0, 0.3)'
      }"
      @input="$emit('update:modelValue', $event.target.value)"
      @keyup.enter.prevent="$emit('keyup.enter', $event)"
    />
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  modelValue: {
    type: String,
    default: ''
  },
  className: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: undefined
  }
})

defineEmits(['update:modelValue', 'keyup.enter'])
</script>

