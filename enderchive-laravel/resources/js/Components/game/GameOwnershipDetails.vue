<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <Icon name="lucide:package" class="w-6 h-6" style="color: #8b5cf6" />
        <h3>Your Copies</h3>
      </div>
    </div>
    <div v-if="ownerships.length > 0" class="space-y-3 mb-4">
      <div
        v-for="ownership in ownerships"
        :key="ownership.user_game_ownership_id"
        class="flex items-start gap-4 p-4 rounded-lg border-2 border-[#2b1d3a] hover:border-[#7c3aed] transition-all"
        style="background: rgba(26, 26, 46, 0.5)"
      >
        <div
          class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
          style="background: rgba(124, 58, 237, 0.2); border: 2px solid rgba(124, 58, 237, 0.3)"
        >
          <Icon
            :name="getPlatformIcon(ownership.platform?.name)"
            class="w-4 h-4"
          />
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1 flex-wrap">
            <span class="font-semibold whitespace-nowrap">{{ ownership.platform?.name || 'Unknown' }}</span>
            <span class="text-white/40">•</span>
            <span class="text-xs px-2 py-1 rounded whitespace-nowrap" :style="getOwnershipTypeStyle(ownership.ownership_type)">
              {{ ownership.ownership_type || 'Owned' }}
            </span>
            <span class="text-white/40">•</span>
            <span class="text-xs px-2 py-1 rounded whitespace-nowrap" :style="getMediaTypeStyle(ownership.media_type)">
              {{ ownership.media_type || 'Digital' }}
            </span>
            <span v-if="ownership.store" class="text-white/40">•</span>
            <span v-if="ownership.store" class="text-sm text-white/60 truncate">{{ ownership.store.name }}</span>
          </div>
          <div class="flex items-center gap-2 flex-wrap mt-1">
            <span
              v-if="ownership.edition"
              class="text-xs px-2 py-1 rounded whitespace-nowrap"
              style="background: rgba(124, 58, 237, 0.2); color: #a78bfa"
            >
              {{ ownership.edition.name }}
            </span>
            <span v-if="ownership.account_identifier" class="text-xs text-white/50 whitespace-nowrap">
              Account: {{ ownership.account_identifier }}
            </span>
            <span v-if="ownership.purchase_date" class="text-xs text-white/40 whitespace-nowrap">
              Purchased {{ formatDate(ownership.purchase_date) }}
            </span>
            <span v-if="ownership.purchase_price" class="text-xs text-white/40 whitespace-nowrap">
              {{ formatPrice(ownership.purchase_price, ownership.purchase_currency) }}
            </span>
          </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <button
            @click="onEditOwnership?.(ownership)"
            class="p-2 hover:bg-white/10 rounded transition-colors"
            title="Edit"
          >
            <Icon name="lucide:edit" class="w-4 h-4 text-white/60 hover:text-[#8b5cf6]" />
          </button>
          <button
            @click="handleDelete(ownership.user_game_ownership_id)"
            class="p-2 hover:bg-white/10 rounded transition-colors"
            title="Remove"
          >
            <Icon name="lucide:trash-2" class="w-4 h-4 text-white/60 hover:text-red-400" />
          </button>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8 mb-4" style="background: rgba(124, 58, 237, 0.05)">
      <Icon name="lucide:package" class="w-12 h-12 mx-auto mb-3 text-white/20" />
      <p class="text-white/60 text-sm mb-2">No copies added yet</p>
      <p class="text-white/40 text-xs">Track your game ownership across platforms</p>
    </div>
    <GameButton
      variant="primary"
      size="sm"
      @click="onAddOwnership?.()"
    >
      <template #icon>
        <Icon name="lucide:plus" class="w-4 h-4" />
      </template>
      Add Copy
    </GameButton>
    <ConfirmationModal
      :is-open="showDeleteModal"
      type="delete"
      @close="showDeleteModal = false"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'
import ConfirmationModal from '../modals/ConfirmationModal.vue'

const props = defineProps({
  ownerships: {
    type: Array,
    default: () => []
  },
  onAddOwnership: {
    type: Function,
    default: undefined
  },
  onEditOwnership: {
    type: Function,
    default: undefined
  },
  onDeleteOwnership: {
    type: Function,
    default: undefined
  }
})

const emit = defineEmits(['delete'])

const showDeleteModal = ref(false)
const deleteId = ref(null)

const getPlatformIcon = (platformName) => {
  if (!platformName) return 'lucide:package'
  const name = platformName.toLowerCase()
  if (name.includes('pc')) return 'lucide:monitor'
  if (name.includes('mobile') || name.includes('phone')) return 'lucide:smartphone'
  return 'lucide:gamepad-2'
}

const getOwnershipTypeStyle = (ownershipType) => {
  const styles = {
    'Owned': { background: 'rgba(34, 197, 94, 0.2)', color: '#86efac' },
    'Subscription': { background: 'rgba(59, 130, 246, 0.2)', color: '#93c5fd' },
    'Borrowed': { background: 'rgba(251, 191, 36, 0.2)', color: '#fde047' },
    'Gifted': { background: 'rgba(168, 85, 247, 0.2)', color: '#c4b5fd' }
  }
  return styles[ownershipType] || styles['Owned']
}

const getMediaTypeStyle = (mediaType) => {
  if (mediaType === 'Physical') {
    return { background: 'rgba(34, 197, 94, 0.2)', color: '#86efac' }
  }
  return { background: 'rgba(59, 130, 246, 0.2)', color: '#93c5fd' }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
  } catch {
    return dateString
  }
}

const formatPrice = (price, currency) => {
  if (!price) return ''
  const formatted = parseFloat(price).toFixed(2)
  return currency ? `${currency} ${formatted}` : `$${formatted}`
}

const handleDelete = (id) => {
  deleteId.value = id
  showDeleteModal.value = true
}

const confirmDelete = () => {
  if (deleteId.value !== null) {
    props.onDeleteOwnership?.(deleteId.value)
    emit('delete', deleteId.value)
    showDeleteModal.value = false
    deleteId.value = null
  }
}
</script>
