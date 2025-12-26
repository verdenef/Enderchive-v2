<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'glass-panel p-4 rounded-lg min-w-[300px] max-w-[400px] shadow-lg',
            toastClasses[toast.type || 'info']
          ]"
        >
          <div class="flex items-start gap-3">
            <Icon :name="iconMap[toast.type || 'info']" class="w-5 h-5 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
              <h4 class="font-bold text-sm mb-1">{{ toast.title }}</h4>
              <p class="text-sm text-white/80">{{ toast.message }}</p>
            </div>
            <button
              @click="removeToast(toast.id)"
              class="text-white/60 hover:text-white transition-colors"
            >
              <Icon name="lucide:x" class="w-4 h-4" />
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '../../Composables/useToast'
import Icon from './Icon.vue'

const { toasts, removeToast } = useToast()

const toastClasses = {
  success: 'border-green-500/50 bg-green-500/10',
  error: 'border-red-500/50 bg-red-500/10',
  info: 'border-blue-500/50 bg-blue-500/10',
  warning: 'border-yellow-500/50 bg-yellow-500/10',
}

const iconMap = {
  success: 'lucide:check-circle',
  error: 'lucide:x-circle',
  info: 'lucide:info',
  warning: 'lucide:alert-triangle',
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>

