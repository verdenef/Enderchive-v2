<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center gap-3 mb-6">
      <Icon name="lucide:bar-chart" class="w-6 h-6" style="color: #22c55e" />
      <h3>Playtime History</h3>
    </div>
    <div v-if="history.length === 0" class="text-center py-8" style="background: rgba(124, 58, 237, 0.05)">
      <Icon name="lucide:bar-chart" class="w-12 h-12 mx-auto mb-3 text-white/20" />
      <p class="text-white/60 text-sm">No playtime history</p>
    </div>
    <div v-else class="space-y-2">
      <div
        v-for="entry in history"
        :key="entry.date"
        class="flex items-center justify-between p-3 rounded-lg"
        style="background: rgba(26, 26, 46, 0.5)"
      >
        <span class="text-sm text-white/80">{{ formatDate(entry.date) }}</span>
        <span class="text-sm font-semibold">{{ entry.hours }}h</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import Icon from './Icon.vue'

const props = defineProps({
  history: {
    type: Array,
    default: () => []
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>

