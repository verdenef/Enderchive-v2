<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div class="flex items-center gap-3 mb-6">
      <Icon name="lucide:image" class="w-6 h-6" style="color: #8b5cf6" />
      <h3>Proof Gallery</h3>
    </div>
    <div v-if="proofs.length === 0" class="text-center py-8" style="background: rgba(124, 58, 237, 0.05)">
      <Icon name="lucide:image" class="w-12 h-12 mx-auto mb-3 text-white/20" />
      <p class="text-white/60 text-sm">No proofs uploaded yet</p>
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="proof in proofs"
        :key="proof.id"
        class="aspect-square rounded-lg overflow-hidden border-2 border-[#2b1d3a] hover:border-[#7c3aed] transition-all cursor-pointer"
        @click="openProof(proof)"
      >
        <img
          v-if="proof.type === 'screenshot'"
          :src="proof.url"
          :alt="proof.achievementTitle"
          class="w-full h-full object-cover"
        />
        <div
          v-else
          class="w-full h-full flex items-center justify-center"
          style="background: rgba(124, 58, 237, 0.1)"
        >
          <Icon name="lucide:link" class="w-8 h-8 text-white/40" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Icon from '../ui/Icon.vue'

const props = defineProps({
  gameId: {
    type: Number,
    required: true
  },
  gameTitle: {
    type: String,
    default: ''
  },
  proofs: {
    type: Array,
    default: () => []
  }
})

const openProof = (proof) => {
  if (proof.type === 'link' && proof.url) {
    window.open(proof.url, '_blank')
  }
}
</script>

