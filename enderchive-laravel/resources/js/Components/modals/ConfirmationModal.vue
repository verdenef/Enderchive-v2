<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
        @click.self="onClose"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="onClose" />
        <div class="relative z-10 glass-panel rounded-2xl p-6 max-w-md w-full">
          <h3 class="text-xl font-bold mb-4">{{ computedTitle }}</h3>
          <p class="text-white/80 mb-6">{{ computedMessage }}</p>
          <div class="flex gap-3 justify-end">
            <GameButton variant="purple-outline" @click="onClose">
              Cancel
            </GameButton>
            <GameButton :variant="computedVariant" @click="handleConfirm">
              {{ confirmText }}
            </GameButton>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import GameButton from '../buttons/GameButton.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'Confirm Action'
  },
  message: {
    type: String,
    default: 'Are you sure you want to proceed?'
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  confirmVariant: {
    type: String,
    default: 'purple-solid'
  },
  type: {
    type: String,
    default: 'confirm'
  }
})

const emit = defineEmits(['close', 'confirm'])

const onClose = () => {
  emit('close')
}

const handleConfirm = () => {
  emit('confirm')
  onClose()
}

const computedTitle = computed(() => {
  if (props.title !== 'Confirm Action') return props.title
  if (props.type === 'logout') return 'Logout'
  if (props.type === 'delete') return 'Delete'
  return props.title
})

const computedMessage = computed(() => {
  if (props.message !== 'Are you sure you want to proceed?') return props.message
  if (props.type === 'logout') return 'Are you sure you want to logout?'
  if (props.type === 'delete') return 'This action cannot be undone.'
  return props.message
})

const computedVariant = computed(() => {
  if (props.type === 'delete') return 'purple-outline'
  return props.confirmVariant
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>

