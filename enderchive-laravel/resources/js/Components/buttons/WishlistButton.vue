<template>
  <button
    :class="[
      'px-3 py-2.5 rounded-sm transition-all duration-200 hover:brightness-110 active:translate-y-[2px] flex items-center justify-center',
      isWishlisted ? 'heart-beat' : '',
      (isInLibrary && !isWishlisted) ? 'opacity-50 cursor-not-allowed' : ''
    ]"
    :style="buttonStyle"
    :disabled="isInLibrary && !isWishlisted"
    @click.stop="handleClick"
    :aria-label="isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
  >
    <Icon
      name="lucide:heart"
      :class="['w-4 h-4 transition-all duration-200', isWishlisted ? 'fill-current' : '']"
      :style="iconStyle"
    />
  </button>
</template>

<script setup>
import { computed } from 'vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  isWishlisted: {
    type: Boolean,
    default: false
  },
  isInLibrary: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const handleClick = () => {
  const isDisabled = props.isInLibrary && !props.isWishlisted
  if (isDisabled) return
  emit('click')
}

const buttonStyle = computed(() => {
  const base = {
    background: 'rgba(27, 22, 38, 0.6)',
  }
  
  if (props.isWishlisted) {
    base.border = '2px solid rgba(239, 68, 68, 0.5)'
    base.color = '#ef4444'
    base.boxShadow = 'inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 0 12px rgba(239, 68, 68, 0.4)'
  } else if (props.isInLibrary) {
    base.border = '2px solid rgba(255, 255, 255, 0.2)'
    base.color = 'rgba(255, 255, 255, 0.4)'
    base.boxShadow = 'inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2)'
  } else {
    base.border = '2px solid rgba(43, 29, 58, 0.7)'
    base.color = 'rgba(255, 255, 255, 0.6)'
    base.boxShadow = 'inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2)'
  }
  
  return base
})

const iconStyle = computed(() => ({
  color: props.isWishlisted ? '#ef4444' : (props.isInLibrary ? 'rgba(255, 255, 255, 0.4)' : 'rgba(255, 255, 255, 0.6)'),
  fill: props.isWishlisted ? '#ef4444' : 'none',
  filter: props.isWishlisted ? 'drop-shadow(0 0 8px rgba(239, 68, 68, 0.8))' : 'none',
  transform: props.isWishlisted ? 'scale(1.1)' : 'scale(1)',
}))
</script>


