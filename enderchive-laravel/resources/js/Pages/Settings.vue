<template>
  <AppLayout>
    <div 
      class="min-h-screen relative"
      style="background: linear-gradient(180deg, #0a0015 0%, #1c0031 50%, #250040 100%)"
    >
      <FloatingParticles v-if="settings.particleEffects" />

      <div class="relative z-10">
        <header 
          class="sticky top-0 z-50 h-20 border-b"
          style="background: linear-gradient(90deg, rgba(10,0,21,0.9) 0%, rgba(30,0,62,0.85) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(94,0,160,0.35);"
        >
          <div class="max-w-[1280px] mx-auto px-3 sm:px-4 md:px-8 h-full flex items-center gap-4">
            <!-- Logo -->
            <button @click="router.visit('/home')" class="flex items-center gap-2 md:gap-3 flex-shrink-0 hover:opacity-80 transition-opacity">
              <Icon name="lucide:gamepad-2" class="w-6 h-6 md:w-7 md:h-7 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
              <h1 class="text-brand text-[#22c55e] hidden sm:block text-xs md:text-sm font-bold" style="text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em">
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
                :active="true"
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
                class="flex items-center gap-2 px-2 sm:px-3 md:px-4 py-1.5 md:py-2 rounded-lg transition-all duration-200 cursor-pointer group font-medium flex-shrink-0"
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
                  {{ isGuest ? 'Sign in' : (user?.name || 'Guest') }}
                </span>
              </button>
              <button
                v-if="isAdmin"
                @click="router.visit('/admin')"
                class="relative p-2 rounded-lg transition-all duration-200 cursor-pointer group flex-shrink-0"
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
              current-page="settings"
              :username="user?.name || 'Guest'"
              :is-admin="isAdmin"
              :show-notifications="!isGuest"
              :notification-count="notifications.filter(n => !n.read).length"
              @notifications-click="() => {}"
              @profile-click="() => router.visit('/profile')"
              @admin-click="() => router.visit('/admin')"
            />
          </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
          <h1 class="mb-6 sm:mb-8 text-2xl sm:text-3xl">Settings</h1>

          <div class="space-y-6">
            <!-- Appearance Card -->
            <div
              class="glass-panel rounded-lg p-4 sm:p-6 border-4 border-[#2b1d3a]"
              style="box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2)"
            >
              <h2 class="mb-4 sm:mb-6 text-lg sm:text-xl font-bold text-white">Appearance</h2>
              
              <div class="space-y-4 sm:space-y-6">
                <!-- Particle Effects -->
                <div class="flex items-center justify-between gap-4">
                  <div class="flex-1 min-w-0">
                    <div class="mb-1 text-white font-medium">Floating Particles</div>
                    <p class="text-xs text-white/70">Show animated background particles</p>
                  </div>
                  <div class="flex-shrink-0">
                    <ToggleSwitch v-model="settings.particleEffects" />
                  </div>
                </div>

                <!-- Font Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-xs mb-2 text-white/70 font-medium">Interface font</p>
                    <select
                      :value="settings.fontFamily"
                      @change="(e) => updateSetting('fontFamily', e.target.value)"
                      class="w-full px-3 py-2 rounded-lg text-sm cursor-pointer font-medium"
                      style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.5); color: white; min-height: 40px"
                    >
                      <option value="pixel" style="background: #0f0f23; color: white">Pixel (default)</option>
                      <option value="system-sans" style="background: #0f0f23; color: white">System Sans (Segoe / SF)</option>
                      <option value="system-serif" style="background: #0f0f23; color: white">System Serif (Times / Georgia)</option>
                      <option value="monospace" style="background: #0f0f23; color: white">Monospace (Courier / Consolas)</option>
                    </select>
                  </div>
                  <div>
                    <p class="text-xs mb-2 text-white/70 font-medium">Font size</p>
                    <select
                      :value="settings.fontSize"
                      @change="(e) => updateSetting('fontSize', e.target.value)"
                      class="w-full px-3 py-2 rounded-lg text-sm cursor-pointer font-medium"
                      style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.5); color: white; min-height: 40px"
                    >
                      <option value="xsmall" style="background: #0f0f23; color: white">Extra small</option>
                      <option value="small" style="background: #0f0f23; color: white">Small</option>
                      <option value="normal" style="background: #0f0f23; color: white">Normal (default)</option>
                      <option value="large" style="background: #0f0f23; color: white">Large</option>
                      <option value="xlarge" style="background: #0f0f23; color: white">Extra large</option>
                    </select>
                  </div>
                </div>

                <!-- Font Preview -->
                <div class="mt-4 p-4 rounded-lg border border-[#2b1d3a]" style="background: rgba(15,15,35,0.8)">
                  <p class="text-xs mb-2" style="color: rgba(255, 255, 255, 0.6)">Preview</p>
                  <p
                    class="text-sm text-white"
                    :style="{
                      fontFamily: resolvePreviewFontFamily(settings.fontFamily),
                      fontSize: `${14 * resolvePreviewFontScale(settings.fontSize)}px`,
                      lineHeight: 1.6,
                    }"
                  >
                    The quick brown fox jumps over the lazy dog 123 — this is how text will look with your current font
                    and size settings.
                  </p>
                </div>

                <!-- Save Button (Appearance) -->
                <div v-if="hasChanges" class="flex justify-end pt-4">
                  <GameButton
                    variant="primary"
                    @click="handleSave"
                    class="min-w-[140px]"
                  >
                    Save Appearance
                  </GameButton>
                </div>
              </div>
            </div>

            <!-- Notifications Card -->
            <div
              class="glass-panel rounded-lg p-4 sm:p-6 border-4 border-[#2b1d3a]"
              style="box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2)"
            >
              <h2 class="mb-4 sm:mb-6 text-lg sm:text-xl font-bold text-white">Notifications</h2>
              
              <div class="space-y-4 sm:space-y-6">
                <!-- Friend Requests -->
                <div class="flex items-center justify-between gap-4">
                  <div class="flex-1 min-w-0">
                    <div class="mb-1 text-white font-medium">Friend Requests</div>
                    <p class="text-xs text-white/70">Get notified when someone sends a friend request</p>
                  </div>
                  <div class="flex-shrink-0">
                    <ToggleSwitch v-model="settings.friendRequests" />
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Change Password Modal -->
        <div 
          v-if="showChangePassword"
          class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
          style="background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(4px)"
          @click="showChangePassword = false"
        >
          <div
            class="glass-panel rounded-lg p-4 sm:p-6 border-4 border-[#2b1d3a] max-w-md w-full my-auto"
            style="box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="flex items-center gap-3 mb-4 sm:mb-6">
              <Icon name="lucide:key" class="w-5 h-5 sm:w-6 sm:h-6 text-[#22c55e]" />
              <h2 class="text-lg sm:text-xl font-bold text-white">Change Password</h2>
            </div>

            <div class="space-y-4 mb-4 sm:mb-6">
              <div>
                <label class="text-sm mb-2 block text-white/80 font-medium">Current Password</label>
                <GameInput
                  type="password"
                  v-model="passwordData.currentPassword"
                  placeholder="Enter current password"
                  class="w-full"
                />
              </div>

              <div>
                <label class="text-sm mb-2 block text-white/80 font-medium">New Password</label>
                <GameInput
                  type="password"
                  v-model="passwordData.newPassword"
                  placeholder="Enter new password (min 8 characters)"
                  class="w-full"
                />
              </div>

              <div>
                <label class="text-sm mb-2 block text-white/80 font-medium">Confirm New Password</label>
                <GameInput
                  type="password"
                  v-model="passwordData.confirmPassword"
                  placeholder="Confirm new password"
                  class="w-full"
                />
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <GameButton
                variant="secondary"
                :full-width="true"
                @click="showChangePassword = false; passwordData = { currentPassword: '', newPassword: '', confirmPassword: '' }"
                class="min-h-[48px]"
              >
                Cancel
              </GameButton>
              <GameButton
                variant="primary"
                :full-width="true"
                @click="handleChangePassword"
                :disabled="!passwordData.currentPassword || !passwordData.newPassword || !passwordData.confirmPassword"
                class="min-h-[48px]"
              >
                Change Password
              </GameButton>
            </div>
          </div>
        </div>

        <!-- Delete Account Modal -->
        <div 
          v-if="showDeleteAccount"
          class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
          style="background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(4px)"
          @click="showDeleteAccount = false"
        >
          <div
            class="glass-panel rounded-lg p-4 sm:p-6 border-4 max-w-md w-full my-auto"
            style="border-color: rgba(239, 68, 68, 0.5); box-shadow: 0 0 40px rgba(239, 68, 68, 0.4)"
            @click.stop
          >
            <div class="flex items-center gap-3 mb-4 sm:mb-6">
              <Icon name="lucide:alert-triangle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" />
              <h2 class="text-lg sm:text-xl font-bold text-red-500">Delete Account</h2>
            </div>

            <div class="mb-4 sm:mb-6 space-y-4">
              <p class="text-white/90 text-sm sm:text-base">
                This action is <span class="text-red-500 font-bold">permanent and irreversible</span>. 
                All your data, including:
              </p>
              <ul class="list-disc list-inside text-sm space-y-1 ml-2 text-white/70">
                <li>Game library and wishlist</li>
                <li>Reviews and ratings</li>
                <li>Friends and social connections</li>
                <li>Achievement progress and statistics</li>
                <li>Profile customization</li>
              </ul>
              <p class="text-white/90 text-sm sm:text-base">
                will be <span class="text-red-500 font-bold">permanently deleted</span>.
              </p>
              
              <div class="mt-4 sm:mt-6">
                <label class="text-sm mb-2 block text-white/80 font-medium">
                  Type <span class="text-red-500 font-bold">DELETE</span> to confirm:
                </label>
                <GameInput
                  type="text"
                  v-model="deleteConfirmation"
                  placeholder="DELETE"
                  class="w-full"
                />
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <GameButton
                variant="secondary"
                :full-width="true"
                @click="showDeleteAccount = false; deleteConfirmation = ''"
                class="min-h-[48px]"
              >
                Cancel
              </GameButton>
              <GameButton
                variant="danger"
                :full-width="true"
                @click="handleDeleteAccount"
                :disabled="deleteConfirmation.toLowerCase() !== 'delete'"
                class="min-h-[48px]"
              >
                Delete Account
              </GameButton>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import FloatingParticles from '../Components/effects/FloatingParticles.vue'
