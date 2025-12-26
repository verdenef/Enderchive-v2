<template>
  <div class="space-y-6">
    <div
      class="rounded-2xl p-5 border-2"
      style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
    >
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <Icon name="lucide:folder" class="w-5 h-5 text-[#8b5cf6]" />
          <h3 class="text-white">Collections</h3>
        </div>
        <GameButton
          variant="secondary"
          size="sm"
          @click="showAddCollection = true"
        >
          <template #icon>
            <Icon name="lucide:plus" class="w-4 h-4" />
          </template>
          New Collection
        </GameButton>
      </div>

      <div class="space-y-2">
        <div
          v-for="collection in collections"
          :key="collection.id"
          class="flex items-center justify-between p-3 rounded-lg border transition-all cursor-pointer"
          :style="getCollectionStyle(collection.id)"
          @click="$emit('select-collection', collection.id)"
        >
          <div class="flex items-center gap-3">
            <Icon
              :name="collection.type === 'smart' ? 'lucide:list' : 'lucide:folder'"
              :class="['w-4 h-4', collection.type === 'smart' ? 'text-[#22c55e]' : 'text-[#8b5cf6]']"
            />
            <div>
              <p class="text-sm font-semibold text-white">{{ collection.name }}</p>
              <p class="text-xs text-white/50">
                {{ collection.description || (collection.type === 'smart' ? 'Smart collection' : 'Custom collection') }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-3 min-w-[88px] justify-end">
            <span class="text-xs text-white/60">{{ collection.gameCount }} games</span>
            <button
              v-if="collection.type === 'custom'"
              @click.stop="$emit('delete-collection', collection.id)"
              class="flex items-center justify-center w-5 h-5 hover:bg-white/10 rounded transition-colors"
            >
              <Icon name="lucide:x" class="w-3 h-3 text-red-400" />
            </button>
            <span v-else class="w-5 h-5" />
          </div>
        </div>
      </div>

      <div
        v-if="showAddCollection"
        class="mt-4 p-3 rounded-lg border border-[#2b1d3a]"
        style="background: rgba(27, 22, 38, 0.5)"
      >
        <GameInput
          v-model="newCollectionName"
          placeholder="Collection name..."
          class="mb-2"
        />
        <div class="flex gap-2">
          <GameButton variant="primary" size="sm" @click="handleAddCollection">
            Add
          </GameButton>
          <GameButton variant="purple-outline" size="sm" @click="showAddCollection = false; newCollectionName = ''">
            Cancel
          </GameButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import GameInput from '../forms/GameInput.vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  collections: {
    type: Array,
    default: () => []
  },
  selectedCollectionId: {
    type: [Number, null],
    default: null
  }
})

const emit = defineEmits(['add-collection', 'delete-collection', 'select-collection'])

const showAddCollection = ref(false)
const newCollectionName = ref('')

const handleAddCollection = () => {
  if (!newCollectionName.value.trim()) return
  emit('add-collection', newCollectionName.value, 'custom')
  newCollectionName.value = ''
  showAddCollection.value = false
}

const getCollectionStyle = (id) => {
  const isSelected = props.selectedCollectionId === id
  return {
    background: 'rgba(27, 22, 38, 0.5)',
    borderColor: isSelected ? 'rgba(124, 58, 237, 0.8)' : '#2b1d3a',
    boxShadow: isSelected
      ? '0 0 18px rgba(124, 58, 237, 0.45), inset 0 0 0 1px rgba(124, 58, 237, 0.4)'
      : '0 0 0 rgba(0,0,0,0)'
  }
}
</script>

