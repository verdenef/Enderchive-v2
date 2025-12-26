<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen">
        <!-- Overlay -->
        <div
          class="fixed inset-0 z-[9998]"
          style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
          @click="$emit('close')"
        />

        <!-- Modal -->
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            class="w-full max-w-2xl rounded-xl max-h-[90vh] overflow-y-auto"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <!-- Header Section with Cover -->
            <div
              class="relative h-32 rounded-t-xl overflow-visible"
              style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.8) 0%, rgba(139, 92, 246, 0.8) 100%)"
            />

            <!-- Avatar - Positioned below header -->
            <div class="flex justify-center -mt-12 relative z-10">
              <div
                class="w-24 h-24 rounded-full flex items-center justify-center border-4 border-[#1a1a2e] overflow-hidden"
                :style="{
                  background: friend.avatar ? 'transparent' : 'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)',
                  boxShadow: '0 0 30px rgba(124, 58, 237, 0.5)'
                }"
              >
                <img v-if="friend.avatar" :src="friend.avatar" :alt="friend.name || friend.username" class="w-full h-full object-cover" />
                <span v-else class="text-3xl">{{ (friend.name || friend.username || 'U').charAt(0).toUpperCase() }}</span>
              </div>
            </div>

            <!-- Content -->
            <div class="pt-4 pb-6 px-6">
              <!-- Status Badge -->
              <div class="flex justify-center mb-4">
                <div
                  class="px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2"
                  :style="{
                    background: friend.status === 'Online' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(107, 114, 128, 0.2)',
                    border: `1px solid ${friend.status === 'Online' ? '#22c55e' : '#6b7280'}`,
                    color: friend.status === 'Online' ? '#22c55e' : '#9ca3af'
                  }"
                >
                  <div
                    class="w-2 h-2 rounded-full"
                    :style="{
                      background: friend.status === 'Online' ? '#22c55e' : '#6b7280',
                      boxShadow: friend.status === 'Online' ? '0 0 8px rgba(34, 197, 94, 0.6)' : 'none'
                    }"
                  />
                  {{ friend.status }}
                </div>
              </div>

              <!-- Display Name, Username and Bio -->
              <div class="text-center mb-6">
                <h2 class="mb-1">{{ friend.name || friend.username }}</h2>
                <p v-if="friend.name && friend.username && friend.name !== friend.username" class="text-sm text-white/60 mb-2">@{{ friend.username }}</p>
                <p v-if="friend.bio" class="text-sm text-white/60 max-w-md mx-auto mb-2">{{ friend.bio }}</p>
                <p v-if="friend.friendSince" class="text-xs text-white/40 mt-2">Friends since {{ friend.friendSince }}</p>
                <p v-else-if="friend.memberSince" class="text-xs text-white/40 mt-2">Member since {{ friend.memberSince }}</p>
              </div>

              <!-- Stats Row -->
              <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="text-center p-3 rounded-lg" style="background: rgba(124, 58, 237, 0.1)">
                  <Icon name="lucide:library" class="w-5 h-5 mx-auto mb-2" style="color: #8b5cf6" />
                  <div class="text-xl mb-1">{{ stats.gamesOwned }}</div>
                  <div class="text-xs text-gray-400">Games</div>
                </div>
                <div class="text-center p-3 rounded-lg" style="background: rgba(34, 197, 94, 0.1)">
                  <Icon name="lucide:star" class="w-5 h-5 mx-auto mb-2" style="color: #22c55e" />
                  <div class="text-xl mb-1">{{ stats.gamesCompleted }}</div>
                  <div class="text-xs text-gray-400">Completed</div>
                </div>
                <div class="text-center p-3 rounded-lg" style="background: rgba(139, 92, 246, 0.1)">
                  <Icon name="lucide:clock" class="w-5 h-5 mx-auto mb-2" style="color: #a78bfa" />
                  <div class="text-xl mb-1">{{ stats.totalPlaytime }}h</div>
                  <div class="text-xs text-gray-400">Playtime</div>
                </div>
                <div class="text-center p-3 rounded-lg" style="background: rgba(245, 158, 11, 0.1)">
                  <Icon name="lucide:message-square" class="w-5 h-5 mx-auto mb-2" style="color: #f59e0b" />
                  <div class="text-xl mb-1">{{ stats.reviewsWritten }}</div>
                  <div class="text-xs text-gray-400">Reviews</div>
                </div>
              </div>

              <div v-if="showFullProfile">
                <!-- Additional Highlights -->
                <div class="mb-6">
                  <div class="p-4 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
                    <h3 class="text-sm text-white/70 mb-2">Favorite Genres</h3>
                    <div v-if="favoriteGenres.length > 0" class="mt-3 flex flex-wrap gap-2">
                      <span
                        v-for="genre in favoriteGenres"
                        :key="genre"
                        class="px-2 py-1 rounded-full text-xs"
                        style="background: rgba(124, 58, 237, 0.2); border: 1px solid rgba(124, 58, 237, 0.4)"
                      >
                        {{ genre }}
                      </span>
                    </div>
                    <p v-else class="text-xs text-white/40">No genre data yet.</p>
                  </div>
                </div>

                <!-- Social Links -->
                <div v-if="socialLinks.length > 0" class="mb-6">
                  <SocialLinks
                    :links="socialLinks"
                    @update:links="() => {}"
                    :is-own-profile="false"
                    :viewer-is-friend="viewerIsFriend"
                  />
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-wrap gap-3 justify-center pt-4 border-t border-[#2b1d3a]">
                <GameButton
                  variant="primary"
                  size="sm"
                  @click="showFullProfile = !showFullProfile"
                >
                  {{ showFullProfile ? 'Hide Full Profile' : 'View Full Profile' }}
                </GameButton>
                <GameButton
                  v-if="isPendingRequest && $attrs.onCancelRequest"
                  variant="danger"
                  size="sm"
                  @click="$emit('cancel-request')"
                >
                  Cancel Request
                </GameButton>
                <GameButton
                  v-if="$attrs.onAddFriend && !viewerIsFriend && !isPendingRequest"
                  variant="secondary"
                  size="sm"
                  @click="$emit('add-friend')"
                >
                  Add Friend
                </GameButton>
                <GameButton
                  variant="outline"
                  size="sm"
                  @click="$emit('close')"
                >
                  Close
                </GameButton>
              </div>
            </div>

            <!-- Close Button -->
            <button
              @click="$emit('close')"
              class="absolute top-4 right-4 p-2 hover:bg-white/10 rounded transition-colors"
            >
              <Icon name="lucide:x" class="w-5 h-5 text-gray-400 hover:text-white" />
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue'
import Icon from '../ui/Icon.vue'
import GameButton from '../buttons/GameButton.vue'
import SocialLinks from '../profile/SocialLinks.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  friend: {
    type: Object,
    required: true
  },
  viewerIsFriend: {
    type: Boolean,
    default: false
  },
  isPendingRequest: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close', 'add-friend', 'cancel-request'])

const showFullProfile = ref(false)

const stats = computed(() => props.friend.stats || {
  gamesOwned: 0,
  gamesCompleted: 0,
  totalPlaytime: 0,
  reviewsWritten: 0
})

const socialLinks = computed(() => props.friend.socialLinks || [])
const favoriteGenres = computed(() => props.friend.favoriteGenres || [])
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

@keyframes modalIn {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
</style>



