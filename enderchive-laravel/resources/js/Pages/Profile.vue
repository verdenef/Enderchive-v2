<template>
  <div 
    class="min-h-screen relative"
    style="background: linear-gradient(180deg, #0a0015 0%, #1c0031 50%, #250040 100%)"
  >
    <FloatingParticles />

    <div class="relative z-10">
      <!-- Sticky Navigation Bar -->
      <header 
        class="sticky top-0 z-50 h-20 border-b"
        style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
      >
        <div class="max-w-[1280px] mx-auto px-3 sm:px-4 md:px-8 h-full flex items-center gap-4">
          <!-- Logo -->
          <button @click="router.visit('/home')" class="flex items-center gap-2 md:gap-3 flex-shrink-0 hover:opacity-80 transition-opacity">
            <Icon name="lucide:gamepad-2" class="w-6 h-6 md:w-7 md:h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
            <h1 
              class="text-brand text-[#22c55e] hidden sm:block text-xs md:text-sm font-bold"
              style="text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em"
            >
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
              @click="router.visit('/profile')"
              class="flex items-center gap-2 px-2 sm:px-3 md:px-4 py-1.5 md:py-2 rounded-lg transition-all duration-200 cursor-pointer group font-medium"
              style="background: rgba(124, 58, 237, 0.2); border: 1px solid rgba(124, 58, 237, 0.4); color: white; min-height: 36px"
              @mouseenter="(e) => {
                e.currentTarget.style.background = 'rgba(124, 58, 237, 0.35)';
                e.currentTarget.style.boxShadow = '0 0 20px rgba(124, 58, 237, 0.5)';
              }"
              @mouseleave="(e) => {
                e.currentTarget.style.background = 'rgba(124, 58, 237, 0.2)';
                e.currentTarget.style.boxShadow = 'none';
              }"
            >
              <Icon name="lucide:user" class="w-4 h-4 transition-colors flex-shrink-0" style="color: #a78bfa" />
              <span class="text-xs md:text-sm whitespace-nowrap hidden sm:inline">
                {{ username }}
              </span>
            </button>
            <button
              v-if="isAdmin"
              @click="router.visit('/admin')"
              class="relative p-2 rounded-lg transition-all duration-200 cursor-pointer group"
              style="background: rgba(124, 58, 237, 0.2); border: 1px solid rgba(124, 58, 237, 0.4); min-width: 36px; min-height: 36px"
              @mouseenter="(e) => {
                e.currentTarget.style.background = 'rgba(124, 58, 237, 0.35)';
                e.currentTarget.style.boxShadow = '0 0 20px rgba(124, 58, 237, 0.5)';
              }"
              @mouseleave="(e) => {
                e.currentTarget.style.background = 'rgba(124, 58, 237, 0.2)';
                e.currentTarget.style.boxShadow = 'none';
              }"
            >
              <Icon name="lucide:shield" class="w-5 h-5 transition-colors" style="color: white" />
            </button>
          </div>

          <!-- Mobile Hamburger Menu -->
          <MobileNav
            current-page="profile"
            :username="username"
            :is-admin="isAdmin"
            :show-notifications="true"
            :notification-count="notifications.filter(n => !n.read).length"
            @notifications-click="() => {}"
            @profile-click="() => {}"
            @admin-click="() => router.visit('/admin')"
          />
        </div>
      </header>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-[360px_1fr] gap-4 sm:gap-6">
          <!-- LEFT COLUMN -->
          <div class="space-y-6">
            <!-- Profile Card -->
            <div class="glass-panel rounded-2xl p-6">
              <!-- Avatar -->
              <div class="flex flex-col items-center mb-6">
                <div
                  class="w-32 h-32 rounded-full mb-4 flex items-center justify-center overflow-hidden"
                  :style="{
                    background: avatarUrl ? 'transparent' : 'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)',
                    border: '4px solid rgba(124, 58, 237, 0.5)',
                    boxShadow: '0 0 30px rgba(124, 58, 237, 0.5)'
                  }"
                >
                  <img v-if="avatarUrl" :src="avatarUrl" alt="Avatar" class="w-full h-full object-cover" />
                  <span v-else class="text-5xl">
                    {{ (displayName || handle || username).charAt(0).toUpperCase() }}
                  </span>
                </div>
                <h2 class="mb-1">{{ displayName }}</h2>
                <p class="text-xs text-[#9ca3af] mb-1">@{{ handle }}</p>
                <p class="text-xs text-gray-400">
                  Member since {{ memberSinceText || formatMemberSince(memberSince) || '—' }}
                </p>
                <input
                  ref="fileInputRef"
                  type="file"
                  accept="image/*"
                  @change="handleAvatarChange"
                  class="hidden"
                />
                <div class="mt-4">
                  <GameButton
                    variant="outline"
                    :full-width="true"
                    @click="handleAvatarClick"
                  >
                    <template #icon>
                      <Icon name="lucide:upload" class="w-4 h-4" />
                    </template>
                    Upload Avatar
                  </GameButton>
                </div>
              </div>
            </div>

            <!-- Gaming Stats Card -->
            <div class="glass-panel rounded-2xl p-6">
              <h3 class="mb-4">Your Stats</h3>
              <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 rounded-lg" style="background: rgba(124, 58, 237, 0.1)">
                  <Icon name="lucide:library" class="w-6 h-6 mx-auto mb-2" style="color: #8b5cf6" />
                  <div class="text-2xl mb-1">{{ stats.gamesOwned }}</div>
                  <div class="text-xs text-gray-400">Games Owned</div>
                </div>
                <div class="text-center p-3 rounded-lg" style="background: rgba(34, 197, 94, 0.1)">
                  <Icon name="lucide:star" class="w-6 h-6 mx-auto mb-2" style="color: #22c55e" />
                  <div class="text-2xl mb-1">{{ stats.gamesCompleted }}</div>
                  <div class="text-xs text-gray-400">Completed</div>
                </div>
                <div class="text-center p-3 rounded-lg" style="background: rgba(139, 92, 246, 0.1)">
                  <Icon name="lucide:clock" class="w-6 h-6 mx-auto mb-2" style="color: #a78bfa" />
                  <div class="text-2xl mb-1">{{ stats.totalPlaytime }}h</div>
                  <div class="text-xs text-gray-400">Playtime</div>
                </div>
                <div class="text-center p-3 rounded-lg" style="background: rgba(245, 158, 11, 0.1)">
                  <Icon name="lucide:message-square" class="w-6 h-6 mx-auto mb-2" style="color: #f59e0b" />
                  <div class="text-2xl mb-1">{{ stats.reviewsWritten }}</div>
                  <div class="text-xs text-gray-400">Reviews</div>
                </div>
              </div>
            </div>


            <!-- Quick Links -->
            <div class="glass-panel rounded-2xl p-6">
              <h3 class="mb-4">Quick Links</h3>
              <div class="space-y-3">
                <GameButton
                  variant="outline"
                  :full-width="true"
                  @click="router.visit('/library')"
                >
                  <template #icon>
                    <Icon name="lucide:library" class="w-4 h-4" />
                  </template>
                  View Library
                </GameButton>
                <GameButton
                  variant="outline"
                  :full-width="true"
                  @click="router.visit('/wishlist')"
                >
                  <template #icon>
                    <Icon name="lucide:heart" class="w-4 h-4" />
                  </template>
                  View Wishlist
                </GameButton>
                <GameButton
                  variant="outline"
                  :full-width="true"
                  @click="router.visit('/settings')"
                >
                  <template #icon>
                    <Icon name="lucide:settings" class="w-4 h-4" />
                  </template>
                  Settings
                </GameButton>
              </div>
            </div>

            <!-- Account Settings Card -->
            <div class="glass-panel rounded-2xl p-6">
              <h2 class="mb-6">Account Settings</h2>
              
              <div class="space-y-4">
                <GameButton
                  variant="secondary"
                  :full-width="true"
                  @click="showChangePassword = true"
                >
                  Change Password
                </GameButton>
                
                <!-- Admin Access Button -->
                <GameButton
                  v-if="isAdmin"
                  variant="secondary"
                  :full-width="true"
                  @click="router.visit('/admin')"
                >
                  <template #icon>
                    <Icon name="lucide:shield" class="w-4 h-4" />
                  </template>
                  Admin Dashboard
                </GameButton>
                
                <GameButton
                  variant="secondary"
                  :full-width="true"
                  @click="showLogoutConfirm = true"
                >
                  <template #icon>
                    <Icon name="lucide:log-out" class="w-4 h-4" />
                  </template>
                  Logout
                </GameButton>
                
                <GameButton
                  variant="danger"
                  :full-width="true"
                  @click="showDeleteAccount = true"
                >
                  Delete Account
                </GameButton>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN -->
          <div class="space-y-6">
            <!-- Analytics Dashboard -->
            <AnalyticsDashboard
              :total-playtime="stats.totalPlaytime"
              :games-owned="stats.gamesOwned"
              :games-completed="stats.gamesCompleted"
              :completion-rate="completionRate"
              :favorite-genre="favoriteGenre"
              :favorite-platform="favoritePlatform"
              :genre-data="genreBreakdown"
              :platform-data="platformBreakdown"
              :playtime-series="playtimeSeries"
            />

            <!-- Edit Profile Form -->
            <div class="glass-panel rounded-2xl p-6">
              <h2 class="mb-6">Profile Information</h2>
              
              <div class="space-y-4 max-w-md">
                <div>
                  <label class="block text-xs text-gray-400 mb-2">Display name</label>
                  <GameInput
                    type="text"
                    v-model="displayName"
                    @input="isEditing = true"
                  />
                </div>

                <div>
                  <label class="block text-xs text-gray-400 mb-2">Username</label>
                  <div class="flex gap-2 items-center">
                    <div class="flex-1">
                      <GameInput
                        type="text"
                        v-model="handle"
                        :disabled="!canEditUsername"
                      />
                    </div>
                    <GameButton
                      variant="outline"
                      size="sm"
                      :disabled="!canEditUsername || handle === originalHandle"
                      @click="handleChangeUsername"
                    >
                      <template #icon>
                        <Icon name="lucide:save" class="w-4 h-4" />
                      </template>
                    </GameButton>
                  </div>
                  <p class="mt-1 text-[11px] text-gray-500">
                    {{ canEditUsername ? 'You can change your username once. Choose carefully.' : 'You have already used your one-time username change.' }}
                  </p>
                </div>

                <div>
                  <label class="block text-xs text-gray-400 mb-2">Email</label>
                  <GameInput
                    type="email"
                    v-model="email"
                    @input="isEditing = true"
                  />
                </div>

                <div>
                  <label class="block text-xs text-gray-400 mb-2">Bio</label>
                  <textarea
                    class="w-full px-4 py-3 rounded-lg text-sm resize-vertical"
                    style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; min-height: 120px"
                    placeholder="Tell us about yourself..."
                    v-model="bio"
                    @input="isEditing = true"
                  />
                </div>

                <div v-if="isEditing" class="flex gap-3 pt-2">
                  <GameButton variant="primary" @click="handleSaveProfile">
                    Save Changes
                  </GameButton>
                  <GameButton variant="outline" @click="isEditing = false">
                    Cancel
                  </GameButton>
                </div>
              </div>
            </div>

            <!-- Site Achievements -->
            <SiteAchievements :stats="stats" />
          </div>
        </div>
      </div>
    </div>

    <!-- Change Password Modal -->
    <div 
      v-if="showChangePassword"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4"
      style="background: rgba(0, 0, 0, 0.8)"
      @click="showChangePassword = false"
    >
      <div
        class="glass-panel rounded-lg p-6 border-4 border-[#2b1d3a] max-w-md w-full"
        style="box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
        @click.stop
      >
        <div class="flex items-center gap-3 mb-6">
          <Icon name="lucide:key" class="w-6 h-6 text-[#22c55e]" />
          <h2>Change Password</h2>
        </div>
        <p class="text-white/80 mb-4">Password change functionality coming soon.</p>
        <GameButton variant="primary" :full-width="true" @click="showChangePassword = false">
          Close
        </GameButton>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div 
      v-if="showLogoutConfirm"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4"
      style="background: rgba(0, 0, 0, 0.8)"
      @click="showLogoutConfirm = false"
    >
      <div
        class="glass-panel rounded-lg p-6 border-4 border-[#2b1d3a] max-w-md w-full"
        style="box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
        @click.stop
      >
        <div class="flex items-center gap-3 mb-6">
          <Icon name="lucide:log-out" class="w-6 h-6 text-[#f59e0b]" />
          <h2>Confirm Logout</h2>
        </div>
        <p class="text-white/80 mb-6">Are you sure you want to logout?</p>
        <div class="flex gap-3">
          <GameButton
            variant="secondary"
            :full-width="true"
            @click="showLogoutConfirm = false"
          >
            Cancel
          </GameButton>
          <GameButton
            variant="primary"
            :full-width="true"
            @click="handleLogout"
          >
            Logout
          </GameButton>
        </div>
      </div>
    </div>

    <!-- Delete Account Modal -->
    <div 
      v-if="showDeleteAccount"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4"
      style="background: rgba(0, 0, 0, 0.8)"
      @click="showDeleteAccount = false"
    >
      <div
        class="glass-panel rounded-lg p-6 border-4 max-w-md w-full"
        style="border-color: rgba(239, 68, 68, 0.5); box-shadow: 0 0 40px rgba(239, 68, 68, 0.4)"
        @click.stop
      >
        <div class="flex items-center gap-3 mb-6">
          <Icon name="lucide:alert-triangle" class="w-6 h-6 text-red-500" />
          <h2 class="text-red-500">Delete Account</h2>
        </div>
        <p class="text-white/80 mb-4">Account deletion functionality coming soon.</p>
        <GameButton variant="danger" :full-width="true" @click="showDeleteAccount = false">
          Close
        </GameButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import FloatingParticles from '../Components/effects/FloatingParticles.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import AnalyticsDashboard from '../Components/profile/AnalyticsDashboard.vue'
