<template>
  <div>
    <div
      class="glass-panel rounded-2xl p-6"
    >
      <div class="flex items-start justify-between mb-4 gap-4 flex-wrap">
        <h3 class="flex-shrink-0">Socials</h3>
        <div v-if="isOwnProfile" class="flex-shrink-0">
          <GameButton
            variant="outline"
            size="sm"
            @click="showAddModal = true"
            class="whitespace-nowrap"
          >
            <template #icon>
              <Icon name="lucide:plus" class="w-4 h-4" />
            </template>
            Add Link
          </GameButton>
        </div>
      </div>

      <div v-if="visibleLinks.length > 0" class="space-y-3">
        <div
          v-for="link in visibleLinks"
          :key="link.id"
          class="flex items-start gap-4 p-4 rounded-lg border-2 transition-all"
          :style="{
            borderColor: '#2b1d3a',
            background: 'rgba(26, 26, 46, 0.5)'
          }"
          @mouseenter="(e) => e.currentTarget.style.borderColor = '#7c3aed'"
          @mouseleave="(e) => e.currentTarget.style.borderColor = '#2b1d3a'"
        >
          <!-- Platform Icon -->
          <div
            class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl flex-shrink-0"
            style="background: rgba(124, 58, 237, 0.2); border: 2px solid rgba(124, 58, 237, 0.3)"
          >
            {{ getPlatformIcon(link.platform) }}
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <span class="font-semibold whitespace-nowrap">{{ getPlatformLabel(link.platform) }}</span>
              <span v-if="link.username" class="text-white/40">•</span>
              <span v-if="link.username" class="text-sm text-white/60 truncate">{{ link.username }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-1">
              <a
                :href="link.url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-xs text-[#8b5cf6] hover:text-[#a78bfa] inline-flex items-center gap-1 whitespace-nowrap"
                style="max-width: 100%"
                :title="link.url"
              >
                <span class="whitespace-nowrap">View Link</span>
                <Icon name="lucide:external-link" class="w-3 h-3 flex-shrink-0" />
              </a>
              <div v-if="isOwnProfile" class="flex items-center gap-1 text-xs text-white/40 whitespace-nowrap w-full sm:w-auto">
                <Icon :name="getPrivacyIcon(link.privacy)" class="w-3 h-3 flex-shrink-0" />
                <span class="capitalize">{{ link.privacy }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div v-if="isOwnProfile" class="flex gap-2 flex-shrink-0">
            <button
              @click="handleEdit(link)"
              class="p-2 rounded hover:bg-white/10 transition-colors flex-shrink-0"
              title="Edit"
            >
              <Icon name="lucide:edit" class="w-4 h-4" style="color: #60a5fa" />
            </button>
            <button
              @click="handleDelete(link.id)"
              class="p-2 rounded hover:bg-white/10 transition-colors flex-shrink-0"
              title="Remove"
            >
              <Icon name="lucide:trash-2" class="w-4 h-4" style="color: #f87171" />
            </button>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8">
        <Icon name="lucide:globe" class="w-12 h-12 mx-auto mb-3 text-white/20" />
        <p class="text-white/60 text-sm">
          {{ isOwnProfile ? 'No social links added yet' : 'No social links available' }}
        </p>
        <p v-if="isOwnProfile" class="text-white/40 text-xs mt-1">Add your gaming and social profiles</p>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showAddModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)" @click="showAddModal = false">
          <div
            class="w-full max-w-md rounded-xl"
            style="background: rgba(26, 26, 46, 0.98); backdrop-filter: blur(12px); border: 2px solid rgba(124, 58, 237, 0.5); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
            @click.stop
          >
            <div class="flex items-center justify-between p-4 border-b border-[#2b1d3a]">
              <h2>{{ editingLink ? 'Edit Social Link' : 'Add Social Link' }}</h2>
              <button @click="showAddModal = false; editingLink = null" class="p-2 hover:bg-white/10 rounded transition-colors">
                <Icon name="lucide:x" class="w-5 h-5 text-gray-400 hover:text-white" />
              </button>
            </div>

            <div class="p-4 space-y-4">
              <div>
                <label class="block text-sm mb-2">Platform <span class="text-red-400">*</span></label>
                <select
                  v-model="formData.platform"
                  class="w-full px-3 py-2 rounded-lg text-sm appearance-none cursor-pointer"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white; padding-right: 40px"
                >
                  <option value="">Select Platform</option>
                  <option v-for="p in platforms" :key="p.value" :value="p.value">
                    {{ p.icon }} {{ p.label }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm mb-2">URL <span class="text-red-400">*</span></label>
                <input
                  v-model="formData.url"
                  type="url"
                  placeholder="https://example.com/yourprofile"
                  class="w-full px-3 py-2 rounded-lg text-sm"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                />
              </div>

              <div>
                <label class="block text-sm mb-2">Username <span class="text-gray-400 text-xs">(optional)</span></label>
                <input
                  v-model="formData.username"
                  type="text"
                  placeholder="Your username on this platform"
                  class="w-full px-3 py-2 rounded-lg text-sm"
                  style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white"
                />
              </div>

              <div>
                <label class="block text-sm mb-2">Privacy</label>
                <div class="space-y-1.5">
                  <label
                    v-for="option in privacyOptions"
                    :key="option.value"
                    class="flex items-center gap-2 p-2 rounded-lg cursor-pointer transition-colors hover:bg-white/5"
                    :style="{
                      background: formData.privacy === option.value ? 'rgba(124, 58, 237, 0.1)' : 'transparent',
                      border: `2px solid ${formData.privacy === option.value ? 'rgba(124, 58, 237, 0.5)' : 'rgba(124, 58, 237, 0.2)'}`
                    }"
                  >
                    <input
                      type="radio"
                      v-model="formData.privacy"
                      :value="option.value"
                      class="sr-only"
                    />
                    <Icon :name="option.icon" class="w-4 h-4" style="color: #8b5cf6" />
                    <div class="flex-1">
                      <div class="font-semibold text-sm">{{ option.label }}</div>
                      <div class="text-xs text-gray-400">{{ option.description }}</div>
                    </div>
                    <div v-if="formData.privacy === option.value" class="w-2 h-2 rounded-full" style="background: #8b5cf6" />
                  </label>
                </div>
              </div>

              <div class="flex gap-3 justify-end pt-3">
                <GameButton variant="outline" @click="showAddModal = false; editingLink = null">
                  Cancel
                </GameButton>
                <GameButton variant="primary" @click="handleSave" :disabled="!formData.platform || !formData.url">
                  {{ editingLink ? 'Save Changes' : 'Add Link' }}
                </GameButton>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :is-open="showDeleteModal"
      type="delete"
      title="Remove Social Link?"
      message="This will remove this social link from your profile."
      @close="showDeleteModal = false; linkToDelete = null"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import Icon from '../ui/Icon.vue'
import GameButton from '../buttons/GameButton.vue'
import ConfirmationModal from '../modals/ConfirmationModal.vue'

const props = defineProps({
  links: {
    type: Array,
    default: () => []
  },
  isOwnProfile: {
    type: Boolean,
    default: true
  },
  viewerIsFriend: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:links'])

const showAddModal = ref(false)
const editingLink = ref(null)
const showDeleteModal = ref(false)
const linkToDelete = ref(null)

const formData = ref({
  platform: '',
  url: '',
  username: '',
  privacy: 'public'
})

const platforms = [
  { value: 'steam', label: 'Steam', icon: '🎮' },
  { value: 'discord', label: 'Discord', icon: '💬' },
  { value: 'xbox', label: 'Xbox Live', icon: '🎯' },
  { value: 'playstation', label: 'PlayStation', icon: '🎮' },
  { value: 'nintendo', label: 'Nintendo Switch', icon: '🎮' },
  { value: 'epic', label: 'Epic Games', icon: '🎮' },
  { value: 'twitch', label: 'Twitch', icon: '📺' },
  { value: 'youtube', label: 'YouTube', icon: '▶️' },
  { value: 'twitter', label: 'Twitter/X', icon: '🐦' },
  { value: 'instagram', label: 'Instagram', icon: '📷' },
  { value: 'tiktok', label: 'TikTok', icon: '🎵' },
  { value: 'reddit', label: 'Reddit', icon: '🔴' },
  { value: 'custom', label: 'Custom', icon: '🔗' }
]

const privacyOptions = [
  { value: 'public', label: 'Public', icon: 'lucide:globe', description: 'Visible to everyone' },
  { value: 'friends', label: 'Friends Only', icon: 'lucide:users', description: 'Visible to friends only' },
  { value: 'private', label: 'Private', icon: 'lucide:lock', description: 'Only visible to you' }
]

const visibleLinks = computed(() => {
  if (props.isOwnProfile) return props.links
  return props.links.filter(link => {
    if (link.privacy === 'public') return true
    if (link.privacy === 'friends' && props.viewerIsFriend) return true
    return false
  })
})

const getPlatformIcon = (platform) => {
  return platforms.find(p => p.value === platform)?.icon || '🔗'
}

const getPlatformLabel = (platform) => {
  return platforms.find(p => p.value === platform)?.label || platform
}

const getPrivacyIcon = (privacy) => {
  return privacyOptions.find(p => p.value === privacy)?.icon || 'lucide:globe'
}

const handleEdit = (link) => {
  editingLink.value = link
  formData.value = {
    platform: link.platform,
    url: link.url,
    username: link.username || '',
    privacy: link.privacy
  }
  showAddModal.value = true
}

const handleDelete = (id) => {
  linkToDelete.value = id
  showDeleteModal.value = true
}

const confirmDelete = () => {
  if (linkToDelete.value) {
    const updatedLinks = props.links.filter(l => l.id !== linkToDelete.value)
    emit('update:links', updatedLinks)
    showDeleteModal.value = false
    linkToDelete.value = null
  }
}

const handleSave = () => {
  if (!formData.value.platform || !formData.value.url) {
    alert('Please fill in platform and URL')
    return
  }

  try {
    new URL(formData.value.url)
  } catch {
    alert('Please enter a valid URL (e.g., https://example.com)')
    return
  }

  const linkData = {
    platform: formData.value.platform,
    url: formData.value.url,
    username: formData.value.username.trim() || undefined,
    privacy: formData.value.privacy
  }

  if (editingLink.value) {
    const updatedLinks = props.links.map(l => 
      l.id === editingLink.value.id ? { ...linkData, id: editingLink.value.id } : l
    )
    emit('update:links', updatedLinks)
  } else {
    const newId = Math.max(...props.links.map(l => l.id), 0) + 1
    emit('update:links', [...props.links, { ...linkData, id: newId }])
  }

  showAddModal.value = false
  editingLink.value = null
  formData.value = {
    platform: '',
    url: '',
    username: '',
    privacy: 'public'
  }
}

watch(showAddModal, (val) => {
  if (!val) {
    editingLink.value = null
    formData.value = {
      platform: '',
      url: '',
      username: '',
      privacy: 'public'
    }
  }
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

