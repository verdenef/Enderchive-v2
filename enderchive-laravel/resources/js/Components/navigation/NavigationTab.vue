<template>
  <button
    :class="[
      'flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200',
      'hover:scale-105 active:scale-95'
    ]"
    :style="buttonStyle"
    @click="handleClick"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <slot name="icon" />
    <span>{{ label }}</span>
  </button>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const hovered = ref(false)

const buttonStyle = computed(() => {
  const baseStyle = {
    color: 'white',
  }

  if (props.active) {
    baseStyle.background = 'rgba(34, 197, 94, 0.2)'
    baseStyle.border = '2px solid #22c55e'
    baseStyle.borderBottom = '3px solid #22c55e'
    baseStyle.boxShadow = '0 0 20px rgba(34, 197, 94, 0.4)'
  } else {
    baseStyle.background = hovered.value ? 'rgba(124, 58, 237, 0.25)' : 'rgba(124, 58, 237, 0.15)'
    baseStyle.border = '2px solid #7c3aed'
    baseStyle.borderBottom = '2px solid #7c3aed'
    baseStyle.boxShadow = hovered.value ? '0 0 30px rgba(124, 58, 237, 0.5)' : '0 0 20px rgba(124, 58, 237, 0.3)'
  }

  return baseStyle
})

const handleMouseEnter = (e) => {
  if (!props.active) {
    hovered.value = true
  }
}

const handleMouseLeave = () => {
  hovered.value = false
}

const handleClick = () => {
  emit('click')
}
</script>

