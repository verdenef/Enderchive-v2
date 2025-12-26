<template>
  <div
    :class="[
      'glass-panel rounded-lg overflow-hidden transition-all duration-300 hover:scale-[1.02] group border-4 flex flex-col',
      bulkSelectMode && isSelected ? 'border-[#22c55e]' : 'border-[#2b1d3a]'
    ]"
    :style="cardStyle"
  >
    <div
      class="aspect-video w-full overflow-hidden bg-black/40 cursor-pointer relative"
      @click="bulkSelectMode ? $emit('toggle-select') : $emit('click')"
    >
      <ImageWithFallback
        :src="image"
        :alt="title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div
        v-if="bulkSelectMode"
        class="absolute top-2 right-2 z-10"
        @click.stop="$emit('toggle-select')"
      >
        <input
          type="checkbox"
          :checked="isSelected"
          @change="$emit('toggle-select')"
          @click.stop
          class="w-6 h-6 rounded accent-[#22c55e] cursor-pointer"
          style="box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5)"
        />
      </div>
      <div
        v-if="bulkSelectMode && isSelected"
        class="absolute inset-0 bg-[#22c55e]/20 pointer-events-none"
      />
    </div>

    <div class="p-4 flex flex-col flex-1">
      <h3
        class="line-clamp-1 cursor-pointer hover:text-[#22c55e] transition-colors mb-3"
        @click="$emit('click')"
      >
        {{ title }}
      </h3>

      <div class="flex-1">
        <p class="text-white/60 text-xs">
          {{ date }} • {{ genres }}
        </p>
      </div>

      <div class="space-y-3 mt-3">
        <div v-if="showStatus" class="relative" ref="dropdownContainer">
          <button
            type="button"
            class="w-full flex items-center justify-between px-4 py-2 rounded-xl transition-all duration-200 hover:brightness-110 active:scale-[0.98] text-sm font-bold"
            :style="{
              background: getStatusColor(currentStatus),
              border: `2px solid ${getStatusColor(currentStatus)}`,
              color: 'white',
              height: '36px',
              boxShadow: '0 4px 10px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.18), inset 0 -1px 2px rgba(0, 0, 0, 0.08)'
            }"
            @click.stop="handleToggleDropdown"
          >
            <span>{{ currentStatus }}</span>
            <Icon name="lucide:chevron-down" class="w-4 h-4" />
          </button>
          
          <Teleport to="body">
            <Transition name="dropdown">
              <div
                v-if="showDropdown"
                ref="dropdownMenu"
                class="fixed rounded-lg border-2 border-[#2b1d3a] z-[9999] overflow-hidden"
                :style="{
                  background: 'rgba(27, 22, 38, 0.98)',
                  backdropFilter: 'blur(12px)',
                  boxShadow: '0 8px 32px rgba(124, 58, 237, 0.5)',
                  ...dropdownStyle
                }"
                @click.stop
              >
                <button
                  @click.stop="handleStatusChange('Playing')"
                  class="w-full px-4 py-2 text-left text-sm transition-colors cursor-pointer hover:bg-[rgba(34,197,94,0.2)] focus:bg-[rgba(34,197,94,0.2)]"
                  style="color: #15803d"
                >
                  Playing
                </button>
                <div class="h-px bg-[#2b1d3a]" />
                <button
                  @click.stop="handleStatusChange('Completed')"
                  class="w-full px-4 py-2 text-left text-sm transition-colors cursor-pointer hover:bg-[rgba(139,92,246,0.2)] focus:bg-[rgba(139,92,246,0.2)]"
                  style="color: #5b21b6"
                >
                  Completed
                </button>
                <div class="h-px bg-[#2b1d3a]" />
                <button
                  @click.stop="handleStatusChange('Abandoned')"
                  class="w-full px-4 py-2 text-left text-sm transition-colors cursor-pointer hover:bg-[rgba(239,68,68,0.2)] focus:bg-[rgba(239,68,68,0.2)]"
                  style="color: #991b1b"
                >
                  Abandoned
                </button>
              </div>
            </Transition>
          </Teleport>
        </div>

        <div class="flex items-center gap-3">
          <GameButton
            variant="danger"
            :full-width="true"
            @click.stop="$emit('remove')"
          >
            <template #icon>
              <Icon name="lucide:trash-2" class="w-5 h-5" />
            </template>
            Remove
          </GameButton>
          <FavoriteButton
            :is-favorite="isFavorite"
            @click="$emit('toggle-favorite')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onUnmounted, nextTick } from 'vue'