import NavigationTab from '../Components/navigation/NavigationTab.vue'
import MobileNav from '../Components/navigation/MobileNav.vue'
import NotificationButton from '../Components/ui/NotificationButton.vue'
import ToggleSwitch from '../Components/forms/ToggleSwitch.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import Icon from '../Components/ui/Icon.vue'
import { useToast } from '../Composables/useToast'
import { useNotifications } from '../Composables/useNotifications'

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

const loadSettings = () => {
  try {
    const saved = localStorage.getItem('enderchive-settings')
    if (saved) {
      return JSON.parse(saved)
    }
  } catch (error) {
    console.error('Failed to load settings:', error)
  }
  return {
    particleEffects: true,
    friendRequests: true,
    fontFamily: 'pixel',
    fontSize: 'normal'
  }
}

const settings = ref(loadSettings())
const hasChanges = ref(false)
const appliedFontFamily = ref(settings.value.fontFamily)
const appliedFontSize = ref(settings.value.fontSize)

// Apply font settings on mount
onMounted(() => {
  // Initialize notifications
  if (!isGuest.value) {
    loadNotifications()
    startNotificationPolling()
  }
  
  // Apply initial font size
  const root = document.documentElement
  let scale = 1
  switch (appliedFontSize.value) {
    case 'xsmall':
      scale = 0.8
      break
    case 'small':
      scale = 0.9
      break
    case 'large':
      scale = 1.1
      break
    case 'xlarge':
      scale = 1.25
      break
    case 'normal':
    default:
      scale = 1
      break
  }
  root.style.setProperty('--font-size-scale', scale.toString())
  root.style.fontSize = `${16 * scale}px`
  
  // Apply initial font family
  switch (appliedFontFamily.value) {
    case 'system-sans':
      root.style.setProperty('--font-primary', "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif")
      break
    case 'system-serif':
      root.style.setProperty('--font-primary', "Georgia, 'Times New Roman', serif")
      break
    case 'monospace':
      root.style.setProperty('--font-primary', "Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace")
      break
    case 'pixel':
    default:
      root.style.setProperty('--font-primary', "'Press Start 2P', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif")
      break
  }
})
const showChangePassword = ref(false)
const showDeleteAccount = ref(false)
const passwordData = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})
const deleteConfirmation = ref('')

