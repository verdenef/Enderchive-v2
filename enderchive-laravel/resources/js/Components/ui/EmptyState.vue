<template>
  <div class="flex flex-col items-center justify-center py-16 px-4 inventory-slide relative">
    <div
      class="mb-8 p-8 rounded-full relative animate-pulse"
      style="background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, rgba(124, 58, 237, 0.05) 70%); box-shadow: 0 0 40px rgba(124, 58, 237, 0.4), inset 0 0 20px rgba(124, 58, 237, 0.1)"
    >
      <div
        class="absolute inset-0 rounded-full"
        style="border: 2px solid rgba(124, 58, 237, 0.3); animation: minecraft-pulse 2s ease-in-out infinite"
      />
      <Icon
        :name="iconName"
        class="w-20 h-20 relative z-10"
        style="color: #8b5cf6; filter: drop-shadow(0 0 10px rgba(139, 92, 246, 0.5))"
      />
    </div>

    <h2 class="mb-3 text-center text-2xl">{{ heading }}</h2>

    <p v-if="subtext" class="text-base text-white/60 mb-10 text-center max-w-md leading-relaxed">
      {{ subtext }}
    </p>

    <div v-if="ctaText && onCtaClick" class="flex flex-col sm:flex-row gap-4">
      <GameButton
        :variant="type === 'library' || type === 'wishlist' ? 'primary' : 'secondary'"
        @click="onCtaClick"
        class="hover-lift"
      >
        {{ ctaText }}
      </GameButton>
    </div>

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div
        class="absolute top-1/4 left-1/4 w-2 h-2 rounded-full"
        style="background: #8b5cf6; animation: float-up 3s ease-in-out infinite; animation-delay: 0s"
      />
      <div
        class="absolute top-1/3 right-1/3 w-2 h-2 rounded-full"
        style="background: #22c55e; animation: float-up 3s ease-in-out infinite; animation-delay: 1s"
      />
      <div
        class="absolute bottom-1/4 right-1/4 w-2 h-2 rounded-full"
        style="background: #8b5cf6; animation: float-up 3s ease-in-out infinite; animation-delay: 2s"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Icon from './Icon.vue'
import GameButton from '../buttons/GameButton.vue'

const props = defineProps({
  type: {
    type: String,
    default: 'custom'
  },
  customIcon: {
    type: String,
    default: undefined
  },
  heading: {
    type: String,
    default: undefined
  },
  subtext: {
    type: String,
    default: undefined
  },
  ctaText: {
    type: String,
    default: undefined
  },
  onCtaClick: {
    type: Function,
    default: undefined
  }
})

const configs = {
  library: {
    icon: 'lucide:package',
    heading: 'Your library is empty',
    subtext: 'Start building your collection by adding games',
    ctaText: 'Explore Games'
  },
  wishlist: {
    icon: 'lucide:heart',
    heading: 'No games in your wishlist',
    subtext: 'Add games you want to play later',
    ctaText: 'Browse Games'
  },
  friends: {
    icon: 'lucide:user-plus',
    heading: 'No friends yet',
    subtext: 'Connect with other gamers',
    ctaText: 'Find Friends'
  },
  search: {
    icon: 'lucide:search',
    heading: 'No games found',
    subtext: 'Try different keywords or filters',
    ctaText: 'Clear Filters'
  },
  custom: {
    icon: props.customIcon || 'lucide:package',
    heading: props.heading || 'No items found',
    subtext: props.subtext || '',
    ctaText: props.ctaText || 'Go Back'
  }
}

const config = computed(() => configs[props.type])
const iconName = computed(() => config.value.icon)
const heading = computed(() => config.value.heading)
const subtext = computed(() => config.value.subtext)
const ctaText = computed(() => config.value.ctaText)
</script>

