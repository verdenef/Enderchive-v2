<template>
  <AppLayout>
    <div 
      class="min-h-screen relative"
      style="background: linear-gradient(180deg, #0a0015 0%, #1c0031 50%, #250040 100%)"
    >
      <div class="relative z-10">
        <!-- Sticky Navigation Bar -->
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto px-4 md:px-8 h-full flex items-center gap-4">
            <!-- Logo -->
            <div class="flex items-center gap-3 flex-shrink-0">
              <Icon name="lucide:gamepad-2" class="w-7 h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
              <h1 
                class="text-brand text-[#22c55e]"
                style="font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em"
              >
                Enderchive
              </h1>
            </div>

            <!-- Navigation Tabs -->
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

            <!-- Desktop Actions -->
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
                @click="router.visit('/profile')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 cursor-pointer group"
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
                <span class="text-sm whitespace-nowrap">
                  {{ isGuest ? 'Sign in / Create account' : (user?.name || 'Guest') }}
                </span>
              </button>
            </div>

            <!-- Mobile Hamburger Menu -->
            <MobileNav
              current-page="search"
              :username="user?.name || 'Guest'"
              :is-admin="isAdmin"
            />
          </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-[1280px] mx-auto px-4 md:px-8 pt-8 pb-16">
          <!-- Advanced Search -->
          <div class="mb-8">
            <div class="space-y-4">
              <!-- Search Bar -->
              <div class="flex gap-2">
                <div class="flex-1">
                  <GameInput
                    v-model="searchQuery"
                    placeholder="Search games..."
                    class="w-full"
                    @keyup.enter="handleSearch"
                  />
                </div>
                <GameButton
                  variant="primary"
                  size="sm"
                  @click="handleSearch"
                >
                  <template #icon>
                    <Icon name="lucide:search" class="w-4 h-4" />
                  </template>
                  Search
                </GameButton>
                <GameButton
                  variant="secondary"
                  size="sm"
                  @click="showFilters = !showFilters"
                >
                  <template #icon>
                    <Icon name="lucide:filter" class="w-4 h-4" />
                  </template>
                  Filters
                </GameButton>
              </div>
            </div>
          </div>

          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h1 class="mb-2 text-white text-2xl font-bold">
                {{ searchQuery.trim() 
                  ? `Search results for: "${searchQuery}"`
                  : 'All Games' }}
              </h1>
              <p class="text-sm text-white/60">{{ filteredGames.length }} games found</p>
            </div>
            
            <!-- View Toggle -->
            <div class="flex gap-2">
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'p-2 rounded-lg transition-all',
                  viewMode === 'grid'
                    ? 'bg-[#8b5cf6] text-white'
                    : 'bg-transparent text-gray-400 hover:text-white'
                ]"
                :style="{
                  boxShadow: viewMode === 'grid' ? '0 0 20px rgba(139, 92, 246, 0.5)' : 'none'
                }"
              >
                <Icon name="lucide:grid" class="w-5 h-5" />
              </button>
              <button
                @click="viewMode = 'list'"
                :class="[
                  'p-2 rounded-lg transition-all',
                  viewMode === 'list'
                    ? 'bg-[#8b5cf6] text-white'
                    : 'bg-transparent text-gray-400 hover:text-white'
                ]"
                :style="{
                  boxShadow: viewMode === 'list' ? '0 0 20px rgba(139, 92, 246, 0.5)' : 'none'
                }"
              >
                <Icon name="lucide:list" class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6" :class="showFilters ? 'lg:grid-cols-[280px_1fr]' : 'lg:grid-cols-1'">
            <!-- FILTER SIDEBAR -->
            <div v-if="showFilters" class="space-y-4">
              <div
                class="glass-panel rounded-lg p-6 border-4 border-[#2b1d3a] sticky top-4"
                style="box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2)"
              >
                <h3 class="mb-6 text-white font-semibold">Filters</h3>

                <!-- Genre Filter -->
                <div class="mb-6">
                  <h4 class="text-sm mb-3 text-white/80 font-semibold">Genre</h4>
                  <div class="space-y-2 max-h-48 overflow-y-auto">
                    <label
                      v-for="genre in genres"
                      :key="genre.genre_id"
                      class="flex items-center gap-2 cursor-pointer hover:text-[#8b5cf6] transition-colors"
                    >
                      <input
                        type="checkbox"
                        :checked="selectedGenres.includes(genre.genre_id)"
                        @change="toggleFilter(selectedGenres, genre.genre_id)"
                        class="w-4 h-4 rounded accent-[#8b5cf6]"
                      />
                      <span class="text-sm text-white/90">{{ genre.name }}</span>
                      <span class="text-xs text-white/50 ml-auto">({{ genre.games_count || 0 }})</span>
                    </label>
                  </div>
                </div>

                <!-- Platform Filter -->
                <div class="mb-6">
                  <h4 class="text-sm mb-3 text-white/80 font-semibold">Platform</h4>
                  <div class="space-y-2 max-h-48 overflow-y-auto">
                    <label
                      v-for="platform in platforms"
                      :key="platform.platform_id"
                      class="flex items-center gap-2 cursor-pointer hover:text-[#8b5cf6] transition-colors"
                    >
                      <input
                        type="checkbox"
                        :checked="selectedPlatforms.includes(platform.platform_id)"
                        @change="toggleFilter(selectedPlatforms, platform.platform_id)"
                        class="w-4 h-4 rounded accent-[#8b5cf6]"
                      />
                      <span class="text-sm text-white/90">{{ platform.name }}</span>
                      <span class="text-xs text-white/50 ml-auto">({{ platform.games_count || 0 }})</span>
                    </label>
                  </div>
                </div>

                <!-- Tags Filter -->
                <div class="mb-6">
                  <h4 class="text-sm mb-3 text-white/80 font-semibold">Tags</h4>
                  <div class="space-y-2 max-h-48 overflow-y-auto">
                    <label
                      v-for="tag in tags"
                      :key="tag.tag_id"
                      class="flex items-center gap-2 cursor-pointer hover:text-[#8b5cf6] transition-colors"
                    >
                      <input
                        type="checkbox"
                        :checked="selectedTags.includes(tag.tag_id)"
                        @change="toggleFilter(selectedTags, tag.tag_id)"
                        class="w-4 h-4 rounded accent-[#8b5cf6]"
                      />
                      <span class="text-sm text-white/90">{{ tag.name }}</span>
                      <span class="text-xs text-white/50 ml-auto">({{ tag.games_count || 0 }})</span>
                    </label>
                  </div>
                </div>

                <!-- Rating Filter -->
                <div class="mb-6">
                  <h4 class="text-sm mb-3 text-white/80 font-semibold">Rating</h4>
                  <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer hover:text-[#8b5cf6] transition-colors">
                      <input
                        type="radio"
                        name="rating"
                        :checked="minRating === 0"
                        @change="minRating = 0"
                        class="w-4 h-4 accent-[#8b5cf6]"
                      />
                      <span class="text-sm text-white/90">Any Rating</span>
                    </label>
                    <label
                      v-for="rating in [5, 4, 3, 2, 1]"
                      :key="rating"
                      class="flex items-center gap-2 cursor-pointer hover:text-[#8b5cf6] transition-colors"
                    >
                      <input
                        type="radio"
                        name="rating"
                        :checked="minRating === rating"
                        @change="minRating = rating"
                        class="w-4 h-4 accent-[#8b5cf6]"
                      />
                      <span class="text-sm text-white/90">{{ rating }}+ hearts</span>
                    </label>
                  </div>
                </div>

                <!-- Year Range Filter -->
                <div class="mb-6">
                  <h4 class="text-sm mb-3 text-white/80 font-semibold">Release Year</h4>
                  <div class="space-y-3">
                    <div>
                      <label class="text-xs text-white/60 mb-1 block">From</label>
                      <input
                        type="number"
                        v-model.number="yearRange[0]"
                        :min="1980"
                        :max="new Date().getFullYear() + 5"
                        class="w-full px-3 py-2 rounded-lg text-sm"
                        style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                      />
                    </div>
                    <div>
                      <label class="text-xs text-white/60 mb-1 block">To</label>
                      <input
                        type="number"
                        v-model.number="yearRange[1]"
                        :min="1980"
                        :max="new Date().getFullYear() + 5"
                        class="w-full px-3 py-2 rounded-lg text-sm"
                        style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                      />
                    </div>
                  </div>
                </div>

                <!-- Clear Filters -->
                <GameButton
                  v-if="hasFilters"
                  variant="danger"
                  :full-width="true"
                  @click="clearFilters"
                >
                  Clear All Filters
                </GameButton>
              </div>
            </div>

            <!-- RESULTS -->
            <div>
              <!-- Sort Bar -->
              <div class="mb-6">
                <select
                  v-model="sortBy"
                  class="px-4 py-2 rounded-lg text-sm cursor-pointer"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                >
                  <option value="relevance">Sort by: Relevance</option>
                  <option value="title">Title A-Z</option>
                  <option value="release">Release Date</option>
                  <option value="rating">Rating</option>
                  <option value="popular">Popular</option>
                </select>
              </div>

              <!-- Results Grid -->
              <div v-if="loading" class="text-center py-12 text-white/60">
                Searching...
              </div>
              <EmptyState
                v-else-if="paginatedGames.length === 0 && hasSearched"
                type="search"
                :on-cta-click="clearFilters"
              />
              <div v-else-if="paginatedGames.length === 0 && !hasSearched && !loading" class="text-center py-12">
                <p class="text-white/60 mb-4">Enter a search query to find games</p>
              </div>
              <template v-else-if="paginatedGames.length > 0 || filteredGames.length > 0">
                <div
                  :class="[
                    'grid mb-8',
                    viewMode === 'grid' ? 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : 'grid-cols-1 gap-3'
                  ]"
                >
                  <template v-if="viewMode === 'grid'">
                    <GameItemCard
                      v-for="game in paginatedGames"
                      :key="game.game_id"
                      :title="game.title"
                      :image="game.cover_image || 'https://via.placeholder.com/300x400?text=No+Image'"
                      :date="game.release_date || 'TBA'"
                      :genres="game.genre?.name || 'Unknown'"
                      :is-in-library="libraryGameIds.has(game.game_id)"
                      :is-favorite="favoriteGameIds.value?.has(game.game_id) || false"
                      :is-wishlisted="wishlistGameIds.has(game.game_id)"
                      @add="addToLibrary(game.game_id)"
                      @favorite="toggleFavorite(game.game_id)"
                      @wishlist="addToWishlist(game.game_id)"
                      @click="navigateToGame(game.game_id)"
                    />
                  </template>
                  <template v-else>
                    <div
                      v-for="game in paginatedGames"
                      :key="game.game_id"
                      class="flex items-center gap-4 p-3 rounded-lg border border-[#2b1d3a]"
                      style="background: rgba(27,22,38,0.6); box-shadow: inset 1px 1px 0 rgba(255,255,255,0.08), inset -1px -1px 0 rgba(0,0,0,0.25)"
                    >
                      <div class="w-16 h-20 flex-shrink-0 overflow-hidden rounded-md bg-black/30">
                        <img
                          :src="game.cover_image || 'https://via.placeholder.com/300x400?text=No+Image'"
                          :alt="game.title"
                          class="w-full h-full object-cover"
                        />
                      </div>
                      <div class="flex-1 min-w-0">
                        <button
                          @click="navigateToGame(game.game_id)"
                          class="text-left w-full"
                        >
                          <h3 class="text-sm font-semibold truncate hover:text-[#22c55e] transition-colors">
                            {{ game.title }}
                          </h3>
                          <p class="text-xs text-white/60 truncate">
                            {{ formatDate(game.release_date) }} · {{ game.genre?.name || 'Unknown' }}
                          </p>
                        </button>
                      </div>
                      <div class="flex items-center gap-2">
                        <GameButton
                          size="sm"
                          :variant="libraryGameIds.has(game.game_id) ? 'secondary' : 'primary'"
                          :disabled="libraryGameIds.has(game.game_id)"
                          @click.stop="addToLibrary(game.game_id)"
                        >
                          <template #icon>
                            <Icon :name="libraryGameIds.has(game.game_id) ? 'lucide:check' : 'lucide:plus'" class="w-4 h-4" />
                          </template>
                          {{ libraryGameIds.has(game.game_id) ? 'Added' : 'Add' }}
                        </GameButton>
                        <FavoriteButton
                          :is-favorite="favoriteGameIds.value?.has(game.game_id) || false"
                          @click="toggleFavorite(game.game_id)"
                        />
                        <WishlistButton
                          :is-wishlisted="wishlistGameIds.has(game.game_id)"
                          :is-in-library="libraryGameIds.has(game.game_id)"
                          @click="addToWishlist(game.game_id)"
                        />
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-center gap-2">
                  <button
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="p-2 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed hover:bg-white/10"
                  >
                    <Icon name="lucide:chevron-left" class="w-5 h-5" />
                  </button>

                  <template v-for="(page, idx) in pageNumbers" :key="`page-${page}`">
                    <span v-if="idx > 0 && pageNumbers[idx - 1] !== undefined && page - pageNumbers[idx - 1] > 1" class="text-gray-400">…</span>
                    <button
                      @click="currentPage = page"
                      :class="[
                        'w-10 h-10 rounded-lg transition-all',
                        currentPage === page
                          ? 'bg-[#15803d] text-white'
                          : 'bg-transparent text-gray-400 hover:text-white border-2 border-[#7c3aed]'
                      ]"
                      :style="{
                        boxShadow: currentPage === page ? '0 0 20px rgba(21, 128, 61, 0.5)' : 'none'
                      }"
                    >
                      {{ page }}
                    </button>
                  </template>

                  <button
                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="p-2 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed hover:bg-white/10"
                  >
                    <Icon name="lucide:chevron-right" class="w-5 h-5" />
                  </button>
                </div>
              </template>
            </div>
          </div>
        </main>

        <!-- Footer -->
        <footer 
          class="border-t px-8 py-6 mt-16"
          style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-top: 1px solid rgba(94,0,160,0.35)"
        >
          <div class="max-w-[1280px] mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
              <Icon name="lucide:gamepad-2" class="w-5 h-5 text-[#22c55e]" />
              <span 
                class="text-brand text-[#22c55e]"
                style="font-size: 10px"
              >
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
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import GameItemCard from '../Components/cards/GameItemCard.vue'
import EmptyState from '../Components/ui/EmptyState.vue'
import Icon from '../Components/ui/Icon.vue'
import FavoriteButton from '../Components/buttons/FavoriteButton.vue'
import WishlistButton from '../Components/buttons/WishlistButton.vue'
import { useToast } from '../Composables/useToast'
import { useNotifications } from '../Composables/useNotifications'
import axios from 'axios'

