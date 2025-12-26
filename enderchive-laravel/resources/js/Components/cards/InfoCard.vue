<template>
  <div
    class="rounded-2xl p-4 transition-all duration-200 hover:brightness-110"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 4px 16px rgba(124, 58, 237, 0.25)"
  >
    <p class="text-white/60 text-xs mb-2">{{ label }}</p>
    <div v-if="isRating && displayRating !== null" class="flex items-center gap-2">
      <HealthHearts :rating="displayRating" :max-hearts="5" />
      <span class="text-white/80 text-sm font-semibold">{{ displayValue }}/5</span>
    </div>
    <p v-else-if="isRating" class="text-white font-semibold">No reviews yet</p>
    <p v-else class="text-white font-semibold">{{ value }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import HealthHearts from '../ui/HealthHearts.vue'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: String,
    required: true
  },
  isRating: {
    type: Boolean,
    default: false
  }
})

const convertRating = (ratingValue) => {
  const numValue = parseFloat(ratingValue)
  if (Number.isNaN(numValue)) return null
  if (numValue > 5) {
    return numValue / 2
  }
  return numValue
}

const displayRating = computed(() => props.isRating ? convertRating(props.value) : null)
const displayValue = computed(() => props.isRating && displayRating.value !== null ? displayRating.value.toFixed(1) : props.value)
</script>

