<template>
  <AppLayout>
    <div class="min-h-screen relative">
      <div class="relative z-10">
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: rgba(10, 0, 21, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(43, 29, 58, 0.5)"
        >
          <div class="max-w-[1280px] mx-auto px-8 h-full flex items-center gap-4">
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
                :active="true"
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
                <span class="text-sm">{{ username }}</span>
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
              current-page="friends"
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

        <!-- Main Content -->
        <main class="max-w-[1280px] mx-auto px-8 pb-16 pt-8">
          <div class="mb-8">
            <h2 class="text-3xl font-bold mb-4">Friends</h2>
            
            <!-- Tabs -->
            <div class="flex gap-2 mb-6 border-b border-[#2b1d3a] pb-2">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                :class="[
                  'px-4 py-2 rounded-t-lg text-sm transition-all',
                  activeTab === tab.id
                    ? 'bg-[#5b21b6] text-white'
                    : 'bg-transparent text-gray-400 hover:text-white'
                ]"
                :style="{
                  boxShadow: activeTab === tab.id ? '0 0 20px rgba(91, 33, 182, 0.5)' : 'none'
                }"
                @click="handleTabClick(tab.id)"
              >
                <div class="flex items-center gap-2">
                  <Icon :name="tab.icon" class="w-4 h-4" />
                {{ tab.label }}
                  <span
                    v-if="tab.count && tab.count > 0"
                    class="px-2 py-0.5 rounded-full text-xs"
                    :style="{
                      background: tab.id === 'friends' ? '#15803d' : 'rgba(139, 92, 246, 0.3)',
                      color: tab.id === 'friends' ? 'white' : '#a78bfa'
                    }"
                  >
                    {{ tab.count }}
                  </span>
                </div>
              </button>
            </div>
          </div>

          <!-- Friends List -->
          <div v-if="activeTab === 'friends'" class="space-y-6">
            <!-- Search Bar -->
            <div class="flex gap-2">
              <div class="flex-1">
                <GameInput
                  v-model="friendsSearchQuery"
                  type="text"
                  placeholder="Search friends..."
                  @keyup.enter="() => {}"
                />
              </div>
              <button
                @click="() => {}"
                class="flex items-center justify-center transition-all duration-200 hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                style="background: transparent; border: none; color: white; height: 48px; width: 48px; padding: 0; margin-top: 1px;"
              >
                <Icon name="lucide:search" class="w-6 h-6" />
              </button>
            </div>

            <div v-if="filteredFriends.length === 0" class="text-center py-12">
              <div
                class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                style="background: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3)"
              >
                <Icon name="lucide:users" class="w-8 h-8" style="color: #22c55e" />
              </div>
              <h3 class="mb-2">{{ friendsSearchQuery.trim() ? 'No friends found' : 'No friends yet' }}</h3>
              <p class="text-sm text-gray-400">{{ friendsSearchQuery.trim() ? 'Try a different search term' : 'Start adding friends to see them here' }}</p>
            </div>
            <div v-else>
              <FriendCard
                v-for="friend in filteredFriends"
              :key="friend.friend_id"
                :username="friend.friendUser?.username || friend.friendUser?.name || 'Unknown'"
                :status="friend.friendUser?.status || 'Offline'"
                :friend-since="formatFriendSince(friend.created_at)"
              @click="viewProfile(friend.friendUser?.user_id || friend.friend_user_id)"
                @remove="removeFriend(friend.friend_id)"
              />
            </div>
          </div>

          <!-- Friend Requests -->
          <div v-else-if="activeTab === 'requests'" class="space-y-6">
            <!-- Requests Sub-tabs -->
            <div class="flex gap-2 mb-4">
              <button
                @click="requestsTab = 'pending'"
                :class="[
                  'px-3 py-1.5 rounded-lg text-sm transition-all font-medium',
                  requestsTab === 'pending'
                    ? 'bg-purple-600 text-white'
                    : 'bg-transparent text-gray-400 hover:text-white hover:bg-white/5'
                ]"
              >
                <div class="flex items-center gap-2">
                  <Icon name="lucide:user-plus" class="w-4 h-4" />
                  Pending Requests
                  <span
                    v-if="incomingRequests.length > 0"
                    class="px-1.5 py-0.5 rounded-full text-xs font-bold"
                    style="background: #15803d; color: white"
                  >
                    {{ incomingRequests.length }}
                  </span>
                </div>
              </button>
              <button
                @click="requestsTab = 'sent'"
                  :class="[
                  'px-3 py-1.5 rounded-lg text-sm transition-all font-medium',
                  requestsTab === 'sent'
                    ? 'bg-purple-600 text-white'
                    : 'bg-transparent text-gray-400 hover:text-white hover:bg-white/5'
                ]"
              >
                <div class="flex items-center gap-2">
                  <Icon name="lucide:send" class="w-4 h-4" />
                  Sent Requests
                  <span
                    v-if="sentRequests.length > 0"
                    class="px-1.5 py-0.5 rounded-full text-xs font-bold"
                    style="background: rgba(139, 92, 246, 0.3); color: #a78bfa"
                >
                    {{ sentRequests.length }}
                  </span>
                </div>
              </button>
            </div>

            <!-- Pending Requests Tab -->
            <div v-if="requestsTab === 'pending'" class="space-y-4">
              <div v-if="incomingRequests.length === 0" class="text-center py-12">
                <div
                  class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                  style="background: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3)"
                >
                  <Icon name="lucide:user-check" class="w-8 h-8" style="color: #22c55e" />
                </div>
                <h3 class="mb-2">No pending requests</h3>
                <p class="text-sm text-gray-400">You're all caught up!</p>
              </div>
              <div v-else>
                <div
                  v-for="request in incomingRequests"
                  :key="request.friend_id"
                  class="flex items-center gap-4 p-4 rounded-lg border-2 border-[#2b1d3a] hover:border-[#7c3aed] transition-all"
                  style="background: rgba(26, 26, 46, 0.5)"
                >
                  <div
                    class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); border: 2px solid rgba(124, 58, 237, 0.5)"
                  >
                    <span class="text-xl">{{ getInitials(request.name || request.username || 'U') }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="mb-1">{{ request.name || request.username || 'Unknown' }}</h4>
                    <p class="text-xs text-gray-400">0 mutual friends</p>
                    <p class="text-xs text-gray-500">Sent {{ formatDate(request.created_at) }}</p>
                  </div>
                  <div class="flex gap-2 flex-shrink-0">
                    <GameButton variant="primary" size="sm" @click="acceptRequest(request.id)">
                      Accept
                </GameButton>
                    <GameButton variant="danger" size="sm" @click="rejectRequest(request.id)">
                      Decline
                </GameButton>
              </div>
                </div>
            </div>
          </div>

            <!-- Sent Requests Tab -->
            <div v-else-if="requestsTab === 'sent'" class="space-y-4">
              <div v-if="sentRequests.length === 0" class="text-center py-12">
                <div
                  class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
                  style="background: rgba(139, 92, 246, 0.1); border: 2px solid rgba(139, 92, 246, 0.3)"
                >
                  <Icon name="lucide:send" class="w-8 h-8" style="color: #8b5cf6" />
                </div>
                <h3 class="mb-2">No sent requests</h3>
                <p class="text-sm text-gray-400">Send friend requests to connect with others</p>
              </div>
              <div v-else>
                <div
                  v-for="request in sentRequests"
                  :key="request.id"
                  class="flex items-center gap-4 p-4 rounded-lg border-2 border-[#2b1d3a]"
                  style="background: rgba(26, 26, 46, 0.5)"
                >
                  <div
                    class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); border: 2px solid rgba(124, 58, 237, 0.5)"
                  >
                    <span class="text-xl">{{ getInitials(request.name || request.username || 'U') }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="mb-1">{{ request.name || request.username || 'Unknown' }}</h4>
                    <div class="flex items-center gap-2">
                      <span
                        class="text-xs px-2 py-1 rounded"
                        style="background: rgba(245, 158, 11, 0.2); color: #f59e0b"
                      >
                        Pending
                      </span>
                      <span class="text-xs text-gray-500">Sent {{ formatDate(request.created_at) }}</span>
                    </div>
                  </div>
                  <GameButton variant="danger" size="sm" @click="cancelRequest(request.id)">
                    Cancel Request
                  </GameButton>
                </div>
              </div>
            </div>
          </div>

          <!-- Search/Find Friends -->
          <div v-else-if="activeTab === 'search'" class="space-y-6">
            <!-- Search Bar -->
            <div class="flex gap-2">
              <div class="flex-1">
                <GameInput
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search by username"
                  @keyup.enter="searchFriends"
                />
              </div>
              <button
                @click="searchFriends"
                :disabled="isSearching"
                class="flex items-center justify-center transition-all duration-200 hover:brightness-110 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                style="background: transparent; border: none; color: white; height: 48px; width: 48px; padding: 0; margin-top: 1px;"
              >
                <Icon name="lucide:search" class="w-6 h-6" />
              </button>
            </div>

            <p v-if="searchError" class="text-sm text-red-400">{{ searchError }}</p>

            <!-- Search Results -->
            <div v-if="searchQuery.trim() && searchResults.length > 0">
              <h3 class="mb-4">Search Results</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="result in searchResults"
                :key="result.user_id"
                  class="p-4 rounded-lg border-2 border-[#2b1d3a] hover:border-[#7c3aed] transition-all cursor-pointer w-full flex items-center justify-between gap-3"
                  style="background: rgba(26, 26, 46, 0.5)"
                  @click="viewProfile(result.user_id)"
              >
                  <div class="flex items-center gap-3 min-w-0">
                    <div
                      class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                      style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); border: 2px solid rgba(124, 58, 237, 0.5)"
                    >
                      <span class="text-sm">{{ getInitials(result.name || result.username || 'U') }}</span>
                    </div>
                    <div class="min-w-0">
                      <h4 class="text-sm text-white truncate">{{ result.name || result.username || 'Unknown' }}</h4>
                      <p class="text-[11px] text-gray-400">0 mutual friends</p>
                    </div>
                  </div>
                    <GameButton
                    v-if="getRelationshipStatus(result.user_id) === 'none'"
                      variant="secondary"
                      size="sm"
                    @click.stop="sendFriendRequest(result.user_id)"
                    >
                    <template #icon>
                      <Icon name="lucide:user-plus" class="w-3 h-3" />
                    </template>
                    Add Friend
                  </GameButton>
                  <GameButton
                    v-else-if="getRelationshipStatus(result.user_id) === 'pending_outgoing'"
                    variant="danger"
                    size="sm"
                    @click.stop="cancelRequest(getFriendRequestId(result.user_id))"
                  >
                    <template #icon>
                      <Icon name="lucide:x" class="w-3 h-3" />
                    </template>
                    Cancel Request
                    </GameButton>
                    <GameButton
                    v-else-if="getRelationshipStatus(result.user_id) === 'pending_incoming'"
                      variant="primary"
                      size="sm"
                    @click.stop="() => {
                      const request = incomingRequests.find(r => r.user_id === result.user_id)
                      if (request?.id) acceptRequest(request.id)
                    }"
                  >
                    <template #icon>
                      <Icon name="lucide:user-check" class="w-3 h-3" />
                    </template>
                    Accept
                  </GameButton>
                  <GameButton
                    v-else-if="getRelationshipStatus(result.user_id) === 'friend'"
                    variant="danger"
                    size="sm"
                    @click.stop="() => {
                      const friend = friends.find(f => (f.friendUser?.user_id || f.friend_user_id) === result.user_id)
                      if (friend?.friend_id) removeFriend(friend.friend_id)
                    }"
                  >
                    <template #icon>
                      <Icon name="lucide:user-minus" class="w-3 h-3" />
                    </template>
                    Remove
                    </GameButton>
                  </div>
              </div>
            </div>

            <!-- Suggested Users -->
            <div class="space-y-4">
              <div class="mb-4">
                <h3 class="text-xl font-bold mb-2">Suggested Users</h3>
                <p class="text-sm text-white/60">Discover new friends based on your interests</p>
              </div>
              <div v-if="suggestedUsers.length === 0" class="text-center py-8">
                <Icon name="lucide:users" class="w-12 h-12 mx-auto mb-3 text-white/20" />
                <p class="text-white/60">No suggested users at the moment</p>
              </div>
              <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="suggested in suggestedUsers"
                  :key="suggested.user_id"
                  class="p-4 rounded-lg border-2 border-[#2b1d3a] hover:border-[#7c3aed] transition-all cursor-pointer"
                  style="background: rgba(26, 26, 46, 0.5)"
                  @click="viewProfile(suggested.user_id)"
                >
                  <div class="flex items-center gap-3 mb-4">
                    <div
                      class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                      style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); border: 2px solid rgba(124, 58, 237, 0.5)"
                    >
                      <span class="text-sm">{{ getInitials(suggested.name || suggested.username || 'U') }}</span>
                    </div>
                    <div class="min-w-0">
                      <h4 class="text-sm text-white truncate">{{ suggested.name || suggested.username || 'Unknown' }}</h4>
                      <p class="text-[11px] text-gray-400">0 mutual friends</p>
                    </div>
                  </div>
                    <GameButton
                    v-if="getRelationshipStatus(suggested.user_id) === 'none'"
                      variant="secondary"
                      size="sm"
                    class="w-full"
                    @click.stop="sendFriendRequest(suggested.user_id)"
                  >
                    <template #icon>
                      <Icon name="lucide:user-plus" class="w-3 h-3" />
                    </template>
                    Add Friend
                  </GameButton>
                  <GameButton
                    v-else-if="getRelationshipStatus(suggested.user_id) === 'pending_outgoing'"
                    variant="danger"
                    size="sm"
                    class="w-full"
                    @click.stop="cancelRequest(getFriendRequestId(suggested.user_id))"
                    >
                    <template #icon>
                      <Icon name="lucide:x" class="w-3 h-3" />
                    </template>
                    Cancel Request
                    </GameButton>
                    <GameButton
                    v-else-if="getRelationshipStatus(suggested.user_id) === 'pending_incoming'"
                      variant="primary"
                      size="sm"
                    class="w-full"
                    @click.stop="() => {
                      const request = incomingRequests.find(r => r.user_id === suggested.user_id)
                      if (request?.id) acceptRequest(request.id)
                    }"
                  >
                    <template #icon>
                      <Icon name="lucide:user-check" class="w-3 h-3" />
                    </template>
                    Accept
                  </GameButton>
                  <GameButton
                    v-else-if="getRelationshipStatus(suggested.user_id) === 'friend'"
                    variant="danger"
                    size="sm"
                    class="w-full"
                    @click.stop="() => {
                      const friend = friends.find(f => (f.friendUser?.user_id || f.friend_user_id) === suggested.user_id)
                      if (friend?.friend_id) removeFriend(friend.friend_id)
                    }"
                  >
                    <template #icon>
                      <Icon name="lucide:user-minus" class="w-3 h-3" />
                    </template>
                    Remove
                    </GameButton>
                </div>
              </div>
            </div>
          </div>
        </main>

        <!-- Footer -->
        <footer 
          class="border-t px-8 py-6 mt-16"
          style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-top: 1px solid rgba(43, 29, 58, 0.5)"
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

      <!-- Friend Profile Modal -->
      <FriendProfileModal
        v-if="selectedUserProfile"
        :is-open="showProfileModal"
        :friend="selectedUserProfile"
        :viewer-is-friend="false"
        @close="showProfileModal = false; selectedUserProfile = null"
        @add-friend="() => {
          if (selectedUserProfile) {
            sendFriendRequest(selectedUserProfile.id)
            showProfileModal = false
          }
        }"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import FriendCard from '../Components/cards/FriendCard.vue'
