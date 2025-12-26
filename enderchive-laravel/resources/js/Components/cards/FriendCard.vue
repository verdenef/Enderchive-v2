<template>
  <div
    class="flex items-center gap-4 p-4 rounded-lg transition-all duration-200 hover:bg-white/5 cursor-pointer group"
    style="background: rgba(27, 22, 38, 0.4); border: 1px solid rgba(43, 29, 58, 0.5)"
    @click="$emit('click')"
  >
    <!-- Avatar -->
    <div class="relative flex-shrink-0">
      <div
        class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold"
        style="background: rgba(124, 58, 237, 0.2); color: #8b5cf6"
      >
        {{ getInitials(username) }}
      </div>
      <div
        v-if="isOnline"
        class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2"
        style="background-color: #22c55e; border-color: rgba(27, 22, 38, 0.8)"
      />
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 mb-1">
        <h3 class="text-white font-medium text-sm">{{ username }}</h3>
        <span
          class="text-xs px-2 py-0.5 rounded"
          :style="{
            backgroundColor: isOnline ? 'rgba(34, 197, 94, 0.15)' : 'rgba(107, 116, 128, 0.15)',
            color: isOnline ? '#22c55e' : '#9ca3af'
          }"
        >
          {{ status }}
        </span>
      </div>
      <p class="text-xs text-white/50">Friends since {{ friendSince }}</p>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2 flex-shrink-0">
      <button
        @click.stop="$emit('remove')"
        class="p-2 rounded-lg transition-colors hover:bg-white/10"
        style="color: #ef4444"
        title="Remove friend"
      >
        <Icon name="lucide:user-minus" class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  username: {
    type: String,
    required: true
  },
  status: {
    type: String,
    default: 'Offline'
  },
  friendSince: {
    type: String,
    default: ''
  }
})

defineEmits(['click', 'remove'])

const isOnline = computed(() => props.status === 'Online')

const getInitials = (name) => {
  if (!name) return 'U'
  return name.substring(0, 2).toUpperCase()
}
</script>

