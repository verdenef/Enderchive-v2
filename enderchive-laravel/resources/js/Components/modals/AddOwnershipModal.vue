<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen">
        <div
          class="fixed inset-0 z-[9998]"
          style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
          @click="handleClose"
        />
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto">
          <div
            class="w-full max-w-lg rounded-xl flex flex-col max-h-[90vh] my-auto"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="flex items-center justify-between p-6 border-b border-[#2b1d3a] flex-shrink-0">
              <h2>{{ editingOwnership ? 'Edit Copy' : 'Add Copy' }}</h2>
              <button
                @click="handleClose"
                class="p-2 hover:bg-white/10 rounded transition-colors"
              >
                <Icon name="lucide:x" class="w-5 h-5 text-gray-400 hover:text-white" />
              </button>
            </div>
            <div class="p-6 space-y-6 overflow-y-auto flex-1 min-h-0">
              <div v-if="gameCover" class="flex items-center gap-4 p-4 rounded-lg" style="background: rgba(124, 58, 237, 0.1)">
                <img :src="gameCover" :alt="gameTitle" class="w-16 h-24 object-cover rounded-lg" />
                <h3 class="text-lg">{{ gameTitle }}</h3>
              </div>
              
              <div>
                <label class="block text-sm mb-3">
                  Platform <span class="text-red-400">*</span>
                </label>
                <select
                  v-model="platformId"
                  class="w-full px-4 py-3 rounded-lg text-sm appearance-none cursor-pointer"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; padding-right: 40px"
                  @change="storeId = null; loadStores()"
                  :disabled="loading"
                >
                  <option :value="null">Select Platform</option>
                  <option v-for="p in platforms" :key="p.platform_id" :value="p.platform_id">{{ p.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm mb-3">
                  Ownership Type <span class="text-red-400">*</span>
                </label>
                <select
                  v-model="ownershipType"
                  class="w-full px-4 py-3 rounded-lg text-sm appearance-none cursor-pointer"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; padding-right: 40px"
                >
                  <option value="Owned">Owned</option>
                  <option value="Subscription">Subscription</option>
                  <option value="Borrowed">Borrowed</option>
                  <option value="Gifted">Gifted</option>
                </select>
              </div>

              <div>
                <label class="block text-sm mb-3">
                  Media Type <span class="text-red-400">*</span>
                </label>
                <select
                  v-model="mediaType"
                  class="w-full px-4 py-3 rounded-lg text-sm appearance-none cursor-pointer"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; padding-right: 40px"
                >
                  <option value="Digital">Digital</option>
                  <option value="Physical">Physical</option>
                </select>
              </div>

              <div>
                <label class="block text-sm mb-3">
                  Store/Service <span class="text-gray-400 text-xs">(optional)</span>
                </label>
                <select
                  v-model="storeId"
                  :disabled="!platformId || loading"
                  class="w-full px-4 py-3 rounded-lg text-sm appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; padding-right: 40px"
                >
                  <option :value="null">Select Store</option>
                  <option v-for="s in stores" :key="s.store_id" :value="s.store_id">{{ s.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm mb-3">
                  Edition <span class="text-gray-400 text-xs">(optional)</span>
                </label>
                <select
                  v-model="editionId"
                  :disabled="loading"
                  class="w-full px-4 py-3 rounded-lg text-sm appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; padding-right: 40px"
                >
                  <option :value="null">Select Edition</option>
                  <option v-for="e in editions" :key="e.edition_id" :value="e.edition_id">{{ e.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm mb-3">
                  Account Identifier <span class="text-gray-400 text-xs">(optional)</span>
                </label>
                <input
                  v-model="accountIdentifier"
                  type="text"
                  placeholder="e.g., username, email, account ID"
                  class="w-full px-4 py-3 rounded-lg text-sm"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                />
              </div>

              <div>
                <label class="block text-sm mb-3">
                  Purchase Date <span class="text-gray-400 text-xs">(optional)</span>
                </label>
                <input
                  v-model="purchaseDate"
                  type="date"
                  class="w-full px-4 py-3 rounded-lg text-sm"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm mb-3">
                    Purchase Price <span class="text-gray-400 text-xs">(optional)</span>
                  </label>
                  <input
                    v-model="purchasePrice"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="w-full px-4 py-3 rounded-lg text-sm"
                    style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                  />
                </div>
                <div>
                  <label class="block text-sm mb-3">
                    Currency <span class="text-gray-400 text-xs">(optional)</span>
                  </label>
                  <input
                    v-model="purchaseCurrency"
                    type="text"
                    maxlength="3"
                    placeholder="USD"
                    class="w-full px-4 py-3 rounded-lg text-sm uppercase"
                    style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                  />
                </div>
              </div>
            </div>
            <div class="flex gap-4 justify-end p-6 pt-4 border-t border-[#2b1d3a] flex-shrink-0">
              <GameButton variant="purple-outline" @click="handleClose">Cancel</GameButton>
              <GameButton variant="primary" @click="handleSubmit" :disabled="!platformId || loading">
                {{ editingOwnership ? 'Save Changes' : 'Add Copy' }}
              </GameButton>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'
import axios from 'axios'
import { useApi } from '../../composables/useApi'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  gameId: {
    type: Number,
    required: true
  },
  gameTitle: {
    type: String,
    required: true
  },
  gameCover: {
    type: String,
    default: ''
  },
  editingOwnership: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'submit'])

const { getAuthHeaders } = useApi()

const platforms = ref([])
const stores = ref([])
const editions = ref([])
const loading = ref(false)

const platformId = ref(null)
const storeId = ref(null)
const editionId = ref(null)
const ownershipType = ref('Owned')
const mediaType = ref('Digital')
const accountIdentifier = ref('')
const purchaseDate = ref('')
const purchasePrice = ref('')
const purchaseCurrency = ref('')

const loadPlatforms = async () => {
  try {
    const response = await axios.get('/api/platforms')
    platforms.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load platforms:', error)
  }
}

const loadStores = async () => {
  try {
    const response = await axios.get('/api/stores', { headers: getAuthHeaders() })
    stores.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load stores:', error)
    stores.value = []
  }
}

const loadEditions = async () => {
  try {
    const response = await axios.get(`/api/games/${props.gameId}/editions`, { headers: getAuthHeaders() })
    editions.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load editions:', error)
    editions.value = []
  }
}

watch(() => props.isOpen, async (newVal) => {
  if (newVal) {
    loading.value = true
    await Promise.all([loadPlatforms(), loadStores(), loadEditions()])
    
    if (props.editingOwnership) {
      platformId.value = props.editingOwnership.platform_id || null
      storeId.value = props.editingOwnership.store_id || null
      editionId.value = props.editingOwnership.edition_id || null
      ownershipType.value = props.editingOwnership.ownership_type || 'Owned'
      mediaType.value = props.editingOwnership.media_type || 'Digital'
      accountIdentifier.value = props.editingOwnership.account_identifier || ''
      purchaseDate.value = props.editingOwnership.purchase_date ? props.editingOwnership.purchase_date.split('T')[0] : ''
      purchasePrice.value = props.editingOwnership.purchase_price || ''
      purchaseCurrency.value = props.editingOwnership.purchase_currency || ''
    } else {
      platformId.value = null
      storeId.value = null
      editionId.value = null
      ownershipType.value = 'Owned'
      mediaType.value = 'Digital'
      accountIdentifier.value = ''
      purchaseDate.value = ''
      purchasePrice.value = ''
      purchaseCurrency.value = ''
    }
    loading.value = false
  }
})

const handleSubmit = () => {
  if (!platformId.value) return
  
  const data = {
    platform_id: platformId.value,
    ownership_type: ownershipType.value,
    media_type: mediaType.value,
    store_id: storeId.value || null,
    edition_id: editionId.value || null,
    account_identifier: accountIdentifier.value || null,
    purchase_date: purchaseDate.value || null,
    purchase_price: purchasePrice.value ? parseFloat(purchasePrice.value) : null,
    purchase_currency: purchaseCurrency.value ? purchaseCurrency.value.toUpperCase() : null,
  }

  emit('submit', data)
  handleClose()
}

const handleClose = () => {
  emit('close')
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
