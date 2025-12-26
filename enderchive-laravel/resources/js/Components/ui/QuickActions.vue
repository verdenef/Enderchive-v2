<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen">
        <div
          class="fixed inset-0 z-[9998]"
          style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
          @click="close"
        />
        <div class="fixed inset-0 z-[9999] flex items-start justify-center pt-20 p-4">
          <div
            class="w-full max-w-lg rounded-xl"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="p-4 border-b border-[#2b1d3a]">
              <div class="flex items-center gap-2 mb-3">
                <Icon name="lucide:command" class="w-5 h-5 text-[#8b5cf6]" />
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Type to search actions..."
                  class="flex-1 bg-transparent text-white placeholder-white/40 outline-none"
                  autofocus
                  @keydown.enter="handleAction(filteredActions[selectedIndex])"
                  @keydown.arrow-down.prevent="selectedIndex = Math.min(selectedIndex + 1, filteredActions.length - 1)"
                  @keydown.arrow-up.prevent="selectedIndex = Math.max(selectedIndex - 1, 0)"
                  @keydown.escape="close"
                />
                <span class="text-xs text-white/40">ESC to close</span>
              </div>
            </div>
            <div class="max-h-96 overflow-y-auto">
              <div v-if="filteredActions.length > 0" class="p-2">
                <button
                  v-for="(action, index) in filteredActions"
                  :key="action.id"
                  :class="[
                    'w-full flex items-center gap-3 p-3 rounded-lg transition-all text-left',
                    index === selectedIndex ? 'bg-[#7c3aed] text-white' : 'hover:bg-white/5 text-white/80'
                  ]"
                  @click="handleAction(action)"
                >
                  <div class="text-white/60">
                    <Icon :name="action.icon" class="w-4 h-4" />
                  </div>
                  <span class="text-sm">{{ action.label }}</span>
                </button>
              </div>
              <div v-else class="p-8 text-center">
                <Icon name="lucide:search" class="w-12 h-12 mx-auto mb-3 text-white/20" />
                <p class="text-white/60 text-sm">No actions found</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Icon from './Icon.vue'

const isOpen = ref(false)
const searchQuery = ref('')
const selectedIndex = ref(0)

const actions = [
  {
    id: 'home',
    label: 'Go to Home',
    icon: 'lucide:home',
    keywords: ['home', 'main', 'dashboard'],
    action: () => router.visit('/home')
  },
  {
    id: 'library',
    label: 'Go to Library',
    icon: 'lucide:library',
    keywords: ['library', 'games', 'collection'],
    action: () => router.visit('/library')
  },
  {
    id: 'wishlist',
    label: 'Go to Wishlist',
    icon: 'lucide:heart',
    keywords: ['wishlist', 'wish', 'want'],
    action: () => router.visit('/wishlist')
  },
  {
    id: 'friends',
    label: 'Go to Friends',
    icon: 'lucide:users',
    keywords: ['friends', 'social', 'community'],
    action: () => router.visit('/friends')
  },
  {
    id: 'add-game',
    label: 'Add Game to Library',
    icon: 'lucide:plus',
    keywords: ['add', 'new', 'game', 'library'],
    action: () => router.visit('/search')
  }
]

const filteredActions = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return actions.filter(action =>
    action.keywords.some(keyword => keyword.includes(query)) ||
    action.label.toLowerCase().includes(query)
  )
})

watch(searchQuery, () => {
  selectedIndex.value = 0
})

const handleAction = (action) => {
  if (action) {
    action.action()
    close()
  }
}

const close = () => {
  isOpen.value = false
  searchQuery.value = ''
  selectedIndex.value = 0
}

const handleKeyDown = (e) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault()
    isOpen.value = true
  }
  if (e.key === 'Escape' && isOpen.value) {
    close()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>

