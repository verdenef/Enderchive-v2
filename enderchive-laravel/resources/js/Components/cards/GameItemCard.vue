<template>
  <div
    :class="[
      'overflow-hidden group transition-all duration-200 hover:brightness-110 hover:-translate-y-1 inventory-slide flex flex-col',
      justAdded ? 'minecraft-pulse' : ''
    ]"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border: 3px solid rgba(37, 0, 64, 0.9); border-radius: 8px; box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.08), inset -2px -2px 0 rgba(0, 0, 0, 0.25), 0 8px 24px rgba(124, 58, 237, 0.25)"
  >
    <div
      class="relative aspect-video overflow-hidden cursor-pointer"
      @click="$emit('click')"
    >
      <ImageWithFallback
        :src="image"
        :alt="title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
    </div>
    <div class="p-4 flex flex-col flex-1">
      <h3
        class="mb-2 cursor-pointer hover:text-[#22c55e] transition-colors text-sm font-semibold leading-snug"
        @click="$emit('click')"
      >
        {{ title }}
      </h3>
      <div class="flex-1 text-xs text-white/80 leading-snug">
        <p v-if="date" class="mb-1">{{ formattedDate }}</p>
        <p v-if="genres">{{ genres }}</p>
      </div>
      <div class="flex gap-2 mt-4">
        <div class="flex-1">
          <GameButton
            :disabled="isInLibrary"
            :variant="isInLibrary ? 'secondary' : 'primary'"
            :full-width="true"
            size="sm"
            @click.stop="handleAdd"
          >
            <template #icon>
              <Icon :name="isInLibrary ? 'lucide:check' : 'lucide:plus'" class="w-4 h-4" />
            </template>
            {{ isInLibrary ? 'Added' : 'Add' }}
          </GameButton>
        </div>
        <FavoriteButton
          :is-favorite="isFavorite"
          @click="$emit('favorite')"
        />
        <WishlistButton
          :is-wishlisted="isWishlistedLocal"
          :is-in-library="isInLibrary"
          @click="handleWishlist"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import GameButton from '../buttons/GameButton.vue'
import Icon from '../ui/Icon.vue'
import ImageWithFallback from '../ui/ImageWithFallback.vue'
import FavoriteButton from '../buttons/FavoriteButton.vue'
import WishlistButton from '../buttons/WishlistButton.vue'

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
    default: undefined
  },
  genres: {
    type: String,
    default: undefined
  },
  isInLibrary: {
    type: Boolean,
    default: false
  },
  isFavorite: {
    type: Boolean,
    default: false
  },
  isWishlisted: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['add', 'wishlist', 'favorite', 'click'])

const isWishlistedLocal = ref(props.isWishlisted)
const justAdded = ref(false)

const formatDate = (dateString) => {
  if (!dateString || dateString === 'TBA') return dateString
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return dateString
    // Format as YYYY-MM-DD (just the date part, no time)
    return date.toISOString().split('T')[0]
  } catch (error) {
    return dateString
  }
}

const formattedDate = computed(() => formatDate(props.date))

watch(() => props.isWishlisted, (newVal) => {
  isWishlistedLocal.value = newVal
})

const handleAdd = () => {
  justAdded.value = true
  setTimeout(() => { justAdded.value = false }, 600)
  emit('add')
}

const handleWishlist = () => {
  const isDisabled = props.isInLibrary && !isWishlistedLocal.value
  if (isDisabled) return
  isWishlistedLocal.value = !isWishlistedLocal.value
  emit('wishlist')
}
</script>