import FriendProfileModal from '../Components/modals/FriendProfileModal.vue'
import Icon from '../Components/ui/Icon.vue'
import { useToast } from '../Composables/useToast'
import { useNotifications } from '../Composables/useNotifications'
import axios from 'axios'

const page = usePage()
const toast = useToast()

const user = computed(() => page.props.auth?.user)
const isGuest = computed(() => !user.value)
const isAdmin = computed(() => user.value?.is_admin === true)
const username = computed(() => user.value?.name || 'Guest')

const searchQuery = ref('')
const friendsSearchQuery = ref('')
const activeTab = ref('friends')
const requestsTab = ref('pending')
const friends = ref([])
const incomingRequests = ref([])
const sentRequests = ref([])
const searchResults = ref([])
const suggestedUsers = ref([])
// Use global notifications
const { notifications, markAsRead: handleMarkAsRead, markAllAsRead: handleMarkAllAsRead, deleteNotification: handleDeleteNotification, loadNotifications, startNotificationPolling, stopNotificationPolling } = useNotifications(user)

const selectedUserProfile = ref(null)
const showProfileModal = ref(false)
const isSearching = ref(false)
const searchError = ref(null)

// Notification handlers are now provided by useNotifications composable
const handleNotificationClick = (notification) => {
  // Mark as read
  handleMarkAsRead(notification.id)
  
  // Navigate to the action URL if provided
  if (notification.actionUrl) {
    router.visit(notification.actionUrl)
  }
}

