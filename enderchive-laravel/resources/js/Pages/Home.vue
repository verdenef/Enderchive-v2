<template>
  <AppLayout>
    <div class="min-h-screen relative">
      <div class="relative z-10">
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto px-4 md:px-8 h-full flex items-center gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
              <Icon name="lucide:gamepad-2" class="w-7 h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
              <h1 class="text-brand text-[#22c55e]" style="font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em">
                Enderchive
              </h1>
            </div>

            <nav class="hidden md:flex gap-4 flex-1 justify-center">
              <NavigationTab
                label="Home"
                :active="true"
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
                <Icon name="lucide:user" class="w-4 h-4 transition-colors" style="color: #8b5cf6" />
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
              current-page="home"
              :username="user?.name || 'Guest'"
              :is-admin="isAdmin"
            />
          </div>
        </header>

        <main class="max-w-[1280px] mx-auto px-4 md:px-8 pt-8 pb-16">
          <section class="mb-12">
            <h3 class="mb-4 text-brand">
              Welcome to <span class="text-[#22c55e]">Enderchive</span>
            </h3>
            <p class="mb-6">
              Track your game collection, share reviews with friends, and discover your next adventure. Build your ultimate gaming archive.
            </p>

            <div class="flex gap-3 max-w-3xl">
              <div class="flex-1">
                <GameInput
                  v-model="searchQuery"
                  placeholder="Search games..."
                  class="h-12"
                  @keyup.enter="handleSearch"
                />
              </div>
              <button
                type="button"
                @click.prevent="handleSearch"
                class="flex items-center justify-center transition-all duration-200 hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                style="background: transparent; border: none; color: white; height: 48px; width: 48px; padding: 0; margin-top: 1px;"
              >
                <Icon name="lucide:search" class="w-6 h-6" />
              </button>
            </div>
          </section>

          <section class="mb-12">
            <h3 class="text-[#22c55e] mb-6">Featured Games</h3>
            <div v-if="loading" class="text-center py-24">
              <p class="text-white/60">Loading games...</p>
            </div>
            <div v-else-if="games.length === 0" class="text-center py-24">
              <p class="text-white/80 mb-2">No games loaded</p>
              <p class="text-white/50 text-sm">Games will appear here</p>
            </div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              <GameItemCard
                v-for="game in games"
                :key="game.game_id"
                :title="game.title"
                :image="game.cover_image || 'https://via.placeholder.com/300x400?text=No+Image'"
                :date="game.release_date || 'TBA'"
                :genres="game.genre?.name || 'Unknown'"
                :is-in-library="libraryGameIds.has(game.game_id)"
                :is-favorite="isGameFavorite(game.game_id)"
                :is-wishlisted="wishlistGameIds.has(game.game_id)"
                @add="addToLibrary(game.game_id)"
                @favorite="toggleFavorite(game.game_id)"
                @wishlist="addToWishlist(game.game_id)"
                @click="navigateToGame(game.game_id)"
              />
            </div>
          </section>
        </main>

        <footer 
          class="border-t px-4 md:px-8 py-6"
          style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-top: 1px solid rgba(94,0,160,0.35)"
        >
          <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
              <Icon name="lucide:gamepad-2" class="w-5 h-5 text-[#22c55e]" />
              <span class="text-brand text-[#22c55e]" style="font-size: 10px">
                Enderchive
              </span>
            </div>
            <div class="h-4" />
          </div>
        </footer>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import FloatingParticles from '../Components/effects/FloatingParticles.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import GameItemCard from '../Components/cards/GameItemCard.vue'
import Icon from '../Components/ui/Icon.vue'
import { useToast } from '../Composables/useToast'
import { useNotifications } from '../Composables/useNotifications'
import axios from 'axios'

const page = usePage()
const toast = useToast()

const props = defineProps({
  games: {
    type: Array,
    default: () => []
  },
  userGames: {
    type: Array,
    default: () => []
  }
})

const user = computed(() => page.props.auth?.user)
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)

const searchQuery = ref('')
const games = ref(props.games || [])
const userGames = ref(props.userGames || [])
const loading = ref(false)
// Use global notifications
const { notifications, markAsRead: handleMarkAsRead, markAllAsRead: handleMarkAllAsRead, deleteNotification: handleDeleteNotification, loadNotifications, startNotificationPolling } = useNotifications(user)