import SiteAchievements from '../Components/profile/SiteAchievements.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import Icon from '../Components/ui/Icon.vue'
import axios from 'axios'
import { useNotifications } from '../Composables/useNotifications'

// Configure axios for Laravel Sanctum session-based auth
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const username = computed(() => user.value?.username || user.value?.name || 'Guest')
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)

const displayName = ref('')
const handle = ref('')
const originalHandle = ref('')
const email = ref('')
const bio = ref('')
const isEditing = ref(false)
const avatarUrl = ref(null)
const fileInputRef = ref(null)
const canEditUsername = ref(true)
const memberSince = ref('')
const memberSinceText = ref('')
const showChangePassword = ref(false)
const showDeleteAccount = ref(false)
const showLogoutConfirm = ref(false)

const passwordData = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Use global notifications
const { notifications, markAsRead: handleMarkAsRead, markAllAsRead: handleMarkAllAsRead, deleteNotification: handleDeleteNotification, loadNotifications, startNotificationPolling } = useNotifications(user)

// Notification handler
const handleNotificationClick = (notification) => {
  handleMarkAsRead(notification.id)
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}

const stats = ref({
  gamesOwned: 0,
  gamesCompleted: 0,
  totalPlaytime: 0,
  reviewsWritten: 0,
  friendsCount: 0
})