const props = defineProps({
  games: {
    type: Array,
    default: () => []
  },
  searchQuery: {
    type: String,
    default: ''
  },
})

const page = usePage()
const toast = useToast()

const user = computed(() => page.props.auth?.user)
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)

// Use global notifications
const { notifications, markAsRead: handleMarkAsRead, markAllAsRead: handleMarkAllAsRead, deleteNotification: handleDeleteNotification, loadNotifications, startNotificationPolling } = useNotifications(user)

// Notification handler
const handleNotificationClick = (notification) => {
  handleMarkAsRead(notification.id)
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}

// Get query parameter from URL
const getQueryParam = () => {
  if (typeof window === 'undefined') return ''
  const urlParams = new URLSearchParams(window.location.search)
  return urlParams.get('q') || ''
}

// Initialize search query from props or URL
const initialQuery = props.searchQuery || getQueryParam()
const searchQuery = ref(initialQuery)
const games = ref(props.games || [])
const userGames = ref([])
const loading = ref(false)
const hasSearched = ref(!!(searchQuery.value))

// View mode and filters
const viewMode = ref('grid')
const selectedGenres = ref([])
const selectedPlatforms = ref([])
const selectedTags = ref([])
const minRating = ref(0)
const maxRating = ref(10)
const yearRange = ref([1980, new Date().getFullYear() + 5])
const sortBy = ref('relevance')
const currentPage = ref(1)
const pageSize = ref(9)
const showFilters = ref(false)