const libraryGameIds = computed(() => {
  if (!userGames.value || !Array.isArray(userGames.value)) return new Set()
  // Only include games that are NOT in wishlist (actual library games)
  return new Set(userGames.value.filter(ug => ug.status !== 'Wishlist').map(ug => ug.game_id))
})

const wishlistGameIds = computed(() => {
  if (!userGames.value || !Array.isArray(userGames.value)) return new Set()
  return new Set(userGames.value.filter(ug => ug.status === 'Wishlist').map(ug => ug.game_id))
})

const favoriteGameIds = ref(new Set())
// Convert Set to array for better Vue reactivity tracking
const favoriteGameIdsArray = ref([])

// Update array whenever Set changes
watch(favoriteGameIds, (newSet) => {
  favoriteGameIdsArray.value = Array.from(newSet)
}, { immediate: true, deep: true })

// Check if game is favorited
const isGameFavorite = (gameId) => {
  return favoriteGameIdsArray.value.includes(Number(gameId))
}

// Load favorites on mount and watch for changes
const loadFavorites = () => {
  if (isGuest.value) {
    favoriteGameIds.value = new Set()
    return
  }
  try {
    const favoritesKey = `favorites_${user.value?.id || 'guest'}`
    const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
    // Normalize IDs to numbers for consistent comparison
    const normalizedFavs = favorites.map(id => Number(id))
    favoriteGameIds.value = new Set(normalizedFavs)
    favoriteGameIdsArray.value = normalizedFavs
  } catch (error) {
    console.error('Error loading favorites:', error)
    favoriteGameIds.value = new Set()
  }
}

// Watch for localStorage changes (for cross-tab sync)
const watchFavorites = () => {
  if (isGuest.value) return () => {} // Return empty cleanup function for guests
  const favoritesKey = `favorites_${user.value?.id || 'guest'}`
  const checkInterval = setInterval(() => {
    try {
      if (!favoriteGameIds.value) {
        favoriteGameIds.value = new Set()
      }
      const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
      // Normalize IDs to numbers for consistent comparison
      const currentSet = new Set(favorites.map(id => Number(id)))
      if (currentSet.size !== favoriteGameIds.value.size || 
          ![...currentSet].every(id => favoriteGameIds.value.has(id))) {
        favoriteGameIds.value = currentSet
      }
    } catch (error) {
      console.error('Error watching favorites:', error)
    }
  }, 100)
  
  // Clean up on unmount
  return () => clearInterval(checkInterval)
}

let favoritesWatcher = null

onMounted(() => {
  // Store API token from flash data if available (after login)
  if (page.props.flash?.api_token) {
    localStorage.setItem('api_token', page.props.flash.api_token)
  }
  
  if (props.games) {
    games.value = props.games
  }
  if (props.userGames) {
    userGames.value = props.userGames
  }
  loadFavorites()
  favoritesWatcher = watchFavorites()
  
  // Initialize notifications
  if (!isGuest.value) {
    loadNotifications()
    startNotificationPolling()
  }
})

onUnmounted(() => {
  if (favoritesWatcher) {
    favoritesWatcher()
  }
})

const handleSearch = (e) => {
  if (e) {
    e.preventDefault()
    e.stopPropagation()
  }
  // Always navigate to search page, even if empty (matching IT109 behavior)
  const query = searchQuery.value.trim() || ''
  const url = query ? `/search?q=${encodeURIComponent(query)}` : '/search'
  router.visit(url)
}

const navigateToGame = (gameId) => {
  router.visit(`/games/${gameId}`)
}