const genreBreakdown = ref([])
const platformBreakdown = ref([])
const playtimeSeries = ref([])

const completionRate = computed(() => {
  return stats.value.gamesOwned > 0
    ? Math.round((stats.value.gamesCompleted / stats.value.gamesOwned) * 100)
    : 0
})

// Compute favorite genre and platform from breakdown data
const favoriteGenre = computed(() => {
  if (genreBreakdown.value.length === 0) return 'N/A'
  // Return the genre with the highest percentage
  return genreBreakdown.value[0]?.name || 'N/A'
})

const favoritePlatform = computed(() => {
  if (platformBreakdown.value.length === 0) return 'N/A'
  // Return the platform with the highest percentage
  return platformBreakdown.value[0]?.name || 'N/A'
})

const formatMemberSince = (createdAt) => {
  if (!createdAt) return ''
  try {
  const d = new Date(createdAt)
    if (Number.isNaN(d.getTime())) {
      console.warn('Invalid date:', createdAt)
      return ''
    }
    // Format as "Month Year" (e.g., "Jan 2024")
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    const month = monthNames[d.getMonth()]
    const year = d.getFullYear()
    return `${month} ${year}`
  } catch (error) {
    console.error('Error formatting date:', error, createdAt)
    return ''
  }
}

// Initialize member since from user data if available
watch(user, (newUser) => {
  if (newUser?.created_at) {
    memberSince.value = newUser.created_at
    memberSinceText.value = formatMemberSince(newUser.created_at)
  }
}, { immediate: true })

