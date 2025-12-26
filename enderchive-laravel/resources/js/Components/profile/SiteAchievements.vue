<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <Icon name="lucide:award" class="w-5 h-5" style="color: #fbbf24" />
        <h3>Enderchive Achievements</h3>
        <span class="text-xs text-white/40">({{ unlockedCount }}/{{ totalCount }})</span>
      </div>
    </div>

    <div class="flex gap-2 mb-4 pb-4 border-b border-[#2b1d3a]">
      <button
        v-for="category in ['all', 'social', 'gaming', 'milestone']"
        :key="category"
        @click="filter = category"
        class="px-3 py-1 rounded-lg text-xs transition-all capitalize"
        :style="{
          background: filter === category ? '#7c3aed' : 'transparent',
          color: filter === category ? 'white' : 'rgba(255, 255, 255, 0.6)',
          border: filter === category ? 'none' : '1px solid rgba(43, 29, 58, 0.5)'
        }"
        @mouseenter="(e) => { if (filter !== category) e.currentTarget.style.color = 'white'; e.currentTarget.style.background = 'rgba(255, 255, 255, 0.05)' }"
        @mouseleave="(e) => { if (filter !== category) e.currentTarget.style.color = 'rgba(255, 255, 255, 0.6)'; e.currentTarget.style.background = 'transparent' }"
      >
        {{ category }}
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div
        v-for="achievement in filteredAchievements"
        :key="achievement.id"
        @click="$emit('achievement-click', achievement)"
        class="p-4 rounded-lg border-2 transition-all cursor-pointer"
        :style="{
          borderColor: achievement.unlocked ? '#22c55e' : '#2b1d3a',
          background: achievement.unlocked ? 'rgba(34, 197, 94, 0.1)' : 'rgba(27, 22, 38, 0.5)',
          opacity: achievement.unlocked ? 1 : 0.7
        }"
        @mouseenter="(e) => { e.currentTarget.style.borderColor = achievement.unlocked ? '#16a34a' : '#7c3aed' }"
        @mouseleave="(e) => { e.currentTarget.style.borderColor = achievement.unlocked ? '#22c55e' : '#2b1d3a' }"
      >
        <div class="flex items-start gap-3">
          <div
            class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
            :style="{
              color: achievement.unlocked ? '#22c55e' : 'rgba(255, 255, 255, 0.4)',
              background: achievement.unlocked ? 'rgba(34, 197, 94, 0.2)' : 'rgba(43, 29, 58, 0.5)'
            }"
          >
            <Icon v-if="achievement.unlocked" :name="achievement.icon" class="w-5 h-5" />
            <Icon v-else name="lucide:lock" class="w-5 h-5" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h4 class="text-sm font-semibold text-white">{{ achievement.title }}</h4>
              <Icon v-if="achievement.unlocked" name="lucide:check-circle" class="w-4 h-4" style="color: #22c55e" />
            </div>
            <p class="text-xs text-white/60 mb-2">{{ achievement.description }}</p>
            <div v-if="!achievement.unlocked && achievement.progress !== undefined" class="space-y-1">
              <div class="flex items-center justify-between text-xs">
                <span class="text-white/60">Progress</span>
                <span class="text-white/80">
                  {{ achievement.progress }}/{{ achievement.maxProgress }}
                </span>
              </div>
              <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(43, 29, 58, 0.5)">
                <div
                  class="h-full rounded-full transition-all"
                  :style="{
                    width: `${progressPercentage(achievement)}%`,
                    background: 'linear-gradient(90deg, #7c3aed 0%, #8b5cf6 100%)'
                  }"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  achievements: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({
      gamesOwned: 0,
      gamesCompleted: 0,
      totalPlaytime: 0,
      friendsCount: 0,
      reviewsWritten: 0
    })
  }
})

const emit = defineEmits(['achievement-click'])

const filter = ref('all')

const s = computed(() => props.stats || {
  gamesOwned: 0,
  gamesCompleted: 0,
  totalPlaytime: 0,
  friendsCount: 0,
  reviewsWritten: 0
})

const generatedAchievements = computed(() => [
  {
    id: 1,
    title: 'First Steps',
    description: 'Complete your first game',
    category: 'gaming',
    icon: 'lucide:trophy',
    unlocked: s.value.gamesCompleted >= 1
  },
  {
    id: 2,
    title: 'Social Butterfly',
    description: 'Add 10 friends',
    category: 'social',
    icon: 'lucide:users',
    unlocked: s.value.friendsCount >= 10,
    progress: Math.min(s.value.friendsCount, 10),
    maxProgress: 10
  },
  {
    id: 3,
    title: 'Review Master',
    description: 'Write 20 reviews',
    category: 'gaming',
    icon: 'lucide:star',
    unlocked: s.value.reviewsWritten >= 20,
    progress: Math.min(s.value.reviewsWritten, 20),
    maxProgress: 20
  },
  {
    id: 4,
    title: '100 Hours Club',
    description: 'Reach 100 hours of total playtime',
    category: 'milestone',
    icon: 'lucide:zap',
    unlocked: s.value.totalPlaytime >= 100,
    progress: Math.min(s.value.totalPlaytime, 100),
    maxProgress: 100
  },
  {
    id: 5,
    title: 'Completionist',
    description: 'Complete 50 games',
    category: 'milestone',
    icon: 'lucide:target',
    unlocked: s.value.gamesCompleted >= 50,
    progress: Math.min(s.value.gamesCompleted, 50),
    maxProgress: 50
  }
])

const achievements = computed(() => props.achievements.length > 0 ? props.achievements : generatedAchievements.value)

const filteredAchievements = computed(() => {
  if (filter.value === 'all') return achievements.value
  return achievements.value.filter(a => a.category === filter.value)
})

const unlockedCount = computed(() => achievements.value.filter(a => a.unlocked).length)
const totalCount = computed(() => achievements.value.length)

const progressPercentage = (achievement) => {
  if (!achievement.progress || !achievement.maxProgress) return 0
  return (achievement.progress / achievement.maxProgress) * 100
}
</script>

