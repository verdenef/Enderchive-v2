<template>
  <div class="md:hidden flex-shrink-0">
    <button
      class="p-2 rounded-lg transition-all hover:bg-white/10 active:scale-95 min-w-[40px] min-h-[40px] flex items-center justify-center"
      aria-label="Toggle menu"
      :aria-expanded="isOpen"
      @click.stop="isOpen = !isOpen"
      style="background: rgba(124, 58, 237, 0.25); border: 1px solid rgba(124, 58, 237, 0.4); box-shadow: 0 2px 8px rgba(124, 58, 237, 0.2)"
    >
      <Icon :name="isOpen ? 'lucide:x' : 'lucide:menu'" class="w-6 h-6 flex-shrink-0" style="color: white; filter: drop-shadow(0 0 4px rgba(124, 58, 237, 0.6))" />
    </button>

    <Teleport to="body">
      <Transition name="overlay">
        <div
          v-if="isOpen"
          class="fixed inset-0 bg-black/70 z-[9998] md:hidden"
          style="backdrop-filter: blur(2px)"
          aria-hidden="true"
          @click="isOpen = false"
        />
      </Transition>

      <Transition name="drawer">
        <div
          v-if="isOpen"
          class="fixed top-0 right-0 h-full w-[280px] sm:w-80 z-[9999] md:hidden overflow-hidden"
          style="background: linear-gradient(180deg, rgba(10,0,21,0.98) 0%, rgba(30,0,62,0.98) 100%); backdrop-filter: blur(20px); border-left: 2px solid rgba(124, 58, 237, 0.5); box-shadow: -4px 0 30px rgba(0, 0, 0, 0.6)"
          @click.stop
        >
          <div class="flex flex-col h-full">
            <!-- Header -->
            <div class="p-4 sm:p-6 border-b border-[#2b1d3a] flex-shrink-0">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-[#22c55e]">Menu</h2>
                <button
                  @click="isOpen = false"
                  class="p-2 rounded-lg hover:bg-white/10 active:bg-white/20 transition-colors min-w-[36px] min-h-[36px] flex items-center justify-center"
                  aria-label="Close menu"
                  style="background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.3)"
                >
                  <Icon name="lucide:x" class="w-5 h-5" style="color: white" />
                </button>
              </div>
              <button
                @click="handleAction('profile')"
                class="w-full flex items-center gap-3 p-3 rounded-lg transition-all hover:bg-white/5 active:bg-white/10"
                style="background: rgba(124, 58, 237, 0.2); border: 1px solid rgba(124, 58, 237, 0.4)"
              >
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                  style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)"
                >
                  {{ username && username.length > 0 ? username.charAt(0).toUpperCase() : 'G' }}
                </div>
                <div class="flex-1 min-w-0 text-left">
                  <p class="text-sm font-semibold text-white truncate">{{ username || 'Guest' }}</p>
                  <p class="text-xs text-white/70">View Profile</p>
                </div>
                <Icon name="lucide:chevron-right" class="w-4 h-4 text-white/50 flex-shrink-0" />
              </button>
            </div>

            <!-- Navigation Items -->
            <nav class="flex-1 overflow-y-auto py-2 min-h-0">
              <button
                v-for="item in navItems"
                :key="item.id"
                :class="[
                  'w-full flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 transition-all active:scale-[0.98] min-h-[52px]',
                  currentPage === item.id
                    ? 'bg-[#22c55e]/20 border-l-4 border-[#22c55e]'
                    : 'hover:bg-white/5 border-l-4 border-transparent'
                ]"
                :aria-current="currentPage === item.id ? 'page' : undefined"
                @click="handleNavigate(item.id)"
              >
                <Icon
                  :name="item.icon"
                  class="w-5 h-5 flex-shrink-0"
                  :style="{ color: currentPage === item.id ? '#22c55e' : 'rgba(255, 255, 255, 0.8)' }"
                />
                <span
                  class="font-medium text-left flex-1"
                  :style="{ color: currentPage === item.id ? '#22c55e' : 'white' }"
                >
                  {{ item.label }}
                </span>
                <Icon
                  v-if="currentPage === item.id"
                  name="lucide:check"
                  class="w-4 h-4 flex-shrink-0"
                  style="color: #22c55e"
                />
              </button>
            </nav>

            <!-- Action Buttons Section -->
            <div class="px-4 sm:px-6 py-4 border-t border-[#2b1d3a] flex-shrink-0 space-y-2">
              <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider mb-3 px-2">Actions</h3>
              
              <!-- Notifications Button -->
              <button
                v-if="showNotifications"
                @click="handleAction('notifications')"
                class="w-full flex items-center gap-3 px-4 sm:px-6 py-3 rounded-lg transition-all hover:bg-white/5 active:bg-white/10 active:scale-[0.98] min-h-[44px]"
                style="background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.2)"
              >
                <Icon name="lucide:bell" class="w-5 h-5 flex-shrink-0" style="color: rgba(255, 255, 255, 0.9)" />
                <span class="font-medium text-white flex-1 text-left">Notifications</span>
                <span v-if="notificationCount > 0" class="px-2 py-1 rounded-full text-xs font-bold flex-shrink-0" style="background: #22c55e; color: white; min-width: 24px; text-align: center">
                  {{ notificationCount }}
                </span>
              </button>

              <!-- Profile Button -->
              <button
                @click="handleAction('profile')"
                class="w-full flex items-center gap-3 px-4 sm:px-6 py-3 rounded-lg transition-all hover:bg-white/5 active:bg-white/10 active:scale-[0.98] min-h-[44px]"
                style="background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.2)"
              >
                <Icon name="lucide:user" class="w-5 h-5 flex-shrink-0" style="color: rgba(255, 255, 255, 0.9)" />
                <span class="font-medium text-white flex-1 text-left">Profile</span>
              </button>

              <!-- Admin Button -->
              <button
                v-if="isAdmin"
                @click="handleAction('admin')"
                class="w-full flex items-center gap-3 px-4 sm:px-6 py-3 rounded-lg transition-all hover:bg-white/5 active:bg-white/10 active:scale-[0.98] min-h-[44px]"
                style="background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.2)"
              >
                <Icon name="lucide:shield" class="w-5 h-5 flex-shrink-0" style="color: rgba(255, 255, 255, 0.9)" />
                <span class="font-medium text-white flex-1 text-left">Admin</span>
              </button>
            </div>

            <!-- Footer -->
            <div class="p-4 sm:p-6 border-t border-[#2b1d3a] flex-shrink-0">
              <p class="text-xs text-white/50 text-center">
                Enderchive © 2025
              </p>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  currentPage: {
    type: String,
    default: 'home'
  },
  username: {
    type: String,
    default: 'Guest'
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  showNotifications: {
    type: Boolean,
    default: false
  },
  notificationCount: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['notifications-click', 'profile-click', 'admin-click'])

