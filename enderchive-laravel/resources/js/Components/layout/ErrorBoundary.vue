<template>
  <div v-if="hasError" class="min-h-screen flex items-center justify-center relative" style="background: linear-gradient(180deg, #0a0015 0%, #1c0031 50%, #250040 100%)">
    <div class="max-w-2xl mx-auto px-4 text-center">
      <div class="glass-panel rounded-2xl p-8 md:p-12 border-4" style="border-color: #ef4444; box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3)">
        <div class="mb-6">
          <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center" style="background: rgba(239, 68, 68, 0.2); border: 3px solid #ef4444">
            <Icon name="lucide:alert-triangle" class="w-10 h-10 text-[#ef4444]" />
          </div>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold mb-4">Oops! Something went wrong</h1>
        <p class="text-white/70 mb-6 text-sm md:text-base">{{ error?.message || 'An unexpected error occurred' }}</p>
        <details v-if="isDev && errorInfo" class="mb-6 text-left">
          <summary class="cursor-pointer text-sm text-white/50 hover:text-white/70 mb-2">View error details</summary>
          <pre class="text-xs overflow-auto p-4 rounded-lg" style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(239, 68, 68, 0.3); max-height: 200px">
            <code class="text-[#ef4444]">{{ error?.stack }}</code>
          </pre>
        </details>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <GameButton variant="primary" @click="handleReset">
            <template #icon>
              <Icon name="lucide:refresh-cw" class="w-5 h-5" />
            </template>
            Try Again
          </GameButton>
          <GameButton variant="secondary" @click="handleGoHome">
            <template #icon>
              <Icon name="lucide:home" class="w-5 h-5" />
            </template>
            Go to Home
          </GameButton>
        </div>
        <p class="text-xs text-white/40 mt-8">If this problem persists, please contact support.</p>
      </div>
    </div>
  </div>
  <slot v-else />
</template>

<script setup>
import { ref, onErrorCaptured, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'

const hasError = ref(false)
const error = ref(null)
const errorInfo = ref(null)

const isDev = computed(() => {
  if (typeof window === 'undefined') return false
  return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
})

onErrorCaptured((err, instance, info) => {
  console.error('Error caught by boundary:', err, info)
  hasError.value = true
  error.value = err
  errorInfo.value = info
  return false
})

const handleReset = () => {
  hasError.value = false
  error.value = null
  errorInfo.value = null
}

const handleGoHome = () => {
  handleReset()
  router.visit('/home')
}
</script>

