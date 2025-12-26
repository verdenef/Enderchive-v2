<template>
  <AppLayout>
    <div class="min-h-screen relative">
      <div class="relative z-10">
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto px-4 md:px-8 h-full flex items-center justify-between gap-2">
            <button @click="router.visit('/home')" class="flex items-center gap-2 md:gap-3 flex-shrink-0">
              <Icon name="lucide:gamepad-2" class="w-6 h-6 md:w-7 md:h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
              <h1 class="text-brand text-[#22c55e] hidden sm:block" style="font-size: 12px; md:font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em">
                Enderchive Admin
              </h1>
            </button>

            <!-- Admin Navigation Tabs - Hidden on mobile -->
            <nav class="hidden md:flex gap-2 flex-1 justify-center">
              <NavigationTab
                label="Dashboard"
                :active="activeTab === 'dashboard'"
                @click="activeTab = 'dashboard'"
              >
                <template #icon>
                  <Icon name="lucide:activity" class="w-5 h-5" />
                </template>
              </NavigationTab>
              <NavigationTab
                label="Users"
                :active="activeTab === 'users'"
                @click="activeTab = 'users'"
              >
                <template #icon>
                  <Icon name="lucide:users" class="w-5 h-5" />
                </template>
              </NavigationTab>
            </nav>

            <!-- Mobile Tab Switcher -->
            <div class="md:hidden flex gap-1 flex-1 justify-center">
              <button
                @click="activeTab = 'dashboard'"
                :class="[
                  'px-3 py-1.5 rounded text-xs font-medium transition-all',
                  activeTab === 'dashboard' 
                    ? 'bg-[#22c55e]/20 border-2 border-[#22c55e] text-[#22c55e]' 
                    : 'bg-white/5 border-2 border-transparent text-white/60'
                ]"
              >
                Dashboard
              </button>
              <button
                @click="activeTab = 'users'"
                :class="[
                  'px-3 py-1.5 rounded text-xs font-medium transition-all',
                  activeTab === 'users' 
                    ? 'bg-[#22c55e]/20 border-2 border-[#22c55e] text-[#22c55e]' 
                    : 'bg-white/5 border-2 border-transparent text-white/60'
                ]"
              >
                Users
              </button>
            </div>

            <!-- Back to User Button - Hidden on mobile, shown on desktop -->
            <button
              @click="router.visit('/home')"
              class="hidden md:flex px-3 py-1 rounded text-sm flex-shrink-0"
              style="background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.3); color: white"
            >
              Back to User
            </button>

            <!-- Mobile Nav -->
            <MobileNav
              current-page="admin"
              :username="user?.name || 'Admin'"
              :is-admin="true"
            />
          </div>
        </header>

        <main class="max-w-[1280px] mx-auto px-4 md:px-8 pt-4 md:pt-8 pb-8 md:pb-16">
          <h1 class="mb-4 md:mb-8 text-xl md:text-2xl font-bold">
            {{ activeTab === 'dashboard' ? 'Admin Dashboard' : 'User Management' }}
          </h1>

          <!-- Dashboard Tab -->
          <div v-if="activeTab === 'dashboard'">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
              <div class="glass-panel rounded-xl md:rounded-2xl p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                  <Icon name="lucide:users" class="w-6 h-6 md:w-8 md:h-8" style="color: #8b5cf6" />
                </div>
                <div class="text-2xl md:text-3xl font-bold mb-1">
                  {{ stats ? stats.totalUsers.toLocaleString() : '—' }}
                </div>
                <div class="text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">Total Users</div>
              </div>

              <div class="glass-panel rounded-xl md:rounded-2xl p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                  <Icon name="lucide:gamepad-2" class="w-6 h-6 md:w-8 md:h-8" style="color: #22c55e" />
                </div>
                <div class="text-2xl md:text-3xl font-bold mb-1">
                  {{ stats ? stats.distinctGames.toLocaleString() : '—' }}
                </div>
                <div class="text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">Distinct Games</div>
              </div>

              <div class="glass-panel rounded-xl md:rounded-2xl p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                  <Icon name="lucide:star" class="w-6 h-6 md:w-8 md:h-8" style="color: #f59e0b" />
                </div>
                <div class="text-2xl md:text-3xl font-bold mb-1">
                  {{ stats ? stats.totalReviews.toLocaleString() : '—' }}
                </div>
                <div class="text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">Total Reviews</div>
              </div>

              <div class="glass-panel rounded-lg md:rounded-lg p-4 md:p-6 border-2 md:border-4" style="border-color: rgba(43, 29, 58, 0.7); box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2)">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                  <Icon name="lucide:activity" class="w-6 h-6 md:w-8 md:h-8" style="color: #3b82f6" />
                </div>
                <div class="text-2xl md:text-3xl font-bold mb-1">
                  {{ stats ? stats.totalUserGames.toLocaleString() : '—' }}
                </div>
                <div class="text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">Library Entries</div>
              </div>
            </div>

            <div v-if="loadingStats" class="text-sm mb-4" style="color: rgba(255, 255, 255, 0.6)">
              Loading admin stats…
            </div>
            <div v-if="statsError" class="text-sm mb-4" style="color: #ef4444">
              {{ statsError }}
            </div>

            <!-- Recent Activity -->
            <div class="glass-panel rounded-xl md:rounded-2xl p-4 md:p-6 mb-6 md:mb-8">
              <h2 class="mb-3 md:mb-4 text-lg md:text-xl font-bold">Recent Activity</h2>
              <div v-if="activityError" class="text-sm mb-2" style="color: #ef4444">
                {{ activityError }}
              </div>
              <div v-else class="space-y-4">
                <!-- New Accounts -->
                <div>
                  <h3 class="text-xs font-semibold mb-2 uppercase tracking-wide" style="color: rgba(255, 255, 255, 0.6)">
                    New Accounts
                  </h3>
                  <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
                    <table class="w-full text-xs md:text-sm min-w-full">
                      <thead>
                        <tr class="border-b text-xs" style="border-color: rgba(43, 29, 58, 0.7); color: rgba(255, 255, 255, 0.6)">
                          <th class="text-left py-2 px-2 md:px-3">User</th>
                          <th class="text-left py-2 px-2 md:px-3">When</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="activity.filter(a => a.type === 'user').length === 0">
                          <td class="py-2 px-2 md:px-3" style="color: rgba(255, 255, 255, 0.6)" colspan="2">
                            No recent registrations.
                          </td>
                        </tr>
                        <tr 
                          v-for="(item, idx) in activity.filter(a => a.type === 'user')" 
                          :key="`user-${item.user}-${idx}`"
                          class="border-b hover:bg-white/5"
                          style="border-color: rgba(43, 29, 58, 0.7)"
                        >
                          <td class="py-2 px-2 md:px-3">{{ item.user }}</td>
                          <td class="py-2 px-2 md:px-3 text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">{{ item.time_ago ?? '—' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Library Updates -->
                <div>
                  <h3 class="text-xs font-semibold mb-2 uppercase tracking-wide" style="color: rgba(255, 255, 255, 0.6)">
                    Library Updates
                  </h3>
                  <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
                    <table class="w-full text-xs md:text-sm min-w-full">
                      <thead>
                        <tr class="border-b text-xs" style="border-color: rgba(43, 29, 58, 0.7); color: rgba(255, 255, 255, 0.6)">
                          <th class="text-left py-2 px-2 md:px-3">User</th>
                          <th class="text-left py-2 px-2 md:px-3">Details</th>
                          <th class="text-left py-2 px-2 md:px-3 hidden sm:table-cell">When</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="activity.filter(a => a.type === 'library').length === 0">
                          <td class="py-2 px-2 md:px-3" style="color: rgba(255, 255, 255, 0.6)" colspan="3">
                            No recent library changes.
                          </td>
                        </tr>
                        <tr 
                          v-for="(item, idx) in activity.filter(a => a.type === 'library')" 
                          :key="`lib-${item.user}-${idx}`"
                          class="border-b hover:bg-white/5"
                          style="border-color: rgba(43, 29, 58, 0.7)"
                        >
                          <td class="py-2 px-2 md:px-3">{{ item.user }}</td>
                          <td class="py-2 px-2 md:px-3 text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">
                            {{ item.context ? `Game ID ${item.context}` : 'Library updated' }}
                            <span v-if="item.status"> • {{ item.status }}</span>
                            <span v-if="item.time_ago" class="sm:hidden block text-xs mt-1">{{ item.time_ago }}</span>
                          </td>
                          <td class="py-2 px-2 md:px-3 text-xs md:text-sm hidden sm:table-cell" style="color: rgba(255, 255, 255, 0.6)">{{ item.time_ago ?? '—' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Reviews -->
                <div>
                  <h3 class="text-xs font-semibold mb-2 uppercase tracking-wide" style="color: rgba(255, 255, 255, 0.6)">
                    Reviews
                  </h3>
                  <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
                    <table class="w-full text-xs md:text-sm min-w-full">
                      <thead>
                        <tr class="border-b text-xs" style="border-color: rgba(43, 29, 58, 0.7); color: rgba(255, 255, 255, 0.6)">
                          <th class="text-left py-2 px-2 md:px-3">User</th>
                          <th class="text-left py-2 px-2 md:px-3">Details</th>
                          <th class="text-left py-2 px-2 md:px-3 hidden sm:table-cell">When</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="activity.filter(a => a.type === 'review').length === 0">
                          <td class="py-2 px-2 md:px-3" style="color: rgba(255, 255, 255, 0.6)" colspan="3">
                            No recent reviews.
                          </td>
                        </tr>
                        <tr 
                          v-for="(item, idx) in activity.filter(a => a.type === 'review')" 
                          :key="`rev-${item.user}-${idx}`"
                          class="border-b hover:bg-white/5"
                          style="border-color: rgba(43, 29, 58, 0.7)"
                        >
                          <td class="py-2 px-2 md:px-3">{{ item.user }}</td>
                          <td class="py-2 px-2 md:px-3 text-xs md:text-sm" style="color: rgba(255, 255, 255, 0.6)">
                            {{ item.context ? `Game ID ${item.context}` : 'Review' }}
                            <span v-if="typeof item.rating === 'number'"> • Rating {{ item.rating }}</span>
                            <span v-if="item.time_ago" class="sm:hidden block text-xs mt-1">{{ item.time_ago }}</span>
                          </td>
                          <td class="py-2 px-2 md:px-3 text-xs md:text-sm hidden sm:table-cell" style="color: rgba(255, 255, 255, 0.6)">{{ item.time_ago ?? '—' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Users Tab -->
          <div v-if="activeTab === 'users'">
            <div class="glass-panel rounded-xl md:rounded-2xl p-4 md:p-6 mb-6 md:mb-8">
              <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 md:mb-4 gap-2">
                <h2 class="text-lg md:text-xl font-bold">Users</h2>
                <span class="text-xs" style="color: rgba(255, 255, 255, 0.6)">All registered accounts</span>
              </div>
              <div v-if="loadingUsers" class="text-sm mb-2" style="color: rgba(255, 255, 255, 0.6)">
                Loading users…
              </div>
              <div v-if="usersError" class="text-sm mb-2" style="color: #ef4444">
                {{ usersError }}
              </div>
              <div v-if="!loadingUsers && !usersError" class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
                <table class="w-full text-xs md:text-sm min-w-[600px]">
                  <thead>
                    <tr class="border-b text-xs" style="border-color: rgba(43, 29, 58, 0.7); color: rgba(255, 255, 255, 0.6)">
                      <th class="text-left py-2 md:py-3 px-2 md:px-4">ID</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4">Username</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4 hidden sm:table-cell">Display Name</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4 hidden md:table-cell">Email</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4">Library</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4">Completed</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4">Wishlist</th>
                      <th class="text-left py-2 md:py-3 px-2 md:px-4">Reviews</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="users.length === 0">
                      <td class="py-3 px-4" style="color: rgba(255, 255, 255, 0.6)" colspan="8">
                        No users found.
                      </td>
                    </tr>
                    <tr 
                      v-for="u in users" 
                      :key="u.id"
                      class="border-b hover:bg-white/5"
                      style="border-color: rgba(43, 29, 58, 0.7)"
                    >
                      <td class="py-2 md:py-3 px-2 md:px-4 text-xs" style="color: rgba(255, 255, 255, 0.6)">{{ u.id }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4">{{ u.username }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4 hidden sm:table-cell">{{ u.name }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4 text-xs hidden md:table-cell" style="color: rgba(255, 255, 255, 0.6)">{{ u.email }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4">{{ u.library_entries }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4">{{ u.completed_games }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4">{{ u.wishlist_games }}</td>
                      <td class="py-2 md:py-3 px-2 md:px-4">{{ u.reviews_count }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import Icon from '../Components/ui/Icon.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import axios from 'axios'

const page = usePage()

const user = computed(() => page.props.auth?.user)
const isAdmin = computed(() => user.value?.is_admin === true)

const activeTab = ref('dashboard')

const stats = ref(null)
const loadingStats = ref(false)
const statsError = ref('')

const users = ref([])
const loadingUsers = ref(false)
const usersError = ref('')
const hasLoadedUsers = ref(false)

const activity = ref([])
const activityError = ref('')

onMounted(async () => {
  if (!isAdmin.value) {
    statsError.value = 'Access denied. You must be logged in as admin@example.com to access this page.'
    return
  }
  
  await loadSummary()
  await loadActivity()
})

// Lazy-load users when Users tab is first opened
watch(activeTab, async (newTab) => {
  if (newTab === 'users' && !hasLoadedUsers.value) {
    await loadUsers()
  }
})

const loadSummary = async () => {
  try {
    loadingStats.value = true
    statsError.value = ''
    const response = await axios.get('/api/admin/summary')
    if (response.data && response.data.data) {
      stats.value = {
        totalUsers: response.data.data.total_users || 0,
        totalUserGames: response.data.data.total_user_games || 0,
        distinctGames: response.data.data.distinct_games || 0,
        totalReviews: response.data.data.total_reviews || 0,
      }
    }
  } catch (error) {
    console.error('Failed to load summary:', error)
    statsError.value = `Failed to load summary: ${error.response?.data?.message || error.message}`
  } finally {
    loadingStats.value = false
  }
}

const loadUsers = async () => {
  try {
    loadingUsers.value = true
    usersError.value = ''
    const response = await axios.get('/api/admin/users')
    if (response.data && response.data.data) {
      users.value = response.data.data || []
      hasLoadedUsers.value = true
    } else {
      users.value = []
    }
  } catch (error) {
    console.error('Failed to load users:', error)
    usersError.value = `Failed to load users: ${error.response?.data?.message || error.message}`
  } finally {
    loadingUsers.value = false
  }
}

const loadActivity = async () => {
  try {
    activityError.value = ''
    const response = await axios.get('/api/admin/activity')
    if (response.data && response.data.data) {
      activity.value = response.data.data || []
    } else {
      activity.value = []
    }
  } catch (error) {
    console.error('Failed to load activity:', error)
    activityError.value = 'Failed to load recent activity'
  }
}
</script>
