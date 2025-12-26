<template>
  <div class="glass-panel rounded-lg p-4">
    <div class="flex items-start justify-between mb-2">
      <div>
        <h4 class="font-bold">{{ username }}</h4>
        <div class="flex items-center gap-2 mt-1">
          <div class="flex">
            <Icon
              v-for="i in 5"
              :key="i"
              name="lucide:star"
              :class="['w-4 h-4', i <= rating ? 'text-yellow-400 fill-yellow-400' : 'text-white/20']"
            />
          </div>
          <span class="text-xs text-white/60">{{ formatDate(date) }}</span>
        </div>
      </div>
    </div>
    <p v-if="reviewText" class="text-white/80 mt-2">{{ reviewText }}</p>
  </div>
</template>

<script setup>
import Icon from '../ui/Icon.vue'

const props = defineProps({
  username: {
    type: String,
    required: true
  },
  rating: {
    type: Number,
    required: true
  },
  reviewText: {
    type: String,
    default: ''
  },
  date: {
    type: String,
    required: true
  },
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>

