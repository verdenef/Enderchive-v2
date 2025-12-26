<template>
  <AppLayout>
    <div class="min-h-screen relative">
      <div class="relative z-10">
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto px-4 md:px-8 h-full flex items-center gap-4">
            <!-- Logo -->
            <button @click="router.visit('/home')" class="flex items-center gap-2 md:gap-3 flex-shrink-0">
              <Icon name="lucide:gamepad-2" class="w-6 h-6 md:w-7 md:h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
              <h1 class="text-brand text-[#22c55e] hidden sm:block" style="font-size: 12px; md:font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em">
                Enderchive
              </h1>
            </button>

            <!-- Desktop Navigation Tabs - Hidden on mobile -->
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
                :active="true"
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

            <!-- Desktop Actions - Hidden on mobile -->
            <div class="hidden md:flex items-center gap-4 flex-shrink-0">
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
                class="flex items-center gap-2 px-3 md:px-4 py-1.5 md:py-2 rounded-lg transition-all duration-200 cursor-pointer group"
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

            <!-- Mobile Hamburger Menu -->
            <MobileNav
              current-page="library"
              :username="user?.name || 'Guest'"
              :is-admin="isAdmin"
            />
          </div>
        </header>

        <main class="max-w-[1280px] mx-auto px-4 md:px-8 pt-8 pb-16">
          <div class="mb-6">
            <LibraryManagement
              :collections="collections"
              :selected-collection-id="selectedCollectionId"
              @add-collection="handleAddCollection"
              @delete-collection="handleDeleteCollection"
              @select-collection="handleSelectCollection"
            />
          </div>

          <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <h2>Your Library</h2>
            
            <div class="flex items-center gap-3">
              <GameButton
                :variant="bulkSelectMode ? 'primary' : 'secondary'"
                size="sm"
                @click="bulkSelectMode = !bulkSelectMode; if (bulkSelectMode) selectedGames.clear()"
              >
                {{ bulkSelectMode ? 'Cancel' : 'Select Multiple' }}
              </GameButton>
              <FilterDropdown
                label="Status"
                :options="['All', 'Playing', 'Completed', 'Abandoned']"
                :value="statusFilter"
                @update:value="statusFilter = $event"
                :width="150"
              />
              <FilterDropdown
                label="Sort"
                :options="['Title A-Z', 'Release Date']"
                :value="sortFilter"
                @update:value="sortFilter = $event"
                :width="150"
              />
            </div>
          </div>

          <div
            v-if="bulkSelectMode"
            class="glass-panel rounded-lg p-4 mb-6 flex items-center justify-between border-2"
            style="border-color: #22c55e; box-shadow: 0 0 20px rgba(34, 197, 94, 0.3)"
          >
            <div class="flex items-center gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  :checked="selectedGames.size === filteredGames.length && filteredGames.length > 0"
                  @change="(e) => e.target.checked ? selectAll() : deselectAll()"
                  class="w-5 h-5 rounded accent-[#22c55e] cursor-pointer"
                />
                <span class="text-sm font-semibold">
                  {{ selectedGames.size === 0 
                    ? 'Select All' 
                    : `${selectedGames.size} game${selectedGames.size !== 1 ? 's' : ''} selected`
                  }}
                </span>
              </label>
            </div>
            <div v-if="selectedGames.size > 0" class="flex gap-3">
              <GameButton
                variant="danger"
                size="sm"
                @click="handleBulkDelete"
              >
                Delete Selected
              </GameButton>
            </div>
          </div>

          <div 
            class="relative rounded-2xl"
            style="background: radial-gradient(ellipse at center, transparent 0%, rgba(10,0,21,0.4) 100%); padding: 2px"
          >
            <div v-if="filteredGames.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              <LibraryGameCard
                v-for="game in filteredGames"
                :key="game.user_game_id"
                :title="game.game?.title || game.title || 'Unknown Game'"
                :image="game.game?.cover_image || game.image || 'https://via.placeholder.com/300x400?text=No+Image'"
                :date="formatReleaseDate(game.game?.release_date || game.date)"
                :genres="game.game?.genre?.name || game.genres || 'Unknown'"
                :status="game.status"
                :show-status="true"
                :bulk-select-mode="bulkSelectMode"
                :is-selected="selectedGames.has(game.user_game_id)"
                :is-favorite="favoriteGameIds.has(Number(game.game_id || game.game?.game_id))"
                @status-change="(status) => handleStatusChange(game.user_game_id, status)"
                @remove="() => handleRemove(game.user_game_id, game.game_id || game.game?.game_id)"
                @click="navigateToGame(game.game_id || game.game?.game_id)"
                @toggle-select="toggleGameSelection(game.user_game_id)"
                @toggle-favorite="toggleFavorite(Number(game.game_id || game.game?.game_id))"
              />
            </div>
            <div v-else class="py-24 text-center">
              <Icon name="lucide:library" class="w-16 h-16 mx-auto mb-4" style="color: rgba(255, 255, 255, 0.2)" />
              <p style="color: rgba(255, 255, 255, 0.6)">No games in Library yet.</p>
            </div>
          </div>
        </main>

        <footer 
          class="border-t px-8 py-6"
          style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-top: 1px solid rgba(94,0,160,0.35)"
        >
          <div class="max-w-[1280px] mx-auto flex items-center justify-between">
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
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import FloatingParticles from '../Components/effects/FloatingParticles.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import FilterDropdown from '../Components/forms/FilterDropdown.vue'
import LibraryManagement from '../Components/library/LibraryManagement.vue'
import LibraryGameCard from '../Components/cards/LibraryGameCard.vue'
import Icon from '../Components/ui/Icon.vue'
import { useToast } from '../Composables/useToast'
import { useNotifications } from '../Composables/useNotifications'
import axios from 'axios'

