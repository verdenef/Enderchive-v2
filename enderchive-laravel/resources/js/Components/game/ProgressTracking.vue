<template>
  <div
    class="rounded-2xl p-5"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center gap-2 mb-4">
      <Icon name="lucide:trophy" class="w-5 h-5 text-[#22c55e]" />
      <h3 class="text-white">Progress</h3>
    </div>
    <div class="mb-5">
      <XPBar :current="completionPercentage" label="Completion" :show-percentage="true" />
    </div>
    <div class="grid grid-cols-2 gap-3">
      <div
        class="rounded-lg p-3 border border-[#2b1d3a] relative group"
        style="background: rgba(27, 22, 38, 0.5)"
      >
        <div class="flex items-center justify-between mb-1">
          <div class="flex items-center gap-2">
            <Icon name="lucide:clock" class="w-4 h-4 text-[#22c55e]" />
            <p class="text-white/60 text-xs">Playtime</p>
          </div>
          <button
            v-if="onEditPlaytime"
            @click="onEditPlaytime"
            class="opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-white/10 rounded"
            title="Edit playtime"
          >
            <Icon name="lucide:edit" class="w-3 h-3 text-white/60 hover:text-[#22c55e]" />
          </button>
        </div>
        <p class="text-white font-semibold">{{ playtimeHours }}h</p>
      </div>
      <div
        class="rounded-lg p-3 border border-[#2b1d3a]"
        style="background: rgba(27, 22, 38, 0.5)"
      >
        <div class="flex items-center gap-2 mb-1">
          <Icon name="lucide:award" class="w-4 h-4 text-[#7c3aed]" />
          <p class="text-white/60 text-xs">Achievements</p>
        </div>
        <p class="text-white font-semibold">{{ achievementCount }}/{{ totalAchievements }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import Icon from '../ui/Icon.vue'
import XPBar from '../ui/XPBar.vue'

const props = defineProps({
  completionPercentage: {
    type: Number,
    required: true
  },
  playtimeHours: {
    type: Number,
    required: true
  },
  achievementCount: {
    type: Number,
    required: true
  },
  totalAchievements: {
    type: Number,
    required: true
  },
  onEditPlaytime: {
    type: Function,
    default: undefined
  }
})
</script>

