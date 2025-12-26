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
              <div class="flex items-center gap-3">
                <Icon name="lucide:clock" class="w-5 h-5 text-[#22c55e]" />
                <h2>Edit Playtime</h2>
              </div>
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
                <div>
                  <h3 class="text-lg">{{ gameTitle }}</h3>
                  <p class="text-sm text-white/60">Current: {{ currentPlaytime }}h</p>
                </div>
              </div>
              <div>
                <label class="block text-sm mb-3">
                  Playtime (hours) <span class="text-red-400">*</span>
                </label>
                <GameInput
                  v-model="playtimeHours"
                  type="number"
                  min="0"
                  step="0.1"
                  placeholder="Enter playtime in hours"
                  class="w-full"
                  @input="errors.playtime = undefined"
                />
                <p v-if="errors.playtime" class="text-red-400 text-xs mt-1">{{ errors.playtime }}</p>
              </div>
            </div>
            <div class="flex gap-4 justify-end p-6 pt-4 border-t border-[#2b1d3a] flex-shrink-0">
              <GameButton variant="purple-outline" @click="handleClose">Cancel</GameButton>
              <GameButton variant="primary" @click="handleSubmit">Save Playtime</GameButton>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import GameInput from '../forms/GameInput.vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  gameTitle: {
    type: String,
    required: true
  },
  currentPlaytime: {
    type: Number,
    required: true
  },
  gameCover: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close', 'submit'])

const playtimeHours = ref(props.currentPlaytime.toString())
const errors = ref({})

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    playtimeHours.value = props.currentPlaytime.toString()
  }
})

const handleSubmit = () => {
  const newErrors = {}
  const playtimeNum = parseFloat(playtimeHours.value)
  
  if (isNaN(playtimeNum) || playtimeNum < 0) {
    newErrors.playtime = 'Please enter a valid playtime (0 or greater)'
  }
  
  if (Object.keys(newErrors).length > 0) {
    errors.value = newErrors
    return
  }
  
  emit('submit', {
    playtimeHours: playtimeNum
  })
  handleClose()
}

const handleClose = () => {
  playtimeHours.value = props.currentPlaytime.toString()
  errors.value = {}
  emit('close')
}
</script>

<style scoped>
.modal-enter-active {
  animation: modalIn 0.3s ease-out;
}

.modal-leave-active {
  animation: modalOut 0.2s ease-in;
}

@keyframes modalIn {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes modalOut {
  from {
    transform: scale(1);
    opacity: 1;
  }
  to {
    transform: scale(0.95);
    opacity: 0;
  }
}
</style>

