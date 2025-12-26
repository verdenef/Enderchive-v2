import { ref, computed, watch } from 'vue'
import axios from 'axios'

// Global notification state (shared across all components)
const globalNotifications = ref([])
const notificationPollInterval = ref(null)
const lastKnownIncomingCount = ref(-1) // -1 indicates initial load
const lastKnownSentIds = ref(new Set())
let currentUserId = null
let isInitialized = false

export function useNotifications(user) {
  // Use global state
  const notifications = computed(() => globalNotifications.value)

  const loadNotifications = () => {
    const userId = user.value?.id || user.value?.user_id || currentUserId
    if (!userId) return
    
    currentUserId = userId
    
    try {
      const stored = localStorage.getItem(`notifications_${userId}`)
      if (stored) {
        const parsed = JSON.parse(stored)
        globalNotifications.value = parsed.map(n => ({
          ...n,
          timestamp: new Date(n.timestamp)
        }))
      } else {
        globalNotifications.value = []
      }
    } catch (error) {
      console.error('Failed to load notifications:', error)
      globalNotifications.value = []
    }
  }

  const saveNotifications = () => {
    const userId = user.value?.id || user.value?.user_id || currentUserId
    if (!userId) return
    
    try {
      localStorage.setItem(`notifications_${userId}`, JSON.stringify(globalNotifications.value))
    } catch (error) {
      console.error('Failed to save notifications:', error)
    }
  }

  const addNotification = (notification) => {
    // Check if notification already exists
    const exists = globalNotifications.value.some(n => n.id === notification.id)
    if (exists) return
    
    globalNotifications.value.unshift(notification)
    saveNotifications()
  }

  const loadFriendRequests = async () => {
    try {
      const token = localStorage.getItem('api_token')
      const headers = token ? { Authorization: `Bearer ${token}` } : {}
      
      const response = await axios.get('/api/friends/requests', { headers })
      const newIncoming = response.data.incoming || []
      const newSent = response.data.sent || []
      
      const isInitialLoad = lastKnownIncomingCount.value === -1
      
      // On initial load, create notifications for ALL existing incoming requests
      if (isInitialLoad) {
        const existingIncomingIds = new Set(
          globalNotifications.value
            .filter(n => n.id.startsWith('friend_req_'))
            .map(n => n.id.replace('friend_req_', ''))
        )
        
        // Create notifications for all existing incoming requests that aren't already in localStorage
        newIncoming.forEach(req => {
          if (!existingIncomingIds.has(String(req.id))) {
            addNotification({
              id: `friend_req_${req.id}`,
              type: 'friend_request',
              title: 'New friend request',
              message: `${req.name || req.username} sent you a friend request`,
              timestamp: new Date(req.created_at || Date.now()),
              read: false,
              actionUrl: '/friends'
            })
          }
        })
      } else {
        // On subsequent loads, only create notifications for NEW requests
        const existingIncomingIds = new Set(
          globalNotifications.value
            .filter(n => n.id.startsWith('friend_req_'))
            .map(n => n.id.replace('friend_req_', ''))
        )
        
        const newRequests = newIncoming.filter(req => {
          return !existingIncomingIds.has(String(req.id))
        })
        
        newRequests.forEach(req => {
          addNotification({
            id: `friend_req_${req.id}`,
            type: 'friend_request',
            title: 'New friend request',
            message: `${req.name || req.username} sent you a friend request`,
            timestamp: new Date(),
            read: false,
            actionUrl: '/friends'
          })
        })
        
        // Check for accepted requests (sent requests that disappeared)
        const newSentIds = new Set(newSent.map(r => r.id))
        const oldSentIds = lastKnownSentIds.value
        
        // Find requests that were accepted (in old but not in new)
        const acceptedRequestIds = Array.from(oldSentIds).filter(id => !newSentIds.has(id))
        
        acceptedRequestIds.forEach(reqId => {
          const existingAcceptanceId = `friend_accepted_${reqId}`
          const hasExisting = globalNotifications.value.some(n => n.id.startsWith(existingAcceptanceId))
          
          if (!hasExisting) {
            addNotification({
              id: `${existingAcceptanceId}_${Date.now()}`,
              type: 'friend_request',
              title: 'Friend request accepted',
              message: 'Your friend request was accepted',
              timestamp: new Date(),
              read: false,
              actionUrl: '/friends'
            })
          }
        })
      }
      
      lastKnownIncomingCount.value = newIncoming.length
      lastKnownSentIds.value = new Set(newSent.map(r => r.id))
      
      return { incoming: newIncoming, sent: newSent }
    } catch (error) {
      console.error('Failed to load friend requests:', error)
      return { incoming: [], sent: [] }
    }
  }

  const startNotificationPolling = () => {
    // Only start if not already polling
    if (notificationPollInterval.value) return
    
    // Initial load
    loadFriendRequests()
    
    // Poll every 30 seconds for new friend requests
    notificationPollInterval.value = setInterval(async () => {
      const userId = user.value?.id || user.value?.user_id || currentUserId
      if (userId) {
        await loadFriendRequests()
      }
    }, 30000)
  }

  const stopNotificationPolling = () => {
    if (notificationPollInterval.value) {
      clearInterval(notificationPollInterval.value)
      notificationPollInterval.value = null
    }
  }

  const markAsRead = (id) => {
    const index = globalNotifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      globalNotifications.value[index].read = true
      saveNotifications()
    }
  }

  const markAllAsRead = () => {
    globalNotifications.value.forEach(n => n.read = true)
    saveNotifications()
  }

  const deleteNotification = (id) => {
    globalNotifications.value = globalNotifications.value.filter(n => n.id !== id)
    saveNotifications()
  }

  // Auto-initialize when user becomes available (only once globally)
  watch(() => user.value?.id || user.value?.user_id, (userId) => {
    if (userId && !isInitialized) {
      isInitialized = true
      currentUserId = userId
      loadNotifications()
      startNotificationPolling()
    }
  }, { immediate: true })

  return {
    notifications,
    addNotification,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    loadNotifications,
    startNotificationPolling,
    stopNotificationPolling
  }
}
