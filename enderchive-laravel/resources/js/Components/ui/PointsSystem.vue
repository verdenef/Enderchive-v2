<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <Icon name="lucide:zap" class="w-5 h-5" style="color: #fbbf24" />
        <h3>Points & Streaks</h3>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
      <div class="text-center p-3 rounded-lg" style="background: rgba(251, 191, 36, 0.1)">
        <Icon name="lucide:zap" class="w-6 h-6 mx-auto mb-2" style="color: #fbbf24" />
        <div class="text-2xl font-bold mb-1" style="color: #fbbf24">{{ points.toLocaleString() }}</div>
        <div class="text-xs text-white/60">Points</div>
      </div>
      <div class="text-center p-3 rounded-lg" style="background: rgba(124, 58, 237, 0.1)">
        <Icon name="lucide:trophy" class="w-6 h-6 mx-auto mb-2" style="color: #8b5cf6" />
        <div class="text-2xl font-bold mb-1" style="color: #8b5cf6">#{{ rank }}</div>
        <div class="text-xs text-white/60">Rank</div>
      </div>
    </div>

    <div class="space-y-3 mb-4">
      <div class="flex items-center justify-between p-3 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
        <div class="flex items-center gap-2">
          <Icon name="lucide:flame" class="w-4 h-4" style="color: #ef4444" />
          <span class="text-sm text-white/80">Daily Login Streak</span>
        </div>
        <div class="flex items-baseline gap-1">
          <span class="text-lg font-bold leading-none" style="color: #ef4444">{{ dailyStreak }}</span>
          <span class="text-xs text-white/60 leading-none">days</span>
        </div>
      </div>
      <div class="flex items-center justify-between p-3 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
        <div class="flex items-center gap-2">
          <Icon name="lucide:trending-up" class="w-4 h-4" style="color: #22c55e" />
          <span class="text-sm text-white/80">Playtime Streak</span>
        </div>
        <div class="flex items-baseline gap-1">
          <span class="text-lg font-bold leading-none" style="color: #22c55e">{{ playtimeStreak }}</span>
          <span class="text-xs text-white/60 leading-none">weeks</span>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <div class="flex items-center justify-between text-xs mb-1">
        <span class="text-white/60">Rank Progress</span>
        <span class="text-white/80">Top {{ rankPercentage.toFixed(1) }}%</span>
      </div>
      <div class="h-2 rounded-full overflow-hidden" style="background: rgba(43, 29, 58, 0.5)">
        <div
          class="h-full rounded-full transition-all"
          :style="{
            width: `${rankPercentage}%`,
            background: 'linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%)'
          }"
        />
      </div>
    </div>

    <button
      @click="showHistory = !showHistory"
      class="w-full text-xs text-[#8b5cf6] hover:text-[#a78bfa] transition-colors"
    >
      {{ showHistory ? 'Hide' : 'Show' }} Recent Points
    </button>

    <div v-if="showHistory" class="mt-3 space-y-2">
      <div v-if="activities && activities.length > 0">
        <div
          v-for="(activity, index) in activities"
          :key="`${activity.action}-${activity.time}-${index}`"
          class="flex items-center justify-between p-2 rounded-lg text-xs"
          style="background: rgba(27, 22, 38, 0.5)"
        >
          <span class="text-white/80">{{ activity.action }}</span>
          <div class="flex items-center gap-2">
            <span class="font-semibold" style="color: #fbbf24">+{{ activity.points }}</span>
            <span class="text-white/40">{{ activity.time }}</span>
          </div>
        </div>
      </div>
      <div v-else class="text-xs text-white/50 text-center py-2">
        No recent point activity yet.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  points: {
    type: Number,
    default: 0
  },
  dailyStreak: {
    type: Number,
    default: 0
  },
  playtimeStreak: {
    type: Number,
    default: 0
  },
  rank: {
    type: Number,
    default: 0
  },
  totalUsers: {
    type: Number,
    default: 0
  },
  activities: {
    type: Array,
    default: () => []
  }
})

const showHistory = ref(false)

const rankPercentage = computed(() => {
  return props.totalUsers > 0 ? ((props.totalUsers - props.rank) / props.totalUsers) * 100 : 0
})
</script>