import { Teleport } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'
import ImageWithFallback from '../ui/ImageWithFallback.vue'
import FavoriteButton from '../buttons/FavoriteButton.vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  image: {
    type: String,
    required: true
  },
  date: {
    type: String,
    required: true
  },
  genres: {
    type: String,
    required: true
  },
  status: {
    type: String,
    default: 'Playing'
  },
  showStatus: {
    type: Boolean,
    default: true
  },
  bulkSelectMode: {
    type: Boolean,
    default: false
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  isFavorite: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['status-change', 'remove', 'click', 'toggle-select', 'toggle-favorite'])

const currentStatus = ref(props.status)
const showDropdown = ref(false)
const dropdownContainer = ref(null)
const dropdownMenu = ref(null)
const dropdownStyle = ref({})

watch(() => props.status, (newStatus) => {
  currentStatus.value = newStatus
})

const getStatusColor = (status) => {
  switch (status) {
    case 'Playing':
      return '#15803d'
    case 'Completed':
      return '#5b21b6'
    case 'Abandoned':
      return '#991b1b'
    default:
      return '#15803d'
  }
}

const updateDropdownPosition = async () => {
  if (!showDropdown.value || !dropdownContainer.value) return
  
  await nextTick()
  if (!dropdownMenu.value) return
  
  const containerRect = dropdownContainer.value.getBoundingClientRect()
  dropdownStyle.value = {
    top: `${containerRect.bottom + 4}px`,
    left: `${containerRect.left}px`,
    width: `${containerRect.width}px`
  }
}

const handleClickOutside = (event) => {
  if (showDropdown.value && dropdownContainer.value && !dropdownContainer.value.contains(event.target) && 
      dropdownMenu.value && !dropdownMenu.value.contains(event.target)) {
    showDropdown.value = false
  }
}

watch(showDropdown, async (isOpen) => {
  if (isOpen) {
    await nextTick()
    await updateDropdownPosition()
    // Use setTimeout to ensure DOM is fully rendered
    setTimeout(() => {
      updateDropdownPosition()
    }, 0)
    window.addEventListener('scroll', updateDropdownPosition, true)
    window.addEventListener('resize', updateDropdownPosition)
    document.addEventListener('click', handleClickOutside)
  } else {
    window.removeEventListener('scroll', updateDropdownPosition, true)
    window.removeEventListener('resize', updateDropdownPosition)
    document.removeEventListener('click', handleClickOutside)
  }
})

const handleToggleDropdown = async () => {
  if (!showDropdown.value) {
    // Set position before showing
    await nextTick()
    if (dropdownContainer.value) {
      const containerRect = dropdownContainer.value.getBoundingClientRect()
      dropdownStyle.value = {
        top: `${containerRect.bottom + 4}px`,
        left: `${containerRect.left}px`,
        width: `${containerRect.width}px`
      }
    }
    // Small delay to ensure position is set before showing
    await nextTick()
  }
  showDropdown.value = !showDropdown.value
}

const handleStatusChange = (status) => {
  currentStatus.value = status
  showDropdown.value = false
  emit('status-change', status)
}

onUnmounted(() => {
  window.removeEventListener('scroll', updateDropdownPosition, true)
  window.removeEventListener('resize', updateDropdownPosition)
  document.removeEventListener('click', handleClickOutside)
})

const cardStyle = computed(() => ({
  boxShadow: props.bulkSelectMode && props.isSelected
    ? '0 4px 20px rgba(34, 197, 94, 0.5)'
    : '0 4px 20px rgba(124, 58, 237, 0.2)',
}))

</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s ease;
}

.dropdown-enter-from {
  opacity: 0;
}

.dropdown-leave-to {
  opacity: 0;
}
</style>
