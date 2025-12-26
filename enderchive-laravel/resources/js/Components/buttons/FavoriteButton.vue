<template>
  <button
    :class="[
      'px-3 py-2.5 rounded-sm transition-all duration-200 hover:brightness-110 active:translate-y-[2px] flex items-center justify-center',
      isFavorite ? 'star-glow' : ''
    ]"
    :style="buttonStyle"
    @click.stop="$emit('click')"
    :aria-label="isFavorite ? 'Remove from favorites' : 'Add to favorites'"
  >
    <Icon
      name="lucide:star"
      :class="['w-4 h-4 transition-all duration-200', isFavorite ? 'fill-current' : '']"
      :style="iconStyle"
    />
  </button>
</template>

<script setup>
import { computed } from 'vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  isFavorite: {
    type: Boolean,
    default: false
  }
})

defineEmits(['click'])

const buttonStyle = computed(() => {
  const base = {
    background: 'rgba(27, 22, 38, 0.6)',
    border: props.isFavorite ? '2px solid rgba(251, 191, 36, 0.5)' : '2px solid rgba(43, 29, 58, 0.7)',
    color: props.isFavorite ? '#fbbf24' : 'rgba(255, 255, 255, 0.6)',
  }
  
  if (props.isFavorite) {
    base.boxShadow = 'inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 0 12px rgba(251, 191, 36, 0.4)'
  } else {
    base.boxShadow = 'inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2)'
  }
  
  return base
})

const iconStyle = computed(() => ({
  color: props.isFavorite ? '#fbbf24' : 'rgba(255, 255, 255, 0.6)',
  fill: props.isFavorite ? '#fbbf24' : 'none',
  filter: props.isFavorite ? 'drop-shadow(0 0 8px rgba(251, 191, 36, 0.8))' : 'none',
  transform: props.isFavorite ? 'scale(1.1)' : 'scale(1)',
}))
</script>