const genres = ref([])
const platforms = ref([])
const tags = ref([])

const libraryGameIds = computed(() => {
  if (!userGames.value || !Array.isArray(userGames.value)) return new Set()
  return new Set(userGames.value.filter(ug => ug.status !== 'Wishlist').map(ug => ug.game_id))
})

const wishlistGameIds = computed(() => {
  return new Set(userGames.value.filter(ug => ug.status === 'Wishlist').map(ug => ug.game_id))
})

const favoriteGameIds = ref(new Set())

// Load favorites on mount and watch for changes
const loadFavorites = () => {
  try {
    if (isGuest.value) {
      favoriteGameIds.value = new Set()
      return
    }
    const favoritesKey = `favorites_${user.value?.id || 'guest'}`
    const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
    favoriteGameIds.value = new Set(favorites)
  } catch (error) {
    console.error('Error loading favorites:', error)
    favoriteGameIds.value = new Set()
  }
}

// Watch for localStorage changes (for cross-tab sync)
const watchFavorites = () => {
  if (isGuest.value) return () => {}
  const favoritesKey = `favorites_${user.value?.id || 'guest'}`
  const checkInterval = setInterval(() => {
    try {
      const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
      const currentSet = new Set(favorites)
      if (!favoriteGameIds.value) {
        favoriteGameIds.value = new Set()
      }
      if (currentSet.size !== favoriteGameIds.value.size || 
          ![...currentSet].every(id => favoriteGameIds.value?.has(id))) {
        favoriteGameIds.value = currentSet
      }
    } catch (error) {
      console.error('Error watching favorites:', error)
    }
  }, 100)
  
  return () => clearInterval(checkInterval)
}

