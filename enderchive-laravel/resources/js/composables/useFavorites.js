import { ref, computed, watch } from 'vue'

// Global favorites state (shared across all components)
const globalFavorites = ref(new Set())
let currentUserId = null
let favoritesWatcher = null

/**
 * Favorites composable with localStorage persistence and cross-tab sync
 */
export function useFavorites(user) {
  // Convert Set to array for better Vue reactivity tracking
  const favoriteGameIdsArray = ref([])

  // Update array whenever Set changes
  watch(globalFavorites, (newSet) => {
    favoriteGameIdsArray.value = Array.from(newSet)
  }, { immediate: true, deep: true })

  const loadFavorites = () => {
    const userId = user.value?.id || user.value?.user_id || currentUserId
    if (!userId) {
      globalFavorites.value = new Set()
      favoriteGameIdsArray.value = []
      return
    }

    currentUserId = userId

    try {
      const favoritesKey = `favorites_${userId}`
      const stored = localStorage.getItem(favoritesKey)
      if (stored) {
        const favorites = JSON.parse(stored)
        // Normalize IDs to numbers for consistent comparison
        const normalizedFavs = favorites.map(id => Number(id))
        globalFavorites.value = new Set(normalizedFavs)
        favoriteGameIdsArray.value = normalizedFavs
      } else {
        globalFavorites.value = new Set()
        favoriteGameIdsArray.value = []
      }
    } catch (error) {
      console.error('Error loading favorites:', error)
      globalFavorites.value = new Set()
      favoriteGameIdsArray.value = []
    }
  }

  const saveFavorites = () => {
    const userId = user.value?.id || user.value?.user_id || currentUserId
    if (!userId) return

    try {
      const favoritesKey = `favorites_${userId}`
      const favoritesArray = Array.from(globalFavorites.value)
      localStorage.setItem(favoritesKey, JSON.stringify(favoritesArray))
    } catch (error) {
      console.error('Error saving favorites:', error)
    }
  }

  const isFavorite = (gameId) => {
    return favoriteGameIdsArray.value.includes(Number(gameId))
  }

  const toggleFavorite = (gameId) => {
    const gameIdNum = Number(gameId)
    const isCurrentlyFavorite = globalFavorites.value.has(gameIdNum)

    if (isCurrentlyFavorite) {
      globalFavorites.value.delete(gameIdNum)
    } else {
      globalFavorites.value.add(gameIdNum)
    }

    saveFavorites()
    return !isCurrentlyFavorite // Return new state (true = now favorited, false = now unfavorited)
  }

  const addFavorite = (gameId) => {
    const gameIdNum = Number(gameId)
    if (!globalFavorites.value.has(gameIdNum)) {
      globalFavorites.value.add(gameIdNum)
      saveFavorites()
    }
  }

  const removeFavorite = (gameId) => {
    const gameIdNum = Number(gameId)
    if (globalFavorites.value.has(gameIdNum)) {
      globalFavorites.value.delete(gameIdNum)
      saveFavorites()
    }
  }

  const watchFavorites = () => {
    const userId = user.value?.id || user.value?.user_id || currentUserId
    if (!userId) return () => {}

    const favoritesKey = `favorites_${userId}`
    
    // Clear existing watcher if any
    if (favoritesWatcher) {
      clearInterval(favoritesWatcher)
    }

    favoritesWatcher = setInterval(() => {
      try {
        const stored = localStorage.getItem(favoritesKey)
        if (stored) {
          const favorites = JSON.parse(stored)
          const currentSet = new Set(favorites.map(id => Number(id)))
          
          // Check if favorites changed
          if (currentSet.size !== globalFavorites.value.size || 
              ![...currentSet].every(id => globalFavorites.value.has(id))) {
            globalFavorites.value = currentSet
          }
        }
      } catch (error) {
        console.error('Error watching favorites:', error)
      }
    }, 100)

    // Return cleanup function
    return () => {
      if (favoritesWatcher) {
        clearInterval(favoritesWatcher)
        favoritesWatcher = null
      }
    }
  }

  const stopWatchingFavorites = () => {
    if (favoritesWatcher) {
      clearInterval(favoritesWatcher)
      favoritesWatcher = null
    }
  }

  // Auto-load favorites when user changes
  watch(() => user.value?.id || user.value?.user_id, (userId) => {
    if (userId) {
      loadFavorites()
      watchFavorites()
    } else {
      globalFavorites.value = new Set()
      favoriteGameIdsArray.value = []
      stopWatchingFavorites()
    }
  }, { immediate: true })

  return {
    favorites: computed(() => globalFavorites.value),
    favoriteGameIds: computed(() => globalFavorites.value),
    favoriteGameIdsArray: computed(() => favoriteGameIdsArray.value),
    isFavorite,
    toggleFavorite,
    addFavorite,
    removeFavorite,
    loadFavorites,
    watchFavorites,
    stopWatchingFavorites
  }
}

