<template>
  <AppLayout>
    <div class="min-h-screen relative">
      <FloatingParticles />

      <div class="relative z-10">
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto px-8 h-full flex items-center gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
              <Icon name="lucide:gamepad-2" class="w-7 h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
              <h1 
                class="text-brand text-[#22c55e]"
                style="font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em"
              >
                Enderchive
              </h1>
            </div>

            <nav class="hidden md:flex gap-4 flex-1 justify-center">
              <NavigationTab
                label="Home"
                :active="false"
                @click="router.visit('/home')"
              >
                <template #icon>
                  <Icon name="lucide:home" class="w-5 h-5" />
                </template>
              </NavigationTab>
              <NavigationTab
                label="Library"
                :active="false"
                @click="router.visit('/library')"
              >
                <template #icon>
                  <Icon name="lucide:library" class="w-5 h-5" />
                </template>
              </NavigationTab>
              <NavigationTab
                label="Wishlist"
                :active="false"
                @click="router.visit('/wishlist')"
              >
                <template #icon>
                  <Icon name="lucide:heart" class="w-5 h-5" />
                </template>
              </NavigationTab>
              <NavigationTab
                label="Friends"
                :active="false"
                @click="router.visit('/friends')"
              >
                <template #icon>
                  <Icon name="lucide:users" class="w-5 h-5" />
                </template>
              </NavigationTab>
              <NavigationTab
                label="Settings"
                :active="false"
                @click="router.visit('/settings')"
              >
                <template #icon>
                  <Icon name="lucide:settings" class="w-5 h-5" />
                </template>
              </NavigationTab>
            </nav>

            <div class="hidden md:flex items-center gap-4">
              <NotificationButton
                v-if="!isGuest"
                :notifications="notifications"
                @mark-as-read="handleMarkAsRead"
                @mark-all-as-read="handleMarkAllAsRead"
                @delete="handleDeleteNotification"
                @notification-click="handleNotificationClick"
              />
              <button
                @click="isGuest ? router.visit('/login') : router.visit('/profile')"
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-200 cursor-pointer group"
                style="background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.3);"
                @mouseenter="(e) => {
                  e.currentTarget.style.background = 'rgba(124, 58, 237, 0.3)';
                  e.currentTarget.style.boxShadow = '0 0 20px rgba(124, 58, 237, 0.4)';
                }"
                @mouseleave="(e) => {
                  e.currentTarget.style.background = 'rgba(124, 58, 237, 0.15)';
                  e.currentTarget.style.boxShadow = 'none';
                }"
              >
                <Icon name="lucide:user" class="w-4 h-4 transition-colors" style="color: #8b5cf6;" />
                <span class="text-xs md:text-sm whitespace-nowrap">
                  {{ isGuest ? 'Sign in' : (user?.name || 'Guest') }}
                </span>
              </button>
              <button
                v-if="isAdmin"
                @click="router.visit('/admin')"
                class="relative p-2 rounded-lg transition-all duration-200 cursor-pointer group"
                style="background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.3)"
                @mouseenter="(e) => {
                  e.currentTarget.style.background = 'rgba(124, 58, 237, 0.3)';
                  e.currentTarget.style.boxShadow = '0 0 20px rgba(124, 58, 237, 0.4)';
                }"
                @mouseleave="(e) => {
                  e.currentTarget.style.background = 'rgba(124, 58, 237, 0.15)';
                  e.currentTarget.style.boxShadow = 'none';
                }"
              >
                <Icon name="lucide:shield" class="w-5 h-5 transition-colors" style="color: white" />
              </button>
            </div>

            <MobileNav
              current-page="details"
              :username="user?.name || 'Guest'"
              :is-admin="isAdmin"
            />
          </div>
        </header>

        <main v-if="loading" class="max-w-[1120px] mx-auto px-8 pt-8 pb-16">
          <div class="text-center py-12 text-white/60">Loading game details...</div>
        </main>

        <main v-else-if="game" class="max-w-[1120px] mx-auto px-8 pt-8 pb-16">
          <div class="mb-6">
            <div style="display: inline-block; overflow: hidden; border-radius: 12px; padding: 2px 0 4px 0;">
              <GameButton 
                variant="secondary"
                size="sm"
                @click="router.visit('/home')"
              >
                <template #icon>
                  <Icon name="lucide:arrow-left" class="w-4 h-4" />
                </template>
                Back
              </GameButton>
            </div>
          </div>

          <div 
            class="glass-panel rounded-2xl overflow-hidden border-4 border-[#2b1d3a]"
            style="box-shadow: 0 8px 40px rgba(124, 58, 237, 0.3);"
          >
            <div class="relative w-full overflow-hidden" style="aspect-ratio: 16/7;">
              <ImageWithFallback
                :src="game.cover_image || 'https://via.placeholder.com/800x350?text=No+Image'"
                :alt="game.title"
                class="w-full h-full object-cover"
              />
              <div 
                class="absolute inset-0"
                style="background: linear-gradient(to top, rgba(10,0,21,0.95) 0%, transparent 60%);"
              />
            </div>

            <div class="p-8">
              <div class="mb-6">
                <h1 
                  class="text-[#22c55e] mb-2"
                  style="font-size: 28px; text-shadow: 0 0 20px rgba(34, 197, 94, 0.3);"
                >
                  {{ game.title }}
                </h1>
                <p class="text-white/60 text-sm">
                  {{ formatDate(game.release_date) }} • {{ game.genre?.name || 'Unknown Genre' }}
                </p>
              </div>

              <div class="mb-6">
                <div class="space-y-4">
                  <p 
                    v-for="(paragraph, index) in description.split('\n\n')"
                    :key="index"
                    class="text-white/80 leading-relaxed"
                    style="line-height: 1.6;"
                  >
                    {{ paragraph }}
                  </p>
                </div>
                <button
                  v-if="shouldTruncate"
                  @click="isDescriptionExpanded = !isDescriptionExpanded"
                  class="mt-3 text-[#8b5cf6] hover:text-[#a78bfa] transition-colors text-sm font-medium cursor-pointer"
                  style="text-decoration: underline; text-underline-offset: 2px;"
                >
                  {{ isDescriptionExpanded ? 'See less' : 'See more' }}
                </button>
              </div>

              <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 space-y-6">
                  <ProgressTracking
                    :completion-percentage="completionPercentage"
                    :playtime-hours="playtimeHours"
                    :achievement-count="achievementCount"
                    :total-achievements="totalAchievements"
                    :on-edit-playtime="() => showEditPlaytime = true"
                  />

                  <GameJournal
                    :game-id="userGame?.user_game_id || game.game_id"
                    :entries="journalEntries"
                    @add-entry="handleAddJournalEntry"
                    @update-entry="handleUpdateJournalEntry"
                    @delete-entry="handleDeleteJournalEntry"
                  />

                  <GameOwnershipDetails
                    :ownerships="ownerships"
                    @add-ownership="showAddOwnership = true"
                    @edit-ownership="handleEditOwnership"
                    @remove-ownership="handleRemoveOwnership"
                  />
                </div>

                <div class="space-y-4">
                  <InfoCard
                    label="Rating"
                    :value="averageRating"
                    :is-rating="true"
                  />
                  <InfoCard label="Genres" :value="game.genre?.name || 'Unknown'" />
                  <InfoCard label="Released" :value="formatDate(game.release_date)" />
                  <InfoCard 
                    label="Platforms" 
                    :value="game.platforms?.map(p => p.name).join(', ') || 'PC'" 
                  />

                  <div class="space-y-4">
                    <GameButton 
                      :variant="isInWishlist ? 'green-accent' : 'secondary'"
                      :full-width="true"
                      @click="handleToggleWishlist"
                    >
                      <template #icon>
                        <Icon 
                          name="lucide:heart"
                          :class="['w-5 h-5 transition-colors', isInWishlist ? 'fill-current' : '']"
                          :style="isInWishlist ? { color: '#ef4444', filter: 'drop-shadow(0 0 8px rgba(239, 68, 68, 0.6))' } : {}"
                        />
                      </template>
                      {{ isGuest ? 'Sign in to Add to Wishlist' : (isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist') }}
                    </GameButton>
                    <GameButton 
                      :variant="isInLibrary ? 'green-accent' : 'secondary'"
                      :full-width="true"
                      @click="handleAddToLibrary"
                    >
                      <template #icon>
                        <Icon name="lucide:library" class="w-5 h-5" />
                      </template>
                      {{ isGuest ? 'Sign in to Add to Library' : (isInLibrary ? 'Remove from Library' : 'Add to Library') }}
                    </GameButton>
                  </div>
                </div>
              </div>
            </div>

            <div 
              class="mx-8 my-8 border-t"
              style="border-color: rgba(94, 0, 160, 0.25);"
            />

            <div class="px-8 pb-8">
              <GameAchievementsSection
                :game-id="game.game_id"
                :available-achievements="milestones"
                :completed-achievements="userGame?.progress || []"
                :loading="loading"
                @achievement-completed="handleCompleteAchievement"
                @achievement-removed="handleRemoveAchievement"
              />
            </div>

            <div class="px-8 pb-8">
              <ProofGallery 
                :game-id="game.game_id"
                :game-title="game.title"
                :proofs="proofs"
              />
            </div>

            <div class="px-8 pb-8">
              <ReviewsSection
                :reviews="formattedReviews"
                :has-user-review="!!myReview"
                @write-review="showWriteReview = true"
              />
            </div>
          </div>
        </main>

        <footer 
          class="border-t px-8 py-6 mt-16"
          style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-top: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
              <Icon name="lucide:gamepad-2" class="w-5 h-5 text-[#22c55e]" />
              <span 
                class="text-brand text-[#22c55e]"
                style="font-size: 10px;"
              >
                Enderchive
              </span>
            </div>
            <div class="h-4" />
          </div>
        </footer>
      </div>

      <WriteReviewModal
        :is-open="showWriteReview"
        :game-title="game?.title || ''"
        :game-cover="game?.cover_image || ''"
        :existing-review="myReview ? { rating: myReview.rating || 0, review_text: myReview.review_text || '' } : null"
        @close="showWriteReview = false"
        @submit="handleWriteReview"
      />

      <AddOwnershipModal
        :is-open="showAddOwnership"
        :game-id="game?.game_id"
        :game-title="game?.title || ''"
        :game-cover="game?.cover_image || ''"
        :editing-ownership="editingOwnership"
        @close="showAddOwnership = false; editingOwnership = null"
        @submit="handleOwnershipSubmit"
      />

      <EditPlaytimeModal
        :is-open="showEditPlaytime"
        :game-title="game?.title || ''"
        :current-playtime="playtimeHours"
        :game-cover="game?.cover_image || ''"
        @close="showEditPlaytime = false"
        @submit="handlePlaytimeUpdate"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'
import FloatingParticles from '../../Components/effects/FloatingParticles.vue'
import NavigationTab from '../../Components/navigation/NavigationTab.vue'
import MobileNav from '../../Components/navigation/MobileNav.vue'
import NotificationButton from '../../Components/ui/NotificationButton.vue'
import GameButton from '../../Components/buttons/GameButton.vue'
import ImageWithFallback from '../../Components/ui/ImageWithFallback.vue'
import InfoCard from '../../Components/cards/InfoCard.vue'
import ProgressTracking from '../../Components/game/ProgressTracking.vue'
import GameJournal from '../../Components/game/GameJournal.vue'
import GameOwnershipDetails from '../../Components/game/GameOwnershipDetails.vue'
import GameAchievementsSection from '../../Components/game/GameAchievementsSection.vue'
import ProofGallery from '../../Components/game/ProofGallery.vue'
import ReviewsSection from '../../Components/game/ReviewsSection.vue'
import WriteReviewModal from '../../Components/modals/WriteReviewModal.vue'
import AddOwnershipModal from '../../Components/modals/AddOwnershipModal.vue'
import EditPlaytimeModal from '../../Components/modals/EditPlaytimeModal.vue'
import Icon from '../../Components/ui/Icon.vue'
import { useToast } from '../../Composables/useToast'
import axios from 'axios'

const page = usePage()
const toast = useToast()

const props = defineProps({
  game: {
    type: Object,
    required: true
  },
  userGame: {
    type: Object,
    default: null
  },
  reviews: {
    type: Array,
    default: () => []
  },
  milestones: {
    type: Array,
    default: () => []
  }
})

const user = computed(() => page.props.auth?.user)
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)

const game = ref(props.game)
const userGame = ref(props.userGame)
const reviews = ref(props.reviews || [])
const milestones = ref(props.milestones || [])
const ownerships = ref([])
const journalEntries = ref([])
const playtimeHours = ref(userGame.value?.playtime_hours || 0)
const loading = ref(false)
const isDescriptionExpanded = ref(false)
const showWriteReview = ref(false)
const showAddOwnership = ref(false)
const showEditPlaytime = ref(false)
const editingOwnership = ref(null)
const notifications = ref([])

const DESCRIPTION_LIMIT = 500

const fullDescription = computed(() => game.value?.description || `Experience an epic adventure in ${game.value?.title || 'this game'}.`)
const shouldTruncate = computed(() => fullDescription.value.length > DESCRIPTION_LIMIT)
const description = computed(() => {
  if (!shouldTruncate.value || isDescriptionExpanded.value) {
    return fullDescription.value
  }
  const truncated = fullDescription.value.substring(0, DESCRIPTION_LIMIT)
  const lastSpace = truncated.lastIndexOf(' ')
  const cutPoint = lastSpace > DESCRIPTION_LIMIT * 0.8 ? lastSpace : DESCRIPTION_LIMIT
  return truncated.substring(0, cutPoint) + '...'
})

const achievementCount = computed(() => userGame.value?.progress?.length || 0)
const totalAchievements = computed(() => milestones.value.length)
const completionPercentage = computed(() => {
  if (totalAchievements.value === 0) return 0
  return Math.round((achievementCount.value / totalAchievements.value) * 100)
})

const isInLibrary = computed(() => !!userGame.value && userGame.value.status?.toLowerCase() !== 'wishlist')
const isInWishlist = computed(() => userGame.value?.status?.toLowerCase() === 'wishlist')

// Favorite state (reactive)
const isFavorite = ref(false)
const loadFavoriteState = () => {
  if (isGuest.value || !game.value) {
    isFavorite.value = false
    return
  }
  const favoritesKey = `favorites_${user.value?.id || 'guest'}`
  const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
  isFavorite.value = favorites.includes(game.value.game_id)
}

const myReview = computed(() => {
  if (isGuest.value) return null
  return reviews.value.find(r => r.user_id === user.value?.id) || null
})

const averageRating = computed(() => {
  if (reviews.value.length === 0) return 'No reviews yet'
  const ratings = reviews.value.filter(r => typeof r.rating === 'number').map(r => r.rating)
  if (ratings.length === 0) return 'No reviews yet'
  return (ratings.reduce((a, b) => a + b, 0) / ratings.length).toFixed(1)
})

const formattedReviews = computed(() => {
  return reviews.value.map(r => ({
    id: r.review_id,
    username: r.user?.name || 'Unknown',
    rating: r.rating || 0,
    review_text: r.review_text || '',
    created_at: r.created_at,
  }))
})

const formattedAchievements = computed(() => {
  const completedIds = new Set((userGame.value?.progress || []).map(p => p.milestone_id))
  return milestones.value.map(m => ({
    id: m.milestone_id,
    name: m.name,
    description: m.description || '',
    completed: completedIds.has(m.milestone_id)
  }))
})

const proofs = computed(() => {
  return (userGame.value?.progress || [])
    .filter(p => p.evidence_url)
    .map(p => ({
      id: p.id,
      type: p.evidence_url?.match(/\.(jpg|jpeg|png|gif|webp)/i) ? 'screenshot' : 'link',
      url: p.evidence_url || '',
      gameId: game.value.game_id,
      gameTitle: game.value.title,
      achievementId: p.milestone_id,
      achievementTitle: milestones.value.find(m => m.milestone_id === p.milestone_id)?.name || 'Unknown',
      verified: true,
      uploadedAt: p.achieved_at,
      notes: p.notes || undefined,
    }))
})

onMounted(() => {
  if (props.game) {
    game.value = props.game
  }
  if (props.userGame) {
    userGame.value = props.userGame
    playtimeHours.value = props.userGame.playtime_hours || 0
  }
  if (props.reviews) {
    reviews.value = props.reviews
  }
  if (props.milestones) {
    milestones.value = props.milestones
  }
  
  loadFavoriteState()
  loadOwnerships()
  
  // Load journal from localStorage (if still using localStorage for journal)
  try {
    const storedJournal = localStorage.getItem(`game:${game.value.game_id}:journal`)
    if (storedJournal) {
      journalEntries.value = JSON.parse(storedJournal)
    }
  } catch (e) {
    console.warn('Failed to load from localStorage', e)
  }
})

const loadOwnerships = async () => {
  if (!user.value || !game.value?.game_id) return
  
  try {
    const token = localStorage.getItem('api_token')
    const response = await axios.get(`/api/user/games/${game.value.game_id}/ownerships`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    ownerships.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load ownerships:', error)
    ownerships.value = []
  }
}

const handleAddToLibrary = async () => {
  if (isGuest.value) {
    router.visit('/login')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    if (isInLibrary.value && userGame.value) {
      await axios.delete(`/api/user/games/${userGame.value.user_game_id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      userGame.value = null
      toast.success('Removed', 'Game removed from library')
    } else {
      // Backend automatically removes from wishlist if it was there
      const response = await axios.post('/api/user/games', {
        game_id: game.value.game_id,
        status: 'Playing',
        playtime_hours: 0
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      toast.success('Added', response.data.message || 'Game added to library!')
    }
    router.reload({ only: ['userGame'] })
  } catch (error) {
    // If game already exists, backend might have moved it from wishlist
    if (error.response?.status === 409 || error.response?.status === 200) {
      toast.success('Success', 'Game moved to library!')
      router.reload({ only: ['userGame'] })
    } else {
      toast.error('Error', error.response?.data?.message || 'Failed to update library')
    }
  }
}

const handleToggleFavorite = async () => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to favorite games')
    return
  }
  
  try {
    const favoritesKey = `favorites_${user.value?.id || 'guest'}`
    const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
    const gameId = game.value.game_id
    const wasFavorite = favorites.includes(gameId)
    
    if (wasFavorite) {
      // Remove from favorites
      const updatedFavorites = favorites.filter(id => id !== gameId)
      localStorage.setItem(favoritesKey, JSON.stringify(updatedFavorites))
      isFavorite.value = false // Update reactive state immediately
      toast.success('Removed', 'Game removed from favorites')
    } else {
      // Add to favorites
      favorites.push(gameId)
      localStorage.setItem(favoritesKey, JSON.stringify(favorites))
      isFavorite.value = true // Update reactive state immediately
      toast.success('Added', 'Game added to favorites!')
      
      // If game is not in library, add it first (IT109 logic)
      if (!isInLibrary.value) {
        try {
          const token = localStorage.getItem('api_token')
          await axios.post('/api/user/games', {
            game_id: gameId,
            status: 'Playing',
            playtime_hours: 0
          }, {
            headers: { Authorization: `Bearer ${token}` }
          })
          router.reload({ only: ['userGame'] })
        } catch (error) {
          // Game might already be in library, that's okay
        }
      }
    }
  } catch (error) {
    toast.error('Error', 'Failed to update favorite status')
  }
}

const handleCompleteAchievement = async (achievementData) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to complete achievements')
    return
  }
  
  if (!userGame.value) {
    toast.error('Error', 'Please add this game to your library first')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    const response = await axios.post(
      `/api/user/games/${game.value.game_id}/progress/${achievementData.milestone_id}`,
      {
        notes: achievementData.notes,
        evidence_url: achievementData.evidence_url
      },
      { headers }
    )
    
    // Add the new progress to userGame immediately
    if (!userGame.value.progress) {
      userGame.value.progress = []
    }
    userGame.value.progress = [...userGame.value.progress, response.data.data]
    
    toast.success('Success', 'Achievement completed successfully!')
    // Reload to sync with backend
    await router.reload({ only: ['userGame'] })
  } catch (error) {
    if (error.response?.status === 409) {
      toast.error('Error', 'You have already completed this achievement')
    } else {
      toast.error('Error', error.response?.data?.message || 'Failed to complete achievement')
    }
    // Emit error event to reset loading state
    throw error
  }
}

const handleRemoveAchievement = async (progressId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to manage achievements')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    // Find the progress to get milestone_id
    const progress = userGame.value?.progress?.find(p => p.user_game_progress_id === progressId)
    if (!progress) {
      toast.error('Error', 'Achievement not found')
      return
    }
    
    await axios.delete(
      `/api/user/games/${game.value.game_id}/progress/${progress.milestone_id}`,
      { headers }
    )
    
    // Remove from userGame immediately
    if (userGame.value?.progress) {
      userGame.value.progress = userGame.value.progress.filter(p => p.user_game_progress_id !== progressId)
    }
    
    toast.success('Success', 'Achievement removed')
    // Reload to sync with backend
    router.reload({ only: ['userGame'] })
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to remove achievement')
  }
}

const handleToggleWishlist = async () => {
  if (isGuest.value) {
    router.visit('/login')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    if (isInWishlist.value && userGame.value) {
      await axios.delete(`/api/user/games/${userGame.value.user_game_id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      userGame.value = null
      toast.success('Removed', 'Game removed from wishlist')
    } else {
      await axios.post('/api/user/games', {
        game_id: game.value.game_id,
        status: 'Wishlist',
        playtime_hours: 0
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      toast.success('Added', 'Game added to wishlist!')
    }
    router.reload({ only: ['userGame'] })
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to update wishlist')
  }
}

const handleWriteReview = async (payload) => {
  try {
    const token = localStorage.getItem('api_token')
    if (myReview.value) {
      await axios.put(`/api/reviews/${myReview.value.review_id}`, payload, {
        headers: { Authorization: `Bearer ${token}` }
      })
      toast.success('Success', 'Review updated!')
    } else {
      await axios.post(`/api/games/${game.value.game_id}/reviews`, payload, {
        headers: { Authorization: `Bearer ${token}` }
      })
      toast.success('Success', 'Review submitted!')
    }
    showWriteReview.value = false
    router.reload({ only: ['reviews'] })
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to submit review')
  }
}

const handleOwnershipSubmit = async (ownershipData) => {
  if (!userGame.value || !game.value?.game_id) {
    toast.error('Error', 'Please add this game to your library first')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    const response = await axios.post(
      `/api/user/games/${game.value.game_id}/ownerships`,
      ownershipData,
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )
    
    await loadOwnerships()
    toast.success('Success', 'Copy saved!')
    showAddOwnership.value = false
    editingOwnership.value = null
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to save copy')
  }
}

const handleEditOwnership = (ownership) => {
  editingOwnership.value = ownership
  showAddOwnership.value = true
}

const handleRemoveOwnership = async (ownershipId) => {
  if (!game.value?.game_id) return
  
  try {
    const token = localStorage.getItem('api_token')
    await axios.delete(
      `/api/user/games/${game.value.game_id}/ownerships/${ownershipId}`,
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )
    
    await loadOwnerships()
    toast.success('Removed', 'Copy removed')
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to remove copy')
  }
}

const handlePlaytimeUpdate = async (edit) => {
  if (!userGame.value) return
  
  try {
    const token = localStorage.getItem('api_token')
    playtimeHours.value = edit.playtimeHours
    await axios.put(`/api/user/games/${userGame.value.user_game_id}`, {
      playtime_hours: edit.playtimeHours,
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    toast.success('Updated', 'Playtime updated!')
    showEditPlaytime.value = false
    router.reload({ only: ['userGame'] })
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to update playtime')
  }
}

const handleAddJournalEntry = async (entry) => {
  if (!userGame.value) return
  
  try {
    const newEntry = {
      ...entry,
      id: Date.now(),
      createdAt: new Date().toISOString(),
    }
    journalEntries.value = [...journalEntries.value, newEntry]
    localStorage.setItem(`game:${game.value.game_id}:journal`, JSON.stringify(journalEntries.value))
    toast.success('Added', 'Journal entry added!')
  } catch (error) {
    toast.error('Error', 'Failed to add journal entry')
  }
}

const handleUpdateJournalEntry = async (id, entry) => {
  if (!userGame.value) return
  
  try {
    journalEntries.value = journalEntries.value.map(e => 
      e.id === id ? { ...e, ...entry } : e
    )
    localStorage.setItem(`game:${game.value.game_id}:journal`, JSON.stringify(journalEntries.value))
    toast.success('Updated', 'Journal entry updated!')
  } catch (error) {
    toast.error('Error', 'Failed to update journal entry')
  }
}

const handleDeleteJournalEntry = async (id) => {
  if (!userGame.value) return
  
  try {
    journalEntries.value = journalEntries.value.filter(e => e.id !== id)
    localStorage.setItem(`game:${game.value.game_id}:journal`, JSON.stringify(journalEntries.value))
    toast.success('Deleted', 'Journal entry deleted')
  } catch (error) {
    toast.error('Error', 'Failed to delete journal entry')
  }
}


const formatDate = (date) => {
  if (!date) return 'TBA'
  return new Date(date).toLocaleDateString()
}

const handleMarkAsRead = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value[index].read = true
  }
}

const handleMarkAllAsRead = () => {
  notifications.value.forEach(n => n.read = true)
}

const handleDeleteNotification = (id) => {
  notifications.value = notifications.value.filter(n => n.id !== id)
}

const handleNotificationClick = (notification) => {
  // Mark as read
  handleMarkAsRead(notification.id)
  
  // Navigate to the action URL if provided
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}
</script>