onMounted(async () => {
  // Initialize notifications
  if (!isGuest.value) {
    loadNotifications()
    startNotificationPolling()
  }
  
  await loadProfile()
})

const loadProfile = async () => {
  try {
    if (user.value) {
      displayName.value = user.value.name || username.value || ''
      handle.value = user.value.username || username.value || ''
      originalHandle.value = user.value.username || username.value || ''
      email.value = user.value.email || ''
      bio.value = user.value.bio || ''
      if (user.value.avatar_url) {
        avatarUrl.value = user.value.avatar_url
      }
      canEditUsername.value = !user.value.username_changed_at
      
      // Set member since date
      const createdAt = user.value.created_at || user.value.createdAt || null
      if (createdAt) {
        memberSince.value = createdAt
        memberSinceText.value = formatMemberSince(createdAt)
      } else {
        memberSinceText.value = ''
      }
    }

    // Fetch all games (handle pagination)
    let allGames = []
    let currentPage = 1
    let lastPage = 1
    const token = localStorage.getItem('api_token')
    // Use token if available, otherwise rely on session cookies (Sanctum)
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    do {
      try {
        const gamesRes = await axios.get('/api/user/games', {
          params: { page: currentPage },
          headers
        })
        const gamesData = gamesRes.data?.data || gamesRes.data || []
        allGames = [...allGames, ...gamesData]
        lastPage = gamesRes.data?.meta?.last_page || gamesRes.data?.last_page || 1
        currentPage++
        // Safety check to prevent infinite loop
        if (currentPage > 100) {
          break
        }
      } catch (error) {
        console.error('Failed to load games page:', error)
        // If first page fails, don't try more pages
        if (currentPage === 1) {
          break
        }
        break
      }
    } while (currentPage <= lastPage)

    // Fetch friends
    let friendsRes
    try {
      friendsRes = await axios.get('/api/friends', { headers })
      friendsRes = friendsRes.data?.data ? friendsRes : { data: { data: friendsRes.data || [] } }
    } catch (error) {
      console.error('Failed to load friends:', error)
      friendsRes = { data: { data: [] } }
    }

    // Filter out wishlist games - only count games in library (Playing, Completed, Abandoned)
    const libraryGames = allGames.filter(g => {
      const status = (g.status || '').toLowerCase()
      return status !== 'wishlist'
    })
    
    const gamesOwned = libraryGames.length
    const gamesCompleted = libraryGames.filter(g => {
      const status = (g.status || '').toLowerCase()
      return status === 'completed'
    }).length
    const totalPlaytime = libraryGames.reduce((sum, g) => sum + (g.playtime_hours ?? 0), 0)
    const friendsCount = Array.isArray(friendsRes.data.data) ? friendsRes.data.data.length : 0
    
    // Count user's reviews - try to get from Review model if available
    // For now, we'll set it to 0 since there's no direct endpoint
    // In a real implementation, you'd add an endpoint like GET /api/user/reviews
    let reviewsWritten = 0
    try {
      // Try to count from games if reviews are loaded
      const gamesWithReviews = allGames.filter(g => g.game?.reviews && Array.isArray(g.game.reviews))
      if (gamesWithReviews.length > 0) {
        reviewsWritten = gamesWithReviews.reduce((sum, g) => {
          const userReviews = g.game.reviews.filter(r => r.user_id === user.value?.user_id)
          return sum + userReviews.length
        }, 0)
      }
    } catch (error) {
      console.warn('Failed to count reviews:', error)
    }

    stats.value = {
      gamesOwned,
      gamesCompleted,
      totalPlaytime,
      friendsCount,
      reviewsWritten
    }

    // Genre breakdown - game.genre is singular (belongsTo relationship)
    const genreCounts = {}
    libraryGames.forEach(g => {
      const game = g.game || {}
      // Genre is singular, so wrap in array if it exists
      if (game.genre) {
        const genreName = game.genre.name || game.genre.title || game.genre
        if (genreName) {
          genreCounts[genreName] = (genreCounts[genreName] || 0) + 1
        }
      }
    })
    const totalGenreCount = Object.values(genreCounts).reduce((sum, v) => sum + v, 0)
    const genreColors = ['#22c55e', '#8b5cf6', '#f59e0b', '#ef4444', '#6b7280']
    genreBreakdown.value = Object.entries(genreCounts)
      .sort((a, b) => b[1] - a[1])
      .map(([name, count], index) => ({
        name,
        value: totalGenreCount > 0 ? Math.round((count / totalGenreCount) * 100) : 0,
        color: genreColors[index % genreColors.length]
      }))

    // Platform breakdown
    const platformCounts = {}
    libraryGames.forEach(g => {
      const game = g.game || {}
      const platforms = game.platforms || []
      platforms.forEach(platform => {
        const platformName = platform.name || platform.title || platform
        if (platformName) {
          platformCounts[platformName] = (platformCounts[platformName] || 0) + 1
        }
      })
    })
    const totalPlatformCount = Object.values(platformCounts).reduce((sum, v) => sum + v, 0)
    const platformColors = ['#22c55e', '#8b5cf6', '#f59e0b', '#ef4444']
    platformBreakdown.value = Object.entries(platformCounts)
      .sort((a, b) => b[1] - a[1])
      .map(([name, count], index) => ({
        name,
        value: totalPlatformCount > 0 ? Math.round((count / totalPlatformCount) * 100) : 0,
        color: platformColors[index % platformColors.length]
      }))

    // Playtime series
    playtimeSeries.value = libraryGames
      .filter(g => (g.playtime_hours ?? 0) > 0)
      .sort((a, b) => (b.playtime_hours ?? 0) - (a.playtime_hours ?? 0))
      .slice(0, 20)
      .map(g => {
        const game = g.game || {}
        return {
          label: game.title || game.name || `Game #${g.user_game_id || g.id}`,
          hours: g.playtime_hours ?? 0
        }
      })

  } catch (error) {
    console.error('Failed to load profile data', error)
  }
}