let favoritesWatcher = null

const loadGames = async (query = null) => {
  const searchTerm = query !== null ? query : searchQuery.value.trim()
  
  loading.value = true
  hasSearched.value = !!searchTerm
  
  try {
    const params = {}
    if (searchTerm) {
      params.search = searchTerm
    }
    
    // Add genre filters
    if (selectedGenres.value.length > 0) {
      params.genre_id = selectedGenres.value[0] // Backend currently supports single genre
    }
    
    // Add platform filters
    if (selectedPlatforms.value.length > 0) {
      params.platform_id = selectedPlatforms.value[0] // Backend currently supports single platform
    }
    
    const response = await axios.get('/api/games', { params })
    games.value = response.data.data || []
    
    // Apply client-side filtering for tags, rating, year range (since backend doesn't support them yet)
    // Filtering handled by computed property
  } catch (error) {
    console.error('Failed to search games:', error)
    games.value = []
    if (searchTerm) {
      toast.error('Error', 'Failed to search games')
    }
  } finally {
    loading.value = false
  }
}

// Filter and sort games
const filteredGames = computed(() => {
  if (!games.value || !Array.isArray(games.value)) {
    return []
  }
  let filtered = [...games.value]
  
  // Filter by genre (using IDs)
  if (selectedGenres.value.length > 0) {
    filtered = filtered.filter(game => {
      return selectedGenres.value.includes(game.genre_id)
    })
  }
  
  // Filter by platform (using IDs)
  if (selectedPlatforms.value.length > 0) {
    filtered = filtered.filter(game => {
      if (!game.platforms || !Array.isArray(game.platforms)) return false
      return game.platforms.some(p => selectedPlatforms.value.includes(p.platform_id))
    })
  }
  
  // Filter by tags (using IDs)
  if (selectedTags.value.length > 0) {
    filtered = filtered.filter(game => {
      if (!game.tags || !Array.isArray(game.tags)) return false
      return game.tags.some(t => selectedTags.value.includes(t.tag_id))
    })
  }
  
  // Filter by rating
  if (minRating.value > 0) {
    filtered = filtered.filter(game => {
      const rating = game.rating || 0
      return rating >= minRating.value
    })
  }
  
  // Filter by year range
  filtered = filtered.filter(game => {
    if (!game.release_date) return true
    const releaseYear = new Date(game.release_date).getFullYear()
    return releaseYear >= yearRange.value[0] && releaseYear <= yearRange.value[1]
  })
  
  // Sort - always use sortBy value, default to 'title' if 'relevance' and no search query
  const effectiveSortBy = (sortBy.value === 'relevance' && !searchQuery.value.trim()) ? 'title' : sortBy.value
  filtered = [...filtered].sort((a, b) => {
    switch (effectiveSortBy) {
      case 'title':
        return (a.title || '').localeCompare(b.title || '')
      case 'release': {
        const dateA = new Date(a.release_date || '').getTime()
        const dateB = new Date(b.release_date || '').getTime()
        // Handle invalid dates
        if (isNaN(dateA) && isNaN(dateB)) return 0
        if (isNaN(dateA)) return 1
        if (isNaN(dateB)) return -1
        return dateB - dateA // Newest first
      }
      case 'rating':
        return (b.rating || 0) - (a.rating || 0)
      case 'popular':
        return (b.game_id || 0) - (a.game_id || 0)
      case 'relevance':
      default:
        return 0 // Keep original order
    }
  })
  
  return filtered
})

