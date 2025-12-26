<template>
  <div class="relative flex-shrink-0">
    <button
      class="relative p-2 rounded-lg transition-all duration-200 cursor-pointer group"
      style="background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.3); min-width: 36px; min-height: 36px"
      @click="isOpen = !isOpen"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
    >
      <Icon name="lucide:bell" class="w-5 h-5 transition-colors" style="color: white" />
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold text-white"
        style="background: #ef4444; color: white; box-shadow: 0 0 10px rgba(239, 68, 68, 0.6)"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <Transition name="dropdown">
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-96 border-2 border-[#2b1d3a] overflow-hidden z-50"
        style="background: rgba(27, 22, 38, 0.98); backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(124, 58, 237, 0.5)"
      >
        <div class="p-4 border-b border-[#2b1d3a]">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold">Notifications</h3>
            <button
              v-if="unreadCount > 0"
              @click="onMarkAllAsRead?.()"
              class="text-xs text-[#8b5cf6] hover:text-[#a78bfa] transition-colors"
            >
              Mark all as read
            </button>
          </div>
          
          <div class="flex items-center gap-2">
            <Icon name="lucide:arrow-up-down" class="w-4 h-4 text-gray-400" />
            <select
              v-model="sortBy"
              class="text-xs px-2 py-1 rounded cursor-pointer"
              style="background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.3); color: white"
            >
              <option value="newest" style="background: #1b1626; color: white">Newest first</option>
              <option value="oldest" style="background: #1b1626; color: white">Oldest first</option>
              <option value="unread" style="background: #1b1626; color: white">Unread first</option>
            </select>
          </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
          <div v-if="sortedNotifications.length > 0" class="divide-y divide-[#2b1d3a]">
            <div
              v-for="notification in sortedNotifications"
              :key="notification.id"
              :class="[
                'p-4 hover:bg-white/5 transition-colors cursor-pointer',
                !notification.read ? 'bg-[rgba(124,58,237,0.1)]' : ''
              ]"
              @click="handleNotificationClick(notification)"
            >
              <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0">
                  {{ getNotificationIcon(notification.type) }}
                </span>
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                      <h4 :class="['text-sm font-semibold mb-1', !notification.read ? 'text-white' : 'text-gray-300']">
                        {{ notification.title }}
                      </h4>
                      <p class="text-xs text-gray-400 line-clamp-2">
                        {{ notification.message }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        {{ formatTime(notification.timestamp) }}
                      </p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                      <div
                        v-if="!notification.read"
                        class="w-2 h-2 rounded-full"
                        style="background: #8b5cf6"
                      />
                      <button
                        @click.stop="onDelete?.(notification.id)"
                        class="p-1 hover:bg-white/10 rounded transition-colors"
                      >
                        <Icon name="lucide:x" class="w-3 h-3 text-gray-400" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="p-8 text-center">
            <Icon name="lucide:bell" class="w-12 h-12 mx-auto mb-3 text-gray-500" />
            <p class="text-sm text-gray-400">No notifications</p>
          </div>
        </div>

        <div v-if="sortedNotifications.length > 0" class="p-3 border-t border-[#2b1d3a]">
          <button
            @click="onClearAll?.()"
            class="w-full text-xs text-center text-gray-400 hover:text-white transition-colors"
          >
            Clear all notifications
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  },
  onMarkAsRead: {
    type: Function,
    default: undefined
  },
  onMarkAllAsRead: {
    type: Function,
    default: undefined
  },
  onDelete: {
    type: Function,
    default: undefined
  },
  onClearAll: {
    type: Function,
    default: undefined
  },
  onNotificationClick: {
    type: Function,
    default: undefined
  }
})

const isOpen = ref(false)
const sortBy = ref('newest')

const unreadCount = computed(() => {
  return props.notifications.filter(n => !n.read).length
})

const sortedNotifications = computed(() => {
  return [...props.notifications].sort((a, b) => {
    if (sortBy.value === 'unread') {
      if (a.read !== b.read) {
        return a.read ? 1 : -1
      }
    }
    const timeDiff = new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
    return sortBy.value === 'newest' ? timeDiff : -timeDiff
  })
})

const formatTime = (date) => {
  const now = new Date()
  const timestamp = date instanceof Date ? date : new Date(date)
  const diff = now.getTime() - timestamp.getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return timestamp.toLocaleDateString()
}

const getNotificationIcon = (type) => {
  switch (type) {
    case 'friend_request': return '👤'
    case 'game_update': return '🎮'
    case 'achievement': return '🏆'
    default: return '🔔'
  }
}

const handleNotificationClick = (notification) => {
  if (!notification.read) {
    props.onMarkAsRead?.(notification.id)
  }
  props.onNotificationClick?.(notification)
}

const handleMouseEnter = (e) => {
  e.currentTarget.style.background = 'rgba(124, 58, 237, 0.3)'
  e.currentTarget.style.boxShadow = '0 0 20px rgba(124, 58, 237, 0.4)'
}

const handleMouseLeave = (e) => {
  e.currentTarget.style.background = 'rgba(124, 58, 237, 0.15)'
  e.currentTarget.style.boxShadow = 'none'
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>