const handleSaveProfile = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    const response = await axios.put('/api/user/profile', {
      name: displayName.value,
      email: email.value,
      bio: bio.value
    }, {
      headers
    })
    
    isEditing.value = false
    
    // Reload user data
    router.reload({ only: ['auth'] })
    
    // Show success message (you might want to use a toast here)
    alert('Profile updated successfully!')
  } catch (error) {
    console.error('Failed to save profile', error)
    if (error.response) {
      alert(error.response?.data?.message || 'Failed to save profile. Please try again.')
    } else {
      alert('Failed to save profile. Please check your connection and try again.')
    }
  }
}

const handleChangePassword = async () => {
  if (passwordData.value.newPassword !== passwordData.value.confirmPassword) {
    alert('New passwords do not match!')
    return
  }
  if (passwordData.value.newPassword.length < 8) {
    alert('Password must be at least 8 characters long!')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.put('/api/user/password', {
      current_password: passwordData.value.currentPassword,
      new_password: passwordData.value.newPassword,
      confirm_password: passwordData.value.confirmPassword
    }, { headers })
    
    alert('Password changed successfully!')
    passwordData.value = { currentPassword: '', newPassword: '', confirmPassword: '' }
    showChangePassword.value = false
  } catch (error) {
    console.error('Failed to change password:', error)
    const errorMessage = error.response?.data?.message || 'Failed to change password. Please check your current password.'
    alert(errorMessage)
  }
}

