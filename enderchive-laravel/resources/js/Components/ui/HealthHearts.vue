<template>
  <div class="flex items-center gap-0.5">
    <Icon
      v-for="i in maxHearts"
      :key="i"
      name="lucide:heart"
      :class="[
        'w-4 h-4 transition-all',
        i <= rating ? 'text-red-500 fill-red-500' : 'text-white/20'
      ]"
      :style="i <= rating ? { filter: 'drop-shadow(0 0 4px rgba(239, 68, 68, 0.6))' } : {}"
    />
  </div>
</template>

<script setup>
import Icon from './Icon.vue'

const props = defineProps({
  rating: {
    type: Number,
    required: true
  },
  maxHearts: {
    type: Number,
    default: 5
  }
})
</script>