// Pagination
const totalPages = computed(() => Math.max(1, Math.ceil(filteredGames.value.length / pageSize.value)))
const safeCurrentPage = computed(() => Math.min(currentPage.value, totalPages.value))
const paginatedGames = computed(() => {
  if (!filteredGames.value || filteredGames.value.length === 0) {
    return []
  }
  const start = (safeCurrentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  return filteredGames.value.slice(start, end)
})

const pageNumbers = computed(() => {
  const pages = new Set()
  pages.add(1)
  pages.add(totalPages.value)
  const start = Math.max(1, safeCurrentPage.value - 2)
  const end = Math.min(totalPages.value, safeCurrentPage.value + 2)
  for (let p = start; p <= end; p++) {
    pages.add(p)
  }
  return Array.from(pages).sort((a, b) => a - b)
})

const hasFilters = computed(() => {
  return selectedGenres.value.length > 0 || 
         selectedPlatforms.value.length > 0 || 
         selectedTags.value.length > 0 ||
         minRating.value > 0 ||
         yearRange.value[0] !== 1980 ||
         yearRange.value[1] !== (new Date().getFullYear() + 5)
})

const formatDate = (dateString) => {
  if (!dateString || dateString === 'TBA') return 'TBA'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return 'TBA'
    return date.toISOString().split('T')[0] // Returns YYYY-MM-DD
  } catch (error) {
    return 'TBA'
  }
}