const handleChangeUsername = async () => {
  if (handle.value === originalHandle.value) {
    alert('Username unchanged')
    return
  }
  if (!canEditUsername.value) {
    alert('You can only change your username once.')
    return
  }
  if (!handle.value || handle.value.trim() === '') {
    alert('Username cannot be empty')
    return
  }
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.put('/api/user/username', { 
      username: handle.value.trim() 
    }, {
      headers
    })
    originalHandle.value = handle.value
    canEditUsername.value = false
    alert('Username updated successfully!')
    router.reload({ only: ['auth'] })
  } catch (error) {
    console.error('Failed to update username:', error)
    if (error.response) {
      alert(error.response?.data?.message || 'Failed to update username. Please try again.')
    } else {
      alert('Failed to update username. Please check your connection and try again.')
    }
  }
}

const handleAvatarClick = () => {
  fileInputRef.value?.click()
}

const handleAvatarChange = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    if (!file.type.startsWith('image/')) {
      alert('Please select an image file')
      return
    }
    if (file.size > 5 * 1024 * 1024) {
      alert('Image size should be less than 5MB')
      return
    }
    const reader = new FileReader()
    reader.onloadend = () => {
      avatarUrl.value = reader.result
    }
    reader.readAsDataURL(file)
  }
}

const handleLogout = async () => {
  // Close the confirmation modal
  showLogoutConfirm.value = false
  try {
    // Clear local storage first
    localStorage.removeItem('api_token')
    localStorage.clear()
    
    // Use Inertia's post method for web logout (session-based)
    router.post('/logout', {}, {
      onSuccess: () => {
        router.visit('/login')
      },
      onError: () => {
        router.visit('/login')
      },
      onFinish: () => {
        // Ensure redirect even if post fails
    router.visit('/login')
      }
    })
  } catch (error) {
    console.error('Logout error:', error)
    // Ensure cleanup even on error
    localStorage.removeItem('api_token')
    localStorage.clear()
    router.visit('/login')
  }
}

// Notification handlers are provided by useNotifications composable
</script>
