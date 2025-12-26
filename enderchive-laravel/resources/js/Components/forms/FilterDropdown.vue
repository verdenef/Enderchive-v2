<template>
  <div class="relative" :style="{ width: `${width}px` }">
    <GameButton
      variant="primary"
      size="sm"
      class="w-full justify-between items-center"
      @click="isOpen = !isOpen"
    >
      <span class="flex-1 text-left whitespace-nowrap">{{ value || label }}</span>
      <Icon
        name="lucide:chevron-down"
        :class="['w-4 h-4 flex-shrink-0 transition-transform', isOpen ? 'rotate-180' : '']"
      />
    </GameButton>

    <Transition name="dropdown">
      <div
        v-if="isOpen"
        class="absolute top-full mt-2 left-0 w-full rounded-lg border border-[#2b1d3a] z-20 max-h-64 overflow-y-auto"
        style="background: rgba(27, 22, 38, 0.9)"
      >
        <button
          v-for="option in options"
          :key="option"
          class="w-full px-4 py-2 text-left text-sm text-white bg-[rgba(27,22,38,0.5)] hover:bg-[rgba(124,58,237,0.25)] border-b border-[#2b1d3a] last:border-b-0 transition-colors"
          @click="handleSelect(option)"
        >
          {{ option }}
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  options: {
    type: Array,
    required: true
  },
  value: {
    type: String,
    default: ''
  },
  width: {
    type: Number,
    default: 140
  }
})

const emit = defineEmits(['update:value'])

const isOpen = ref(false)

const handleSelect = (option) => {
  emit('update:value', option)
  isOpen.value = false
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>

