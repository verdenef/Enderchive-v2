<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen">
        <div
          class="fixed inset-0 z-[9998]"
          style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
          @click="handleClose"
        />
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            class="w-full max-w-2xl rounded-xl max-h-[80vh] overflow-hidden flex flex-col"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="p-6 border-b border-[#2b1d3a]">
              <div class="flex items-center justify-between mb-4">
                <h3>{{ selectedAchievement ? 'Complete Achievement' : 'Select Achievement' }}</h3>
                <button
                  @click="handleClose"
                  class="p-2 hover:bg-white/10 rounded transition-colors"
                >
                  <Icon name="lucide:x" class="w-5 h-5 text-gray-400 hover:text-white" />
                </button>
              </div>

              <!-- Search bar -->
              <div v-if="!selectedAchievement" class="relative">
                <Icon name="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40" />
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search achievements..."
                  class="w-full pl-10 pr-4 py-2 rounded-lg text-sm"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                />
              </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
              <div v-if="!selectedAchievement">
                <!-- Achievement selection list -->
                <div v-if="filteredAchievements.length > 0" class="space-y-3">
                  <button
                    v-for="achievement in filteredAchievements"
                    :key="achievement.milestone_id"
                    @click="handleSelectAchievement(achievement)"
                    class="w-full text-left p-4 rounded-lg border-2 transition-all hover:border-[#8b5cf6] hover:bg-white/5"
                    style="background: rgba(124, 58, 237, 0.05); border-color: rgba(124, 58, 237, 0.3)"
                  >
                    <div class="flex items-start justify-between gap-3">
                      <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                          <Icon name="lucide:award" class="w-4 h-4" style="color: #f59e0b" />
                          <h4 class="font-semibold text-sm">{{ achievement.name }}</h4>
                          <span v-if="achievement.points" class="text-xs" style="color: #22c55e">+{{ achievement.points }} pts</span>
                        </div>
                        <p v-if="achievement.description" class="text-xs text-white/60">{{ achievement.description }}</p>
                        <span v-if="!achievement.is_optional" class="text-xs text-[#8b5cf6] mt-1 inline-block">Required</span>
                      </div>
                    </div>
                  </button>
                </div>
                <div v-else class="text-center py-12">
                  <Icon name="lucide:award" class="w-12 h-12 mx-auto mb-3 text-white/20" />
                  <p class="text-white/60 text-sm">
                    {{ searchQuery ? 'No achievements match your search' : 'All achievements completed!' }}
                  </p>
                </div>
              </div>
              <div v-else>
                <!-- Achievement completion form -->
                <div class="space-y-4">
                  <!-- Selected achievement info -->
                  <div
                    class="p-4 rounded-lg border-2"
                    style="background: rgba(124, 58, 237, 0.1); border-color: rgba(124, 58, 237, 0.5)"
                  >
                    <div class="flex items-center gap-2 mb-2">
                      <Icon name="lucide:award" class="w-5 h-5" style="color: #f59e0b" />
                      <h4 class="font-semibold">{{ selectedAchievement.name }}</h4>
                      <span v-if="selectedAchievement.points" class="text-xs" style="color: #22c55e">+{{ selectedAchievement.points }} pts</span>
                    </div>
                    <p v-if="selectedAchievement.description" class="text-sm text-white/70">{{ selectedAchievement.description }}</p>
                  </div>

                  <!-- Notes input -->
                  <div>
                    <label class="block text-sm mb-2">Notes (optional)</label>
                    <textarea
                      v-model="notes"
                      placeholder="Add any notes about completing this achievement..."
                      class="w-full px-4 py-2 rounded-lg text-sm resize-vertical"
                      style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; min-height: 80px"
                      maxlength="500"
                    />
                    <p class="text-xs text-white/40 mt-1">{{ notes.length }}/500 characters</p>
                  </div>

                  <!-- Proof URL input -->
                  <div>
                    <label class="block text-sm mb-2">Proof Link (optional)</label>
                    <input
                      v-model="proofUrl"
                      type="url"
                      placeholder="https://example.com/proof..."
                      class="w-full px-4 py-3 rounded-lg text-sm"
                      style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                    />
                    <p class="text-xs text-white/40 mt-1">
                      Link to screenshot, video, or profile showing you completed this achievement
                    </p>
                  </div>

                  <!-- Action buttons -->
                  <div class="flex gap-3 pt-2">
                    <GameButton
                      variant="purple-outline"
                      :full-width="true"
                      @click="handleBack"
                      :disabled="loading"
                    >
                      Back
                    </GameButton>
                    <GameButton
                      variant="primary"
                      :full-width="true"
                      @click="handleSubmit"
                      :disabled="loading"
                    >
                      <template v-if="loading">
                        <Icon name="lucide:loader-2" class="w-4 h-4 animate-spin mr-2" />
                        Completing...
                      </template>
                      <template v-else>
                        Complete Achievement
                      </template>
                    </GameButton>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  gameId: {
    type: Number,
    required: true
  },
  availableAchievements: {
    type: Array,
    default: () => []
  },
  completedAchievementIds: {
    type: Set,
    default: () => new Set()
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'submit'])

const searchQuery = ref('')
const selectedAchievement = ref(null)
const proofUrl = ref('')
const notes = ref('')

// Filter out completed achievements
const incompleteAchievements = computed(() => {
  return props.availableAchievements.filter(a => !props.completedAchievementIds.has(a.milestone_id))
})

const filteredAchievements = computed(() => {
  if (!searchQuery.value.trim()) return incompleteAchievements.value
  const query = searchQuery.value.toLowerCase()
  return incompleteAchievements.value.filter(a => 
    a.name.toLowerCase().includes(query) ||
    (a.description && a.description.toLowerCase().includes(query))
  )
})

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    // Reset form when opening
    selectedAchievement.value = null
    searchQuery.value = ''
    proofUrl.value = ''
    notes.value = ''
  }
})

const handleSelectAchievement = (achievement) => {
  selectedAchievement.value = achievement
  proofUrl.value = ''
  notes.value = ''
}

const handleBack = () => {
  selectedAchievement.value = null
  proofUrl.value = ''
  notes.value = ''
}

const handleSubmit = () => {
  if (!selectedAchievement.value) return
  
  // Validate URL if provided
  let validEvidenceUrl = null
  if (proofUrl.value.trim()) {
    try {
      new URL(proofUrl.value.trim())
      validEvidenceUrl = proofUrl.value.trim()
    } catch (e) {
      alert('Please enter a valid URL for the proof (e.g., https://example.com/image.png)')
      return
    }
  }
  
  emit('submit', {
    milestone_id: selectedAchievement.value.milestone_id,
    notes: notes.value.trim() || null,
    evidence_url: validEvidenceUrl
  })
}

const handleClose = () => {
  selectedAchievement.value = null
  searchQuery.value = ''
  proofUrl.value = ''
  notes.value = ''
  emit('close')
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>