const isOpen = ref(false)

const navItems = computed(() => {
  const items = [
    { id: 'home', label: 'Home', icon: 'lucide:home', path: '/home' },
    { id: 'library', label: 'Library', icon: 'lucide:library', path: '/library' },
    { id: 'wishlist', label: 'Wishlist', icon: 'lucide:heart', path: '/wishlist' },
    { id: 'friends', label: 'Friends', icon: 'lucide:users', path: '/friends' },
    { id: 'settings', label: 'Settings', icon: 'lucide:settings', path: '/settings' },
  ]
  
  if (props.isAdmin) {
    items.push({ id: 'admin', label: 'Admin', icon: 'lucide:shield', path: '/admin' })
  }
  
  return items
})

const handleNavigate = (page) => {
  const item = navItems.value.find(i => i.id === page)
  if (item) {
    router.visit(item.path)
  }
  isOpen.value = false
}

const handleAction = (action) => {
  emit(`${action}-click`)
  isOpen.value = false
  
  // Navigate to appropriate page
  switch (action) {
    case 'profile':
      router.visit('/profile')
      break
    case 'admin':
      router.visit('/admin')
      break
    case 'notifications':
      // Notifications are handled by the parent component
      break
  }
}

// Close menu on escape key
const handleEscape = (e) => {
  if (e.key === 'Escape' && isOpen.value) {
    isOpen.value = false
  }
}

// Prevent body scroll when menu is open
watch(isOpen, (open) => {
  if (open) {
    document.body.style.overflow = 'hidden'
    window.addEventListener('keydown', handleEscape)
  } else {
    document.body.style.overflow = ''
    window.removeEventListener('keydown', handleEscape)
  }
})

onUnmounted(() => {
  document.body.style.overflow = ''
  window.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.2s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.drawer-enter-active,
.drawer-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
}

.drawer-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.drawer-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>

