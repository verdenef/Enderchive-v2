<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div
        class="p-4 rounded-xl border-2 text-center"
        style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
      >
        <div class="flex items-center justify-center gap-2 mb-2">
          <Icon name="lucide:clock" class="w-4 h-4" style="color: #22c55e" />
          <span class="text-xs text-white/60">Total Playtime</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ totalPlaytime }}h</p>
      </div>
      <div
        class="p-4 rounded-xl border-2 text-center"
        style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
      >
        <div class="flex items-center justify-center gap-2 mb-2">
          <Icon name="lucide:target" class="w-4 h-4" style="color: #8b5cf6" />
          <span class="text-xs text-white/60">Games Owned</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ gamesOwned }}</p>
      </div>
      <div
        class="p-4 rounded-xl border-2 text-center"
        style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
      >
        <div class="flex items-center justify-center gap-2 mb-2">
          <Icon name="lucide:award" class="w-4 h-4" style="color: #f59e0b" />
          <span class="text-xs text-white/60">Games Completed</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ gamesCompleted }}</p>
      </div>
      <div
        class="p-4 rounded-xl border-2 text-center"
        style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
      >
        <div class="flex items-center justify-center gap-2 mb-2">
          <Icon name="lucide:trending-up" class="w-4 h-4" style="color: #22c55e" />
          <span class="text-xs text-white/60">Completion Rate</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ completionRate }}%</p>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Genre Preferences -->
      <div
        class="rounded-2xl p-5 border-2"
        style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
      >
        <h3 class="text-sm font-semibold text-white mb-3">Genre Preferences</h3>
        <div v-if="genreData.length === 0" class="text-sm text-white/60">No genre data yet.</div>
        <div v-else class="space-y-3">
          <div v-for="genre in genreData" :key="genre.name">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-white/80">{{ genre.name }}</span>
              <span class="text-white/60">{{ genre.value }}%</span>
            </div>
            <div class="h-3 rounded-full overflow-hidden" style="background: rgba(43, 29, 58, 0.5)">
              <div
                class="h-full rounded-full transition-all"
                :style="{
                  width: `${genre.value}%`,
                  background: genre.color || '#22c55e'
                }"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Platform Distribution -->
      <div
        class="rounded-2xl p-5 border-2"
        style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
      >
        <h3 class="text-sm font-semibold text-white mb-3">Platform Distribution</h3>
        <div v-if="platformData.length === 0" class="text-sm text-white/60">No platform data yet.</div>
        <div v-else class="space-y-3">
          <div v-for="platform in platformData" :key="platform.name">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-white/80">{{ platform.name }}</span>
              <span class="text-white/60">{{ platform.value }}%</span>
            </div>
            <div class="h-3 rounded-full overflow-hidden" style="background: rgba(43, 29, 58, 0.5)">
              <div
                class="h-full rounded-full transition-all"
                :style="{
                  width: `${platform.value}%`,
                  background: platform.color || '#8b5cf6'
                }"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Insights -->
    <div
      class="rounded-2xl p-5 border-2"
      style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
    >
      <h3 class="text-sm font-semibold text-white mb-3">Insights</h3>
      <div class="space-y-3">
        <div class="p-3 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
          <p class="text-sm text-white/80">
            Your favorite genre is
            <span class="text-[#22c55e] font-semibold">
              {{ favoriteGenre !== 'N/A' ? favoriteGenre : 'not set yet' }}
            </span>
            <template v-if="genreData.length > 0 && favoriteGenre !== 'N/A'">
              , making up
              {{ genreData.find((g) => g.name === favoriteGenre)?.value ?? 0 }}% of your library.
            </template>
            <template v-if="genreData.length === 0"> — no genre data yet.</template>
          </p>
        </div>
        <div class="p-3 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
          <p class="text-sm text-white/80">
            You primarily play on
            <span class="text-[#8b5cf6] font-semibold">
              {{ favoritePlatform !== 'N/A' ? favoritePlatform : 'no main platform yet' }}
            </span>
            <template v-if="platformData.length > 0 && favoritePlatform !== 'N/A'">
              , with
              {{ platformData.find((p) => p.name === favoritePlatform)?.value ?? 0 }}% of your games.
            </template>
            <template v-if="platformData.length === 0"> — no platform data yet.</template>
          </p>
        </div>
        <div class="p-3 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
          <p class="text-sm text-white/80">
            Your completion rate is
            <span class="text-[#f59e0b] font-semibold">{{ completionRate }}%</span>, which is
            {{ completionRate >= 50 ? 'above' : 'below' }} average.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Icon from '../ui/Icon.vue'

const props = defineProps({
  totalPlaytime: {
    type: Number,
    default: 0
  },
  gamesOwned: {
    type: Number,
    default: 0
  },
  gamesCompleted: {
    type: Number,
    default: 0
  },
  completionRate: {
    type: Number,
    default: 0
  },
  favoriteGenre: {
    type: String,
    default: 'N/A'
  },
  favoritePlatform: {
    type: String,
    default: 'N/A'
  },
  genreData: {
    type: Array,
    default: () => []
  },
  platformData: {
    type: Array,
    default: () => []
  },
  playtimeSeries: {
    type: Array,
    default: () => []
  }
})
</script>

