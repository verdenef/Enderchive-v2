<template>
  <div class="space-y-4">
    <div class="mb-6 pb-4 border-b border-[#2b1d3a]">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Icon name="lucide:star" class="w-6 h-6" style="color: #f59e0b" />
          <div>
            <h2 class="text-white font-semibold mb-1">Reviews</h2>
            <p class="text-sm text-white/60">
              {{ reviews.length }} {{ reviews.length === 1 ? 'review' : 'reviews' }}
            </p>
          </div>
        </div>
        <GameButton variant="primary" @click="onWriteReview">
          <template #icon>
            <Icon name="lucide:edit-3" class="w-4 h-4" />
          </template>
          {{ hasUserReview ? 'Edit Review' : 'Write Review' }}
        </GameButton>
      </div>
    </div>
    <div v-if="sortedReviews.length > 0" class="space-y-3">
      <ReviewCard
        v-for="review in sortedReviews"
        :key="review.id"
        :username="review.username"
        :rating="review.rating"
        :review-text="review.review_text"
        :date="review.created_at"
      />
    </div>
    <div v-else class="text-center py-8">
      <Icon name="lucide:star" class="w-12 h-12 mx-auto mb-3 text-white/20" />
      <p class="text-white/60 text-sm">No reviews yet</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'
import ReviewCard from '../cards/ReviewCard.vue'

const props = defineProps({
  reviews: {
    type: Array,
    default: () => []
  },
  onWriteReview: {
    type: Function,
    default: undefined
  },
  hasUserReview: {
    type: Boolean,
    default: false
  }
})

const sortedReviews = computed(() => {
  // Sort by date (newest first) since we removed voting
  return [...props.reviews].sort((a, b) => {
    return new Date(b.created_at) - new Date(a.created_at)
  })
})
</script>

