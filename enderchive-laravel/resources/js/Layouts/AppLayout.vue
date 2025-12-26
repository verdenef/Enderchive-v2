<template>
  <div 
    class="min-h-screen relative"
    style="background: linear-gradient(135deg, #0a0015 0%, #1c0031 50%, #250040 100%); background-color: #0a0015;"
  >
    <FloatingParticles />
    <ThemeProvider :theme="theme" :layout="layout">
      <ErrorBoundary>
        <slot />
        <Toast />
        <QuickActions />
      </ErrorBoundary>
    </ThemeProvider>
  </div>
</template>

<script setup>
import FloatingParticles from '../Components/effects/FloatingParticles.vue'
import ThemeProvider from '../Components/layout/ThemeProvider.vue'
import ErrorBoundary from '../Components/layout/ErrorBoundary.vue'
import Toast from '../Components/ui/Toast.vue'
import QuickActions from '../Components/ui/QuickActions.vue'

defineProps({
  theme: {
    type: String,
    default: 'default'
  },
  layout: {
    type: String,
    default: 'standard'
  }
})
</script>