const toggleFilter = (filterArray, value) => {
  const index = filterArray.indexOf(value)
  if (index > -1) {
    filterArray.splice(index, 1)
  } else {
    filterArray.push(value)
  }
}

const clearFilters = () => {
  selectedGenres.value = []
  selectedPlatforms.value = []
  selectedTags.value = []
  minRating.value = 0
  yearRange.value = [1980, new Date().getFullYear() + 5]
  searchQuery.value = ''
  hasSearched.value = false
  games.value = []
  router.visit('/search')
}

// Reset pagination when filters/search change
watch([searchQuery, selectedGenres, selectedPlatforms, selectedTags, minRating, yearRange, sortBy], () => {
  currentPage.value = 1
}, { deep: true })

watch(viewMode, () => {
  pageSize.value = viewMode.value === 'grid' ? 9 : 8
})

const loadFilterOptions = async () => {
  try {
    // Load genres
    const genresRes = await axios.get('/api/genres')
    genres.value = genresRes.data.data || []
    
    // Load platforms
    const platformsRes = await axios.get('/api/platforms')
    platforms.value = platformsRes.data.data || []
    
    // Load tags
    const tagsRes = await axios.get('/api/tags')
    tags.value = tagsRes.data.data || []
  } catch (error) {
    console.error('Failed to load filter options:', error)
  }
}

