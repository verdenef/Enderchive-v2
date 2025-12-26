<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <Icon name="lucide:award" class="w-6 h-6" style="color: #f59e0b" />
        <div>
          <h3>Game Achievements</h3>
          <p class="text-sm text-white/60">
            {{ loading ? 'Loading...' : `${completedAchievements.length} / ${availableAchievements.length} unlocked` }}
          </p>
        </div>
      </div>
      <GameButton
        v-if="!isGuest"
        variant="primary"
        size="sm"
        @click="showAddModal = true"
        :disabled="loading || incompleteAchievements.length === 0"
      >
        <template #icon>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Complete Achievement
      </GameButton>
    </div>

    <!-- Completed Achievements List -->
    <div v-if="loading" class="text-center py-8 mb-6">
      <p class="text-white/60 text-sm">Loading achievements...</p>
    </div>
    <div v-else-if="completedAchievements.length > 0" class="space-y-3 mb-6">
      <div
        v-for="progress in completedAchievements"
        :key="progress.user_game_progress_id"
        class="p-4 rounded-lg border-2"
        style="background: rgba(34, 197, 94, 0.05); border-color: rgba(34, 197, 94, 0.5); box-shadow: 0 0 15px rgba(34, 197, 94, 0.2)"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
              <Icon name="lucide:check-circle" class="w-5 h-5" style="color: #22c55e" />
              <h4 class="font-semibold">{{ progress.milestone?.name || 'Unknown Achievement' }}</h4>
              <span v-if="progress.milestone?.points" class="text-xs px-2 py-1 rounded" style="background: rgba(34, 197, 94, 0.2); color: #22c55e">
                {{ progress.milestone.points }} pts
              </span>
            </div>
            <p v-if="progress.milestone?.description" class="text-sm text-white/60 mb-2">{{ progress.milestone.description }}</p>
            <p v-if="progress.notes" class="text-sm text-white/50 mb-2 italic">"{{ progress.notes }}"</p>
            <div class="flex items-center gap-4 text-xs text-white/40">
              <span>Unlocked: {{ formatDate(progress.achieved_at) }}</span>
              <a
                v-if="progress.evidence_url"
                :href="progress.evidence_url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-[#8b5cf6] hover:text-[#a78bfa] flex items-center gap-1"
              >
                <Icon name="lucide:link" class="w-3 h-3" />
                View Proof
                <Icon name="lucide:external-link" class="w-3 h-3" />
              </a>
            </div>
          </div>
          <button
            @click="handleRemoveAchievement(progress.user_game_progress_id)"
            class="p-2 rounded hover:bg-white/10 transition-colors"
            title="Remove"
          >
            <Icon name="lucide:x" class="w-4 h-4" style="color: #ef4444" />
          </button>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8 mb-6" style="background: rgba(124, 58, 237, 0.05)">
      <Icon name="lucide:award" class="w-12 h-12 mx-auto mb-3 text-white/20" />
      <p class="text-white/60 text-sm">No achievements completed yet</p>
      <p class="text-white/40 text-xs mt-1">Complete your first achievement to track your progress!</p>
    </div>

    <!-- Available Achievements Reference -->
    <div v-if="!loading && incompleteAchievements.length > 0" class="mt-6 pt-6 border-t" style="border-color: #2b1d3a">
      <h3 class="text-sm font-semibold mb-3 text-white/80">Available Achievements ({{ incompleteAchievements.length }})</h3>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="achievement in incompleteAchievements.slice(0, 10)"
          :key="achievement.milestone_id"
          class="text-xs px-3 py-1.5 rounded-lg border-2 border-dashed"
          style="border-color: rgba(124, 58, 237, 0.3); background: rgba(124, 58, 237, 0.05); color: #a78bfa"
          :title="achievement.description || ''"
        >
          {{ achievement.name }}
          <span v-if="achievement.points"> ({{ achievement.points }} pts)</span>
        </span>
        <span v-if="incompleteAchievements.length > 10" class="text-xs text-white/40">+{{ incompleteAchievements.length - 10 }} more...</span>
      </div>
    </div>

    <AddAchievementModal
      :is-open="showAddModal"
      :game-id="gameId"
      :available-achievements="availableAchievements"
      :completed-achievement-ids="completedAchievementIds"
      :loading="completingAchievement"
      @close="handleCloseModal"
      @submit="handleCompleteAchievement"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Icon from '../ui/Icon.vue'
import GameButton from '../buttons/GameButton.vue'
import AddAchievementModal from '../modals/AddAchievementModal.vue'

const page = usePage()
const isGuest = computed(() => !page.props.auth?.user)

const props = defineProps({
  gameId: {
    type: Number,
    required: true
  },
  availableAchievements: {
    type: Array,
    default: () => []
  },
  completedAchievements: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['achievement-completed', 'achievement-removed'])

const showAddModal = ref(false)
const completingAchievement = ref(false)

const completedAchievementIds = computed(() => {
  return new Set(props.completedAchievements.map(p => p.milestone_id))
})

const incompleteAchievements = computed(() => {
  return props.availableAchievements.filter(a => !completedAchievementIds.value.has(a.milestone_id))
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  } catch (e) {
    return dateString
  }
}

const handleCompleteAchievement = async (achievementData) => {
  completingAchievement.value = true
  emit('achievement-completed', achievementData)
  
  // Reset loading state after 5 seconds if parent doesn't reload
  setTimeout(() => {
    if (completingAchievement.value) {
      completingAchievement.value = false
      // Don't close modal on timeout in case user wants to retry
    }
  }, 5000)
}

const handleCloseModal = () => {
  showAddModal.value = false
  completingAchievement.value = false
}

const handleRemoveAchievement = (progressId) => {
  if (confirm('Are you sure you want to remove this achievement?')) {
    emit('achievement-removed', progressId)
  }
}

// Watch for new achievements added via router.reload()
watch(() => props.completedAchievements.length, (newCount, oldCount) => {
  if (oldCount !== undefined && newCount > oldCount && completingAchievement.value) {
    // A new achievement was added, reset loading and close modal
    completingAchievement.value = false
    showAddModal.value = false
  }
})

// Also watch for when modal closes to reset loading state
watch(() => showAddModal.value, (isOpen) => {
  if (!isOpen) {
    completingAchievement.value = false
  }
})
</script>
