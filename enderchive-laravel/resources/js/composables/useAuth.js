import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Authentication composable for user state
 */
export function useAuth() {
  const page = usePage()
  
  const user = computed(() => page.props.auth?.user)
  const isGuest = computed(() => !user.value)
  const isAdmin = computed(() => user.value?.is_admin === true)
  const username = computed(() => user.value?.username || user.value?.name || 'Guest')
  const userId = computed(() => user.value?.id || user.value?.user_id || null)
  
  return {
    user,
    isGuest,
    isAdmin,
    username,
    userId,
    page
  }
}