const page = usePage()
const toast = useToast()

const user = computed(() => page.props.auth?.user)
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)

// Use global notifications
const { notifications, markAsRead: handleMarkAsRead, markAllAsRead: handleMarkAllAsRead, deleteNotification: handleDeleteNotification, loadNotifications, startNotificationPolling } = useNotifications(user)

const props = defineProps({
  userGames: {
    type: Array,
    default: () => []
  }
})

const libraryGames = ref(props.userGames || [])
const customCollections = ref([])
const selectedCollectionId = ref(null)
const statusFilter = ref('All')
const sortFilter = ref('Title A-Z')
const bulkSelectMode = ref(false)
const selectedGames = ref(new Set())
const favoriteGameIds = ref(new Set())

const smartCollections = computed(() => [
  {
    id: 1,
    name: 'Completed Games',
    description: 'All completed games',
    gameCount: libraryGames.value.filter(g => g.status === 'Completed').length,
    type: 'smart',
  },
  {
    id: 2,
    name: 'Currently Playing',
    description: 'Games in progress',
    gameCount: libraryGames.value.filter(g => g.status === 'Playing').length,
    type: 'smart',
  },
  {
    id: 3,
    name: 'Abandoned',
    description: 'Games you stopped playing',
    gameCount: libraryGames.value.filter(g => g.status === 'Abandoned').length,
    type: 'smart',
  },
  {
    id: 4,
    name: 'Favorites',
    description: 'My favorite games',
    gameCount: libraryGames.value.filter(g => {
      const gameId = g.game_id || g.game?.game_id
      return favoriteGameIds.value.has(Number(gameId))
    }).length,
    type: 'smart',
  },
])

const collections = computed(() => [
  ...smartCollections.value,
  ...customCollections.value,
])

const filteredGames = computed(() => {
  let filtered = [...libraryGames.value]

  // Collection filter
  if (selectedCollectionId.value) {
    if (selectedCollectionId.value === 1) {
      filtered = filtered.filter(g => g.status === 'Completed')
    } else if (selectedCollectionId.value === 2) {
      filtered = filtered.filter(g => g.status === 'Playing')
    } else if (selectedCollectionId.value === 3) {
      filtered = filtered.filter(g => g.status === 'Abandoned')
    } else if (selectedCollectionId.value === 4) {
      // Favorites collection - check by game_id (mapped to rawgGameId for IT109 compatibility)
      filtered = filtered.filter(g => {
        const gameId = g.game_id || g.game?.game_id
        return favoriteGameIds.value.has(Number(gameId))
      })
    }
  }

  // Status filter
  if (statusFilter.value !== 'All') {
    filtered = filtered.filter(g => g.status === statusFilter.value)
  }

  // Sort
  if (sortFilter.value === 'Title A-Z') {
    filtered.sort((a, b) => {
      const titleA = a.game?.title || a.title || ''
      const titleB = b.game?.title || b.title || ''
      return titleA.localeCompare(titleB)
    })
  } else if (sortFilter.value === 'Release Date') {
    filtered.sort((a, b) => {
      const dateA = new Date(a.game?.release_date || a.date || 0).getTime()
      const dateB = new Date(b.game?.release_date || b.date || 0).getTime()
      return dateB - dateA
    })
  }

  return filtered
})

const handleLibraryUpdate = () => {
  loadLibrary()
}

// Notification handler
const handleNotificationClick = (notification) => {
  handleMarkAsRead(notification.id)
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}

onMounted(() => {
  // Initialize notifications
  if (!isGuest.value) {
    loadNotifications()
    startNotificationPolling()
  }
  
  // Load favorites from localStorage
  if (user.value) {
    const favoritesKey = `favorites_${user.value.id}`
    const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
    // Convert to numbers for consistent comparison
    favoriteGameIds.value = new Set(favorites.map(id => Number(id)))
  }
  
  if (props.userGames) {
    // Filter out wishlist items and ensure proper data structure
    libraryGames.value = props.userGames
      .filter(g => g.status !== 'Wishlist')
      .map(g => ({
        ...g,
        user_game_id: g.user_game_id,
        game_id: g.game?.game_id || g.game_id,
        // Map game_id to rawgGameId for IT109 compatibility
        rawgGameId: g.game?.game_id || g.game_id,
      }))
  }
  
  // Listen for library update events
  window.addEventListener('enderchive-library-updated', handleLibraryUpdate)
})