const tabs = computed(() => [
  { id: 'friends', label: 'Friends', icon: 'lucide:users', count: friends.value.length },
  { id: 'requests', label: 'Requests', icon: 'lucide:user-plus', count: incomingRequests.value.length },
  { id: 'search', label: 'Find Friends', icon: 'lucide:search' },
])

const filteredFriends = computed(() => {
  if (!friendsSearchQuery.value.trim()) return friends.value
  const query = friendsSearchQuery.value.toLowerCase()
  return friends.value.filter(friend => {
    const name = (friend.friendUser?.name || '').toLowerCase()
    const username = (friend.friendUser?.username || '').toLowerCase()
    return name.includes(query) || username.includes(query)
  })
})

onMounted(async () => {
  if (!isGuest.value) {
    await loadFriends()
    await loadFriendRequests()
    await loadSuggestedUsers()
    // Global notifications are initialized by useNotifications composable
    loadNotifications()
    startNotificationPolling()
  }
})

onUnmounted(() => {
  // Stop polling when leaving friends page (but keep notifications loaded globally)
  stopNotificationPolling()
})

const loadFriends = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    // Load accepted friends
    const response = await axios.get('/api/friends', { 
      headers,
      params: { status: 'Accepted' }
    })
    const allFriends = response.data.data || []
    
    // Filter to only show accepted friends
    friends.value = allFriends.filter(f => {
      if (f.status !== 'Accepted') return false
      if (!f.friendUser) {
        console.warn('Friend missing friendUser:', f)
        return false
      }
      return true
    })
  } catch (error) {
    console.error('Failed to load friends:', error)
  }
}

