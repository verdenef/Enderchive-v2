<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen">
        <div
          class="fixed inset-0 z-[9998]"
          style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
          @click="onClose"
        />
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            class="w-full max-w-2xl rounded-xl max-h-[90vh] overflow-y-auto"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="flex items-center justify-between p-6 border-b border-[#2b1d3a]">
              <h2>{{ isEditing ? 'Edit Your Review' : 'Write a Review' }}</h2>
              <button
                @click="onClose"
                class="p-2 hover:bg-white/10 rounded transition-colors"
              >
                <Icon name="lucide:x" class="w-5 h-5 text-gray-400 hover:text-white" />
              </button>
            </div>
            <div class="p-6 space-y-6">
              <div class="flex items-center gap-4 p-4 rounded-lg" style="background: rgba(124, 58, 237, 0.1)">
                <ImageWithFallback
                  :src="gameCover"
                  :alt="gameTitle"
                  class="w-16 h-24 object-cover rounded-lg"
                />
                <h3 class="text-lg">{{ gameTitle }}</h3>
              </div>
              <div>
                <label class="block text-sm mb-3">Your Rating</label>
                <div class="flex gap-2">
                  <button
                    v-for="heartValue in 5"
                    :key="heartValue"
                    type="button"
                    @click="rating = heartValue"
                    @mouseenter="hoverRating = heartValue"
                    @mouseleave="hoverRating = 0"
                    class="transition-all duration-200 hover:scale-110"
                  >
                    <Icon
                      name="lucide:heart"
                      class="w-10 h-10"
                      :class="heartValue <= (hoverRating || rating) ? 'fill-red-500' : ''"
                      :style="{
                        color: heartValue <= (hoverRating || rating) ? '#ef4444' : '#6b7280',
                        filter: heartValue <= (hoverRating || rating) ? 'drop-shadow(0 0 8px rgba(239, 68, 68, 0.6))' : 'none'
                      }"
                    />
                  </button>
                </div>
                <p v-if="rating > 0" class="text-sm text-gray-400 mt-2">
                  {{ rating === 1 ? 'Poor' : rating === 2 ? 'Fair' : rating === 3 ? 'Good' : rating === 4 ? 'Very Good' : 'Excellent' }}
                </p>
              </div>
              <div>
                <label class="block text-sm mb-3">Your Review</label>
                <textarea
                  v-model="reviewText"
                  placeholder="Share your thoughts about this game..."
                  class="w-full px-4 py-3 rounded-lg text-sm resize-vertical"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; min-height: 150px"
                  :maxlength="maxChars"
                />
                <div class="flex justify-between items-center mt-2">
                  <p class="text-xs text-gray-400">Minimum 10 characters</p>
                  <p class="text-xs text-gray-400">{{ reviewText.length }} / {{ maxChars }}</p>
                </div>
              </div>
              <div class="flex gap-4 justify-end pt-4">
                <GameButton variant="purple-outline" @click="onClose">Cancel</GameButton>
                <GameButton variant="primary" @click="handleSubmit">
                  {{ isEditing ? 'Update Review' : 'Submit Review' }}
                </GameButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'
import ImageWithFallback from '../ui/ImageWithFallback.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  gameTitle: {
    type: String,
    required: true
  },
  gameCover: {
    type: String,
    required: true
  },
  existingReview: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'submit'])

const maxChars = 1000
const rating = ref(0)
const hoverRating = ref(0)
const reviewText = ref('')

const isEditing = computed(() => !!props.existingReview)

watch(() => props.isOpen, (newVal) => {
  if (newVal && props.existingReview) {
    rating.value = props.existingReview.rating || 0
    reviewText.value = props.existingReview.review_text || ''
  } else if (newVal) {
    rating.value = 0
    reviewText.value = ''
  }
})

const handleSubmit = () => {
  if (rating.value === 0) {
    alert('Please select a rating')
    return
  }
  if (reviewText.value.trim().length < 10) {
    alert('Review must be at least 10 characters')
    return
  }
  emit('submit', {
    rating: rating.value,
    review_text: reviewText.value.trim() || null
  })
  onClose()
}

const onClose = () => {
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