onMounted(async () => {
  // Initialize notifications
  if (!isGuest.value) {
    loadNotifications()
    startNotificationPolling()
  }
  
  loadFavorites()
  favoritesWatcher = watchFavorites()
  await loadFilterOptions()
  
  if (!isGuest.value) {
    await loadUserGames()
  }
  
  // Always check URL for query parameter (in case navigating from Home page)
  const urlQuery = getQueryParam()
  if (urlQuery && urlQuery !== searchQuery.value) {
    searchQuery.value = urlQuery
  }
  
  // If we have games from props (backend search), use them
  if (props.games && props.games.length > 0) {
    games.value = props.games
    hasSearched.value = !!searchQuery.value.trim()
  } else if (searchQuery.value.trim()) {
    // If there's a query but no games from props, fetch them
    await loadGames()
  } else {
    // Load all games when no query (like IT109)
    await loadGames('')
  }
})

onUnmounted(() => {
  if (favoritesWatcher) {
    favoritesWatcher()
  }
})

const loadUserGames = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    const response = await axios.get('/api/user/games', { headers })
    userGames.value = response.data.data || []
  } catch (error) {
    // Don't show error if user is not authenticated (401)
    if (error.response?.status !== 401) {
      console.error('Failed to load user games:', error)
    }
  }
}

const handleSearch = () => {
  if (!searchQuery.value.trim()) {
    hasSearched.value = false
    games.value = []
    router.visit('/search')
    return
  }
  // Navigate with query - backend will handle fetching games
  router.visit(`/search?q=${encodeURIComponent(searchQuery.value.trim())}`)
}

const navigateToGame = (gameId) => {
  router.visit(`/games/${gameId}`)
}

const addToLibrary = async (gameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to add games to your library')
    return
  }
  
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
    
    toast.success('Success', response.data.message || 'Game added to library!')
    // Refresh to get the actual data from backend
    await loadUserGames()
  } catch (error) {
    // Revert optimistic update on error
    userGames.value = userGames.value.filter(ug => ug.game_id !== gameId)
    
    if (error.response?.status === 409 || error.response?.status === 200) {
      toast.success('Success', 'Game moved to library!')
      await loadUserGames()
    } else {
      toast.error('Error', error.response?.data?.message || 'Failed to add game to library')
    }
  }
}

const addToWishlist = async (gameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to add games to your wishlist')
    return
  }
  
  if (libraryGameIds.value.has(gameId)) {
    toast.error('Already in Library', 'This game is already in your library')
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
    // Refresh to get the actual data from backend
    await loadUserGames()
  } catch (error) {
    // Revert optimistic update on error
    userGames.value = userGames.value.filter(ug => ug.game_id !== gameId)
    toast.error('Error', error.response?.data?.message || 'Failed to add game to wishlist')
  }
}

const toggleFavorite = async (gameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to favorite games')
    return
  }
  
  try {
    const favoritesKey = `favorites_${user.value?.id || 'guest'}`
    const favorites = JSON.parse(localStorage.getItem(favoritesKey) || '[]')
    const isFavorite = favorites.includes(gameId)
    
    if (isFavorite) {
      // Remove from favorites
      const updatedFavorites = favorites.filter(id => id !== gameId)
      localStorage.setItem(favoritesKey, JSON.stringify(updatedFavorites))
      // Create new Set to ensure Vue reactivity
      favoriteGameIds.value = new Set(updatedFavorites)
      toast.success('Removed', 'Game removed from favorites')
    } else {
      // Add to favorites
      favorites.push(gameId)
      localStorage.setItem(favoritesKey, JSON.stringify(favorites))
      // Create new Set to ensure Vue reactivity
      favoriteGameIds.value = new Set(favorites)
      toast.success('Added', 'Game added to favorites!')
      
      // If game is not in library, add it first (optimistically)
      if (!libraryGameIds.value.has(gameId)) {
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
          
          await axios.post('/api/user/games', {
            game_id: gameId,
            status: 'Playing',
            playtime_hours: 0
          }, { headers })
          await loadUserGames()
        } catch (error) {
          // Revert optimistic update on error
          userGames.value = userGames.value.filter(ug => ug.game_id !== gameId)
        }
      }
    }
  } catch (error) {
    toast.error('Error', 'Failed to update favorite status')
  }
}

// Notification handlers are provided by useNotifications composable
</script>