// Save settings to localStorage whenever they change
watch(settings, () => {
  try {
    localStorage.setItem('enderchive-settings', JSON.stringify(settings.value))
    window.dispatchEvent(new Event('settings-updated'))
  } catch (error) {
    console.error('Failed to save settings:', error)
  }
}, { deep: true })

// Apply font setting globally
watch(appliedFontFamily, (newVal) => {
  const root = document.documentElement
  switch (newVal) {
    case 'system-sans':
      root.style.setProperty('--font-primary', "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif")
      break
    case 'system-serif':
      root.style.setProperty('--font-primary', "Georgia, 'Times New Roman', serif")
      break
    case 'monospace':
      root.style.setProperty('--font-primary', "Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace")
      break
    case 'pixel':
    default:
      root.style.setProperty('--font-primary', "'Press Start 2P', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif")
      break
  }
}, { immediate: true })

// Apply font size globally
watch(appliedFontSize, (newVal) => {
  const root = document.documentElement
  let scale = 1
  switch (newVal) {
    case 'xsmall':
      scale = 0.8
      break
    case 'small':
      scale = 0.9
      break
    case 'large':
      scale = 1.1
      break
    case 'xlarge':
      scale = 1.25
      break
    case 'normal':
    default:
      scale = 1
      break
  }
  // Set CSS variable for reference
  root.style.setProperty('--font-size-scale', scale.toString())
  // Apply font size directly to html element (base 16px)
  root.style.fontSize = `${16 * scale}px`
}, { immediate: true })