const formatReleaseDate = (date) => {
  if (!date || date === 'TBA') return 'TBA'
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return 'TBA'
    // Return only the date part (YYYY-MM-DD) without time
    return dateObj.toISOString().split('T')[0]
  } catch (error) {
    return 'TBA'
  }
}

onUnmounted(() => {
  window.removeEventListener('enderchive-library-updated', handleLibraryUpdate)
})

const loadLibrary = async () => {
  router.reload({ only: ['userGames'] })
}

const handleAddCollection = (name, type) => {
  if (type === 'custom') {
    customCollections.value.push({
      id: Date.now(),
      name,
      gameCount: 0,
      type: 'custom',
    })
  }
}

const handleDeleteCollection = (id) => {
  customCollections.value = customCollections.value.filter(c => c.id !== id)
}

const handleSelectCollection = (id) => {
  selectedCollectionId.value = selectedCollectionId.value === id ? null : id
}

const handleStatusChange = async (userGameId, status) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.put(`/api/user/games/${userGameId}`, { status }, { headers })
    await loadLibrary()
    toast.success('Success', 'Status updated')
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to update status')
  }
}

const handleRemove = async (userGameId, gameId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    // Optimistically remove from UI immediately
    const gameToRemove = libraryGames.value.find(g => g.user_game_id === userGameId)
    libraryGames.value = libraryGames.value.filter(g => g.user_game_id !== userGameId)
    
    // Instead of deleting, move to wishlist (like IT109)
    await axios.put(`/api/user/games/${userGameId}`, { status: 'Wishlist' }, { headers })
    toast.success('Success', 'Game moved to wishlist')
    // Dispatch event for wishlist to refresh (pass gameId like IT109)
    window.dispatchEvent(new CustomEvent('enderchive-wishlist-updated', { detail: { rawgGameId: gameId } }))
    window.dispatchEvent(new CustomEvent('enderchive-library-updated'))
    // Reload to sync with backend (in case of any differences)
    await loadLibrary()
  } catch (error) {
    // Revert optimistic update on error
    if (gameToRemove) {
      libraryGames.value = [...libraryGames.value, gameToRemove]
    }
    toast.error('Error', error.response?.data?.message || 'Failed to move game')
  }
}

const toggleGameSelection = (userGameId) => {
  if (selectedGames.value.has(userGameId)) {
    selectedGames.value.delete(userGameId)
  } else {
    selectedGames.value.add(userGameId)
  }
}

const selectAll = () => {
  selectedGames.value = new Set(filteredGames.value.map(g => g.user_game_id))
}

const deselectAll = () => {
  selectedGames.value.clear()
}

const handleBulkDelete = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    // Optimistically remove from UI immediately
    const gamesToRemove = libraryGames.value.filter(g => selectedGames.value.has(g.user_game_id))
    const selectedIds = Array.from(selectedGames.value)
    libraryGames.value = libraryGames.value.filter(g => !selectedGames.value.has(g.user_game_id))
    selectedGames.value.clear()
    bulkSelectMode.value = false
    
    await Promise.all(selectedIds.map(id => 
      axios.delete(`/api/user/games/${id}`, { headers })
    ))
    toast.success('Success', 'Games deleted')
    // Reload to sync with backend (in case of any differences)
    await loadLibrary()
  } catch (error) {
    // Revert optimistic update on error
    if (gamesToRemove.length > 0) {
      libraryGames.value = [...libraryGames.value, ...gamesToRemove]
    }
    toast.error('Error', error.response?.data?.message || 'Failed to delete games')
  }
}

const navigateToGame = (gameId) => {
  if (gameId) {
    router.visit(`/games/${gameId}`)
  }
}

const toggleFavorite = (gameId) => {
  if (!user.value) {
    toast.error('Guest Account', 'Please create an account to favorite games')
    return
  }
  
  const favoritesKey = `favorites_${user.value.id}`
  let favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
  const gameIdNum = Number(gameId)
  
  if (favoriteGameIds.value.has(gameIdNum)) {
    favoriteGameIds.value.delete(gameIdNum)
    favorites = favorites.filter(id => Number(id) !== gameIdNum)
  } else {
    favoriteGameIds.value.add(gameIdNum)
    favorites.push(gameIdNum)
    // If game is not in library, add it
    const gameInLibrary = libraryGames.value.find(g => {
      const gId = g.game_id || g.game?.game_id
      return Number(gId) === gameIdNum
    })
    if (!gameInLibrary) {
      // Add to library with Playing status
      axios.post('/api/user/games', {
        game_id: gameIdNum,
        status: 'Playing'
      }).then(() => {
        loadLibrary()
      }).catch(() => {
        // Ignore errors, just update favorite state
      })
    }
  }
  
  localStorage.setItem(favoritesKey, JSON.stringify(favorites))
}

// Notification handlers are provided by useNotifications composable
</script>

