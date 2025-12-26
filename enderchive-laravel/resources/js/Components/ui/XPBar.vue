<template>
  <div>
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs text-white/60">{{ label }}</span>
      <span v-if="showPercentage" class="text-xs text-white/80 font-semibold">{{ current }}%</span>
    </div>
    <div
      class="h-3 rounded-full overflow-hidden relative"
      style="background: rgba(43, 29, 58, 0.5); border: 1px solid rgba(124, 58, 237, 0.3)"
    >
      <div
        class="h-full transition-all duration-500 relative"
        :style="{
          width: `${Math.min(Math.max(current, 0), 100)}%`,
          background: 'linear-gradient(90deg, #22c55e 0%, #16a34a 100%)',
          boxShadow: '0 0 10px rgba(34, 197, 94, 0.5)'
        }"
      >
        <div
          class="absolute inset-0"
          style="background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%); animation: shimmer 2s infinite"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  current: {
    type: Number,
    required: true
  },
  label: {
    type: String,
    default: 'Progress'
  },
  showPercentage: {
    type: Boolean,
    default: false
  }
})
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}
</style>

