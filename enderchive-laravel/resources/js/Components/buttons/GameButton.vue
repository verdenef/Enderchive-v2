<template>
  <div :class="[fullWidth ? 'w-full' : '', 'relative']" style="display: inline-block">
    <div 
      ref="shadowRef"
      class="absolute pointer-events-none"
      :style="{
        background: variantStyle.bottomShadow,
        top: '2px',
        left: 0,
        right: 0,
        width: '100%',
        height: sizeStyle.height,
        borderRadius: '0.75rem',
        transform: 'translateY(0)',
        transition: 'top 0.18s ease-out',
        zIndex: 0
      }"
      data-button-shadow
    />
    <button
      ref="buttonRef"
      :type="type"
      :aria-label="ariaLabel || (typeof $slots.default === 'function' ? undefined : $slots.default?.()?.[0]?.children)"
      :class="[
        'relative flex items-center justify-center transition-all duration-200',
        'hover:brightness-110 active:scale-[0.98]',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        fullWidth ? 'w-full' : '',
        className
      ]"
      :style="{
        background: variantStyle.background,
        border: variantStyle.border,
        color: variantStyle.color,
        height: sizeStyle.height,
        padding: sizeStyle.padding,
        fontSize: sizeStyle.fontSize,
        gap: sizeStyle.gap,
        boxShadow: variantStyle.boxShadow,
        fontWeight: 'bold',
        position: 'relative',
        zIndex: 1,
        isolation: 'isolate',
        width: '100%',
        borderRadius: '0.75rem'
      }"
      :disabled="disabled"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
      @click="$emit('click', $event)"
    >
      <span
        v-if="$slots.icon"
        class="flex items-center"
        :style="{ width: sizeStyle.iconSize, height: sizeStyle.iconSize }"
      >
        <slot name="icon" />
      </span>
      <slot />
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['purple-solid', 'purple-outline', 'green-accent', 'outline', 'ghost', 'primary', 'secondary', 'danger'].includes(value)
  },
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'sm'].includes(value)
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  className: {
    type: String,
    default: ''
  },
  ariaLabel: {
    type: String,
    default: undefined
  }
})

defineEmits(['click'])

const buttonRef = ref(null)
const shadowRef = ref(null)

const normalizedVariant = computed(() => {
  return props.variant === 'outline' ? 'secondary' : props.variant
})

const variants = {
  primary: {
    background: '#22c55e',
    border: '2px solid #4ade80',
    color: 'white',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)',
    bottomShadow: '#166534',
    hoverTransform: 'translateY(-1px)'
  },
  secondary: {
    background: 'transparent',
    border: '2px solid #7c3aed',
    color: 'white',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)',
    bottomShadow: '#4c1d95',
    hoverTransform: 'translateY(-1px)'
  },
  danger: {
    background: 'transparent',
    border: '2px solid #ef4444',
    color: 'white',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)',
    bottomShadow: '#991b1b',
    hoverTransform: 'translateY(-1px)'
  },
  'purple-solid': {
    background: '#7c3aed',
    border: '2px solid #8b5cf6',
    color: 'white',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)',
    bottomShadow: '#5b21b6',
    hoverTransform: 'translateY(-1px)'
  },
  'purple-outline': {
    background: '#1a1a2e',
    border: '2px solid #7c3aed',
    color: 'white',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)',
    bottomShadow: '#4c1d95',
    hoverTransform: 'translateY(-1px)'
  },
  'green-accent': {
    background: '#0f0f23',
    border: '2px solid #22c55e',
    color: 'white',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)',
    bottomShadow: '#166534',
    hoverTransform: 'translateY(-1px)'
  },
  ghost: {
    background: 'transparent',
    border: '2px solid transparent',
    color: 'rgba(255, 255, 255, 0.6)',
    boxShadow: 'none',
    bottomShadow: 'transparent',
    hoverTransform: 'translateY(-1px)'
  }
}

const sizes = {
  default: {
    height: '52px',
    padding: '12px 24px',
    fontSize: '16px',
    gap: '12px',
    iconSize: '20px'
  },
  sm: {
    height: '36px',
    padding: '8px 16px',
    fontSize: '14px',
    gap: '8px',
    iconSize: '16px'
  }
}

const variantStyle = computed(() => variants[normalizedVariant.value] || variants.primary)
const sizeStyle = computed(() => sizes[props.size] || sizes.default)

const handleMouseEnter = (e) => {
  if (shadowRef.value) {
    shadowRef.value.style.top = '1px'
  }
  if (buttonRef.value) {
    buttonRef.value.style.transform = variantStyle.value.hoverTransform
  }
}

const handleMouseLeave = (e) => {
  if (shadowRef.value) {
    shadowRef.value.style.top = '2px'
  }
  if (buttonRef.value) {
    buttonRef.value.style.transform = 'translateY(0)'
  }
}
</script>

