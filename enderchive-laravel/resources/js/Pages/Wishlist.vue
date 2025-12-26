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
                :active="false"
                @click="router.visit('/library')"
              >
                <template #icon>
                  <Icon name="lucide:library" class="w-5 h-5" />
                </template>
              </NavigationTab>
              <NavigationTab
                label="Wishlist"
                :active="true"
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
              current-page="wishlist"
              :username="user?.name || 'Guest'"
              :is-admin="isAdmin"
            />
          </div>
        </header>

        <main class="container-standard py-8">
          <div class="mb-8">
            <h2 class="text-3xl font-bold mb-4">Wishlist</h2>
            <p class="text-white/60">Games you want to play</p>
          </div>

          <EmptyState
            v-if="wishlistGames.length === 0"
            type="wishlist"
            :on-cta-click="() => router.visit('/search')"
          />
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <GameItemCard
              v-for="userGame in wishlistGames"
              :key="userGame.user_game_id"
              :title="userGame.game?.title || 'Unknown Game'"
              :image="userGame.game?.cover_image || 'https://via.placeholder.com/300x400?text=No+Image'"
              :date="userGame.game?.release_date || 'TBA'"
              :genres="userGame.game?.genre?.name || 'Unknown'"
              :is-in-library="false"
              :is-wishlisted="true"
              @add="addToLibrary(userGame)"
              @wishlist="removeFromWishlist(userGame.user_game_id)"
              @click="navigateToGame(userGame.game?.game_id || 0)"
            />
          </div>
        </main>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import GameItemCard from '../Components/cards/GameItemCard.vue'
import EmptyState from '../Components/ui/EmptyState.vue'
import Icon from '../Components/ui/Icon.vue'
import { useToast } from '../Composables/useToast'
import { useNotifications } from '../Composables/useNotifications'
import axios from 'axios'

const props = defineProps({
  wishlistGames: Array,
})

const page = usePage()
const toast = useToast()

const user = computed(() => page.props.auth?.user)
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)

const wishlistGames = ref(props.wishlistGames || [])

// Use global notifications
const { notifications, markAsRead: handleMarkAsRead, markAllAsRead: handleMarkAllAsRead, deleteNotification: handleDeleteNotification, loadNotifications, startNotificationPolling } = useNotifications(user)

// Notification handler
const handleNotificationClick = (notification) => {
  handleMarkAsRead(notification.id)
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}

// Notification handler is already defined above

const navigateToGame = (gameId) => {
  if (gameId) {
    router.visit(`/games/${gameId}`)
  }
}

const addToLibrary = async (userGame) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to add games to your library')
    return
  }
  
  if (!userGame.game?.game_id) return
  
  // Optimistically remove from wishlist immediately
  const gameToRemove = wishlistGames.value.find(g => g.user_game_id === userGame.user_game_id)
  wishlistGames.value = wishlistGames.value.filter(g => g.user_game_id !== userGame.user_game_id)
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    await axios.put(`/api/user/games/${userGame.user_game_id}`, {
      status: 'Playing'
    }, { headers })
    toast.success('Success', 'Game moved to library!')
    // Reload to sync with backend (in case of any differences)
    router.reload({ only: ['wishlistGames'] })
  } catch (error) {
    // Revert optimistic update on error
    if (gameToRemove) {
      wishlistGames.value = [...wishlistGames.value, gameToRemove]
    }
    toast.error('Error', error.response?.data?.message || 'Failed to add game to library')
  }
}

const removeFromWishlist = async (userGameId) => {
  if (isGuest.value) {
    toast.error('Guest Account', 'Please create an account to remove games from your wishlist')
    return
  }
  
  if (confirm('Remove this game from your wishlist?')) {
    // Optimistically remove from UI immediately
    const gameToRemove = wishlistGames.value.find(g => g.user_game_id === userGameId)
    wishlistGames.value = wishlistGames.value.filter(g => g.user_game_id !== userGameId)
    
    try {
      const token = localStorage.getItem('api_token')
      const headers = token ? { Authorization: `Bearer ${token}` } : {}
      await axios.delete(`/api/user/games/${userGameId}`, { headers })
      toast.success('Success', 'Game removed from wishlist')
      // Reload to sync with backend (in case of any differences)
      router.reload({ only: ['wishlistGames'] })
    } catch (error) {
      // Revert optimistic update on error
      if (gameToRemove) {
        wishlistGames.value = [...wishlistGames.value, gameToRemove]
      }
      toast.error('Error', error.response?.data?.message || 'Failed to remove game')
    }
  }
}

</script>

