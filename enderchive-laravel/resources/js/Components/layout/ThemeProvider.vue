<template>
  <slot />
</template>

<script setup>
import { watch, onMounted } from 'vue'

const props = defineProps({
  theme: {
    type: String,
    default: 'default'
  },
  layout: {
    type: String,
    default: 'standard'
  }
})

const applyTheme = () => {
  if (typeof document === 'undefined') return
  const root = document.documentElement
  
  switch (props.theme) {
    case 'default':
      root.style.setProperty('--theme-primary', '#7c3aed')
      root.style.setProperty('--theme-secondary', '#22c55e')
      root.style.setProperty('--theme-accent', '#8b5cf6')
      root.style.setProperty('--theme-bg-start', '#0a0015')
      root.style.setProperty('--theme-bg-mid', '#1c0031')
      root.style.setProperty('--theme-bg-end', '#250040')
      root.style.setProperty('--glow-purple', 'rgba(124, 58, 237, 0.25)')
      root.style.setProperty('--border-dark-purple', '#2b1d3a')
      break
    case 'purple':
      root.style.setProperty('--theme-primary', '#8b5cf6')
      root.style.setProperty('--theme-secondary', '#a78bfa')
      root.style.setProperty('--theme-accent', '#c4b5fd')
      root.style.setProperty('--theme-bg-start', '#0f0a1f')
      root.style.setProperty('--theme-bg-mid', '#1e1535')
      root.style.setProperty('--theme-bg-end', '#2d1b4e')
      root.style.setProperty('--glow-purple', 'rgba(139, 92, 246, 0.3)')
      root.style.setProperty('--border-dark-purple', '#3d2560')
      break
    case 'green':
      root.style.setProperty('--theme-primary', '#22c55e')
      root.style.setProperty('--theme-secondary', '#16a34a')
      root.style.setProperty('--theme-accent', '#4ade80')
      root.style.setProperty('--theme-bg-start', '#001a0a')
      root.style.setProperty('--theme-bg-mid', '#003318')
      root.style.setProperty('--theme-bg-end', '#004d25')
      root.style.setProperty('--glow-purple', 'rgba(34, 197, 94, 0.25)')
      root.style.setProperty('--border-dark-purple', '#0d3d1f')
      break
    case 'dark':
      root.style.setProperty('--theme-primary', '#404040')
      root.style.setProperty('--theme-secondary', '#2b2b2b')
      root.style.setProperty('--theme-accent', '#505050')
      root.style.setProperty('--theme-bg-start', '#000000')
      root.style.setProperty('--theme-bg-mid', '#0a0a0a')
      root.style.setProperty('--theme-bg-end', '#141414')
      root.style.setProperty('--glow-purple', 'rgba(64, 64, 64, 0.25)')
      root.style.setProperty('--border-dark-purple', '#2b2b2b')
      break
  }
  
  root.setAttribute('data-layout', props.layout)
}

watch([() => props.theme, () => props.layout], () => {
  applyTheme()
})

onMounted(() => {
  applyTheme()
})
</script>