const loadFriendRequests = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    const response = await axios.get('/api/friends/requests', { headers })
    const newIncoming = response.data.incoming || []
    const newSent = response.data.sent || []
    
    incomingRequests.value = newIncoming
    sentRequests.value = newSent
  } catch (error) {
    console.error('Failed to load friend requests:', error)
  }
}

const loadSuggestedUsers = async () => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    const response = await axios.get('/api/users/suggested', { headers })
    const users = response.data.data || []
    
    suggestedUsers.value = users.map(u => ({
      user_id: u.user_id || u.id,
      name: u.name || 'Unknown',
      username: u.username || u.name || 'unknown'
    }))
  } catch (error) {
    console.error('Failed to load suggested users:', error)
    suggestedUsers.value = []
  }
}

const searchFriends = async () => {
  if (!searchQuery.value.trim()) {
    activeTab.value = 'search'
    searchResults.value = []
    return
  }
  
  isSearching.value = true
  searchError.value = null
  
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    // Use the friends search endpoint
    const query = searchQuery.value.trim().replace(/^@/, '')
    const response = await axios.get('/api/friends/search', {
      headers,
      params: { q: query }
    })
    
    searchResults.value = (response.data.data || []).map(u => ({
      user_id: u.user_id,
      name: u.name || 'Unknown',
      username: u.username || 'unknown'
    }))
    
    activeTab.value = 'search'
  } catch (error) {
    console.error('Failed to search users:', error)
    searchError.value = error.response?.data?.message || 'Failed to search. Please try again.'
    searchResults.value = []
  } finally {
    isSearching.value = false
  }
}