const updateSetting = (key, value) => {
  settings.value[key] = value
  hasChanges.value = true
}

const handleSave = () => {
  toast.success('Success', 'Settings saved!')
  hasChanges.value = false
  appliedFontFamily.value = settings.value.fontFamily
  appliedFontSize.value = settings.value.fontSize
}

const handleChangePassword = () => {
  if (passwordData.value.newPassword !== passwordData.value.confirmPassword) {
    toast.error('Error', 'New passwords do not match!')
    return
  }
  if (passwordData.value.newPassword.length < 8) {
    toast.error('Error', 'Password must be at least 8 characters long!')
    return
  }
  toast.success('Success', 'Password changed!')
  passwordData.value = { currentPassword: '', newPassword: '', confirmPassword: '' }
  showChangePassword.value = false
}

const handleDeleteAccount = () => {
  if (deleteConfirmation.value.toLowerCase() !== 'delete') {
    toast.error('Error', 'Please type DELETE to confirm account deletion')
    return
  }
  toast.error('Account Deleted', 'Your account has been permanently deleted')
  deleteConfirmation.value = ''
  showDeleteAccount.value = false
  // In a real app, you would call an API endpoint here
}

const resolvePreviewFontFamily = (family) => {
  switch (family) {
    case 'system-sans':
      return "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    case 'system-serif':
      return "Georgia, 'Times New Roman', serif"
    case 'monospace':
      return "Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    case 'pixel':
    default:
      return "'Press Start 2P', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
  }
}

const resolvePreviewFontScale = (size) => {
  switch (size) {
    case 'xsmall':
      return 0.8
    case 'small':
      return 0.9
    case 'large':
      return 1.1
    case 'xlarge':
      return 1.25
    case 'normal':
    default:
      return 1
  }
}
</script>