const addToLibrary = async (gameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to add games to your library')
    return
  }
  
  // Prevent duplicate additions
  if (libraryGameIds.value.has(gameId)) {
    return
  }
  
  // Optimistically update UI immediately
  const optimisticGame = {
    game_id: gameId,
    status: 'Playing',
    playtime_hours: 0,
    user_id: user.value?.id
  }
  userGames.value = [...userGames.value, optimisticGame]
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    const response = await axios.post('/api/user/games', {
      game_id: gameId,
      status: 'Playing',
      playtime_hours: 0
    }, { headers })
    
    // Backend automatically removes from wishlist if it was there
    toast.success('Success', response.data.message || 'Game added to library!')
    router.reload({ only: ['userGames'] })
  } catch (error) {
    // Revert optimistic update on error
    userGames.value = userGames.value.filter(ug => ug.game_id !== gameId)
    
    // If game already exists, that's okay (might be in wishlist and backend moved it)
    if (error.response?.status === 409 || error.response?.status === 200) {
      toast.success('Success', 'Game moved to library!')
      router.reload({ only: ['userGames'] })
    } else {
      toast.error('Error', error.response?.data?.message || 'Failed to add game to library')
    }
  }
}

const toggleFavorite = async (gameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to favorite games')
    return
  }
  
  try {
    // Normalize gameId to number for consistent comparison
    const gameIdNum = Number(gameId)
    
    // Get current favorites from localStorage
    const favoritesKey = `favorites_${user.value?.id || 'guest'}`
    const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
    // Normalize all IDs to numbers for comparison
    const normalizedFavorites = favorites.map(id => Number(id))
    const isFavorite = normalizedFavorites.includes(gameIdNum)
    
    if (isFavorite) {
      // Remove from favorites - update immediately
      const updatedFavorites = normalizedFavorites.filter(id => id !== gameIdNum)
      localStorage.setItem(favoritesKey, JSON.stringify(updatedFavorites))
      // Create new Set to ensure Vue reactivity
      favoriteGameIds.value = new Set(updatedFavorites)
      // Manually update array to ensure reactivity
      favoriteGameIdsArray.value = updatedFavorites
      toast.success('Removed', 'Game removed from favorites')
    } else {
      // Add to favorites - update immediately
      const updatedFavorites = [...normalizedFavorites, gameIdNum]
      localStorage.setItem(favoritesKey, JSON.stringify(updatedFavorites))
      // Create new Set to ensure Vue reactivity
      favoriteGameIds.value = new Set(updatedFavorites)
      // Manually update array to ensure reactivity
      favoriteGameIdsArray.value = updatedFavorites
      toast.success('Added', 'Game added to favorites!')
      
      // If game is not in library, add it first (optimistically)
      if (!libraryGameIds.value.has(gameIdNum)) {
        const optimisticGame = {
          game_id: gameIdNum,
          status: 'Playing',
          playtime_hours: 0,
          user_id: user.value?.id
        }
        userGames.value = [...userGames.value, optimisticGame]
        
        try {
          const token = localStorage.getItem('api_token')
          const headers = token ? { Authorization: `Bearer ${token}` } : {}
          
          await axios.post('/api/user/games', {
            game_id: gameIdNum,
            status: 'Playing',
            playtime_hours: 0
          }, { headers })
          router.reload({ only: ['userGames'] })
        } catch (error) {
          // Revert optimistic update on error
          userGames.value = userGames.value.filter(ug => ug.game_id !== gameIdNum)
        }
      }
    }
  } catch (error) {
    toast.error('Error', 'Failed to update favorite status')
  }
}

const addToWishlist = async (gameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to add games to your wishlist')
    return
  }
  
  // Optimistically update UI immediately
  const optimisticGame = {
    game_id: gameId,
    status: 'Wishlist',
    playtime_hours: 0,
    user_id: user.value?.id
  }
  userGames.value = [...userGames.value, optimisticGame]
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.post('/api/user/games', {
      game_id: gameId,
      status: 'Wishlist',
      playtime_hours: 0
    }, { headers })
    toast.success('Success', 'Game added to wishlist!')
    router.reload({ only: ['userGames'] })
  } catch (error) {
    // Revert optimistic update on error
    userGames.value = userGames.value.filter(ug => ug.game_id !== gameId)
    toast.error('Error', error.response?.data?.message || 'Failed to add game to wishlist')
  }
}

// Notification handlers are now provided by useNotifications composable
const handleNotificationClick = (notification) => {
  // Mark as read
  handleMarkAsRead(notification.id)
  
  // Navigate to the action URL if provided
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}
</script>