const handleTabClick = async (tabId) => {
  activeTab.value = tabId
  if (tabId === 'search') {
    // Reload suggested users when switching to search tab to get fresh data
    await loadSuggestedUsers()
  }
}

const removeFriend = async (friendId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.delete(`/api/friends/${friendId}`, { headers })
    toast.success('Success', 'Friend removed')
    await loadFriends()
    // Reload suggested users to update button states
    await loadSuggestedUsers()
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to remove friend')
  }
}

const acceptRequest = async (friendId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.put(`/api/friends/${friendId}`, { status: 'Accepted' }, { headers })
    toast.success('Success', 'Friend request accepted')
    
    // Reload all data to update UI
    await Promise.all([
      loadFriends(),
      loadFriendRequests(),
      loadSuggestedUsers()
    ])
  } catch (error) {
    console.error('Accept request error:', error)
    toast.error('Error', error.response?.data?.message || 'Failed to accept request')
  }
}

const rejectRequest = async (friendId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.delete(`/api/friends/${friendId}`, { headers })
    toast.success('Success', 'Friend request rejected')
    await loadFriendRequests()
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to reject request')
  }
}

const cancelRequest = async (friendId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.delete(`/api/friends/${friendId}`, { headers })
    toast.success('Success', 'Friend request cancelled')
    await loadFriendRequests()
    // Reload suggested users to update button states
    await loadSuggestedUsers()
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to cancel request')
  }
}

