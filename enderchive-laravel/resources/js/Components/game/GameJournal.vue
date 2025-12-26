<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <Icon name="lucide:book-open" class="w-6 h-6" style="color: #8b5cf6" />
        <h3>Game Journal</h3>
      </div>
      <GameButton variant="primary" size="sm" @click="showAddModal = true">
        <template #icon>
          <Icon name="lucide:plus" class="w-4 h-4" />
        </template>
        Add Entry
      </GameButton>
    </div>
    <div v-if="entries.length === 0" class="text-center py-8" style="background: rgba(124, 58, 237, 0.05)">
      <Icon name="lucide:book-open" class="w-12 h-12 mx-auto mb-3 text-white/20" />
      <p class="text-white/60 text-sm mb-2">No journal entries yet</p>
      <p class="text-white/40 text-xs">Document your gaming journey</p>
    </div>
    <div v-else class="space-y-3">
      <div
        v-for="entry in entries"
        :key="entry.id"
        class="p-4 rounded-lg border-2 border-[#2b1d3a] hover:border-[#7c3aed] transition-all"
        style="background: rgba(26, 26, 46, 0.5)"
      >
        <div class="flex items-start justify-between mb-2">
          <h4 class="font-semibold">{{ entry.title }}</h4>
          <div class="flex items-center gap-2">
            <button
              @click="handleEdit(entry)"
              class="p-1 hover:bg-white/10 rounded transition-colors"
              title="Edit"
            >
              <Icon name="lucide:edit" class="w-4 h-4 text-white/60 hover:text-[#8b5cf6]" />
            </button>
            <button
              @click="$emit('delete-entry', entry.id)"
              class="p-1 hover:bg-white/10 rounded transition-colors"
              title="Delete"
            >
              <Icon name="lucide:trash-2" class="w-4 h-4 text-white/60 hover:text-red-400" />
            </button>
          </div>
        </div>
        <p class="text-sm text-white/60 mb-2">{{ entry.content }}</p>
        <span class="text-xs text-white/40">{{ formatDate(entry.date) }}</span>
      </div>
    </div>

    <!-- Add/Edit Journal Entry Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showAddModal">
          <div
            class="fixed inset-0 z-[9998]"
            style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px)"
            @click="handleCloseModal"
          />
          <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div
              class="glass-panel rounded-lg p-6 border-4 border-[#2b1d3a] max-w-md w-full"
              style="box-shadow: 0 0 40px rgba(124, 58, 237, 0.4)"
              @click.stop
            >
              <div class="flex items-center gap-3 mb-6">
                <Icon name="lucide:book-open" class="w-6 h-6 text-[#8b5cf6]" />
                <h2>{{ editingEntry ? 'Edit Entry' : 'Add Journal Entry' }}</h2>
              </div>

              <div class="space-y-4 mb-6">
                <div>
                  <label class="text-sm mb-2 block text-white/80 font-medium">Title</label>
                  <input
                    v-model="entryForm.title"
                    type="text"
                    placeholder="Entry title"
                    class="w-full px-4 py-3 rounded-lg text-sm"
                    style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white;"
                  />
                </div>

                <div>
                  <label class="text-sm mb-2 block text-white/80 font-medium">Content</label>
                  <textarea
                    v-model="entryForm.content"
                    placeholder="Write your journal entry..."
                    rows="6"
                    class="w-full px-4 py-3 rounded-lg text-sm resize-vertical"
                    style="background: #0f0f23; border: 2px solid rgba(124, 58, 237, 0.3); color: white;"
                  ></textarea>
                </div>
              </div>

              <div class="flex gap-3">
                <GameButton
                  variant="secondary"
                  :full-width="true"
                  @click="handleCloseModal"
                >
                  Cancel
                </GameButton>
                <GameButton
                  variant="primary"
                  :full-width="true"
                  @click="handleSaveEntry"
                  :disabled="!entryForm.title || !entryForm.content"
                >
                  {{ editingEntry ? 'Update' : 'Add' }} Entry
                </GameButton>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  gameId: {
    type: Number,
    required: true
  },
  entries: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['add-entry', 'update-entry', 'delete-entry'])

const showAddModal = ref(false)
const editingEntry = ref(null)

const entryForm = ref({
  title: '',
  content: ''
})

const handleEdit = (entry) => {
  editingEntry.value = entry
  entryForm.value = {
    title: entry.title || '',
    content: entry.content || ''
  }
  showAddModal.value = true
}

const handleCloseModal = () => {
  showAddModal.value = false
  editingEntry.value = null
  entryForm.value = {
    title: '',
    content: ''
  }
}

const handleSaveEntry = () => {
  if (!entryForm.value.title || !entryForm.value.content) return

  const entryData = {
    title: entryForm.value.title,
    content: entryForm.value.content,
    date: new Date().toISOString()
  }

  if (editingEntry.value) {
    emit('update-entry', editingEntry.value.id, entryData)
  } else {
    emit('add-entry', entryData)
  }

  handleCloseModal()
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
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