const sendFriendRequest = async (userId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    await axios.post('/api/friends', {
      friend_user_id: userId
    }, { headers })
    toast.success('Success', 'Friend request sent')
    await loadFriendRequests()
    await loadFriends() // Reload friends list in case they're already friends
    await loadSuggestedUsers() // Update button states
  } catch (error) {
    const errorData = error.response?.data || {}
    const errorMessage = errorData.message || 'Failed to send friend request'
    
    console.error('Send friend request error:', error)
    
    // If already friends, reload friends list to update UI
    if (error.response?.status === 409) {
      if (errorData.relationship_status === 'accepted') {
        toast.info('Already Friends', 'You are already friends with this user. Refreshing friends list...')
        // Force reload everything
        await Promise.all([
          loadFriends(),
          loadFriendRequests(),
          loadSuggestedUsers()
        ])
      } else if (errorData.relationship_status === 'pending_outgoing') {
        toast.info('Request Sent', 'You have already sent a friend request to this user')
        await loadFriendRequests()
        await loadSuggestedUsers()
      } else if (errorData.relationship_status === 'pending_incoming') {
        toast.info('Incoming Request', 'This user has already sent you a friend request. Check your incoming requests.')
        await loadFriendRequests()
        await loadSuggestedUsers()
      } else {
        // Unknown status - reload everything to sync
        toast.warning('Request Exists', errorMessage + '. Refreshing...')
        await Promise.all([
          loadFriends(),
          loadFriendRequests(),
          loadSuggestedUsers()
        ])
      }
    } else {
      toast.error('Error', errorMessage)
    }
  }
}

const viewProfile = async (userId) => {
  try {
    const token = localStorage.getItem('api_token')
    const headers = token ? { Authorization: `Bearer ${token}` } : {}
    
    const response = await axios.get(`/api/users/${userId}/profile`, { headers })
    const profileData = response.data.data
    
    selectedUserProfile.value = {
      id: profileData.user_id,
      username: profileData.username || profileData.name || 'Unknown',
      name: profileData.name || profileData.username || 'Unknown',
      avatar: profileData.avatar || profileData.avatar_url,
      bio: profileData.bio || '',
      status: profileData.status || 'Offline',
      friendSince: null,
      memberSince: profileData.memberSince || profileData.created_at,
      stats: profileData.stats || {
        gamesOwned: 0,
        gamesCompleted: 0,
        totalPlaytime: 0,
        reviewsWritten: 0
      },
      favoriteGenres: profileData.favoriteGenres || [],
      recentActivity: profileData.recentActivity || [],
      socialLinks: profileData.socialLinks || []
    }
    
    showProfileModal.value = true
  } catch (error) {
    console.error('Failed to load user profile:', error)
    toast.error('Error', 'Failed to load user profile')
  }
}

const getRelationshipStatus = (userId) => {
  // Check if already a friend
  const isFriend = friends.value.some(f => 
    (f.friendUser?.user_id || f.friend_user_id) === userId
  )
  if (isFriend) return 'friend'
  
  // Check if there's a pending incoming request
  const hasIncomingRequest = incomingRequests.value.some(r => 
    r.user_id === userId
  )
  if (hasIncomingRequest) return 'pending_incoming'
  
  // Check if there's a pending sent request
  const hasSentRequest = sentRequests.value.some(r => 
    r.user_id === userId
  )
  if (hasSentRequest) return 'pending_outgoing'
  
  return 'none'
}

const getFriendRequestId = (userId) => {
  // Find the friend_id for a sent request
  const sentRequest = sentRequests.value.find(r => 
    r.user_id === userId
  )
  return sentRequest?.id || null
}

const getInitials = (name) => {
  if (!name) return 'U'
  return name.substring(0, 2).toUpperCase()
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  } catch {
    return ''
  }
}

const formatFriendSince = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
  } catch {
    return ''
  }
}

// Notification management is handled by useNotifications composable (global)
</script>
