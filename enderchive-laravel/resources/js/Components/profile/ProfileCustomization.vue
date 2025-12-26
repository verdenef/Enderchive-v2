<template>
  <div
    class="rounded-2xl p-5 border-2"
    style="background: rgba(27, 22, 38, 0.6); backdrop-filter: blur(10px); border-color: rgba(43, 29, 58, 0.7); box-shadow: inset 2px 2px 0 rgba(255, 255, 255, 0.1), inset -2px -2px 0 rgba(0, 0, 0, 0.2), 0 8px 24px rgba(124, 58, 237, 0.25);"
  >
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <Icon name="lucide:palette" class="w-5 h-5" style="color: #8b5cf6" />
        <h3 class="text-white">Theme & Layout</h3>
      </div>
      <button
        @click="showPreview = !showPreview"
        class="flex items-center gap-2 px-3 py-1 rounded-lg text-xs transition-all hover:bg-white/5"
        style="border: 1px solid rgba(43, 29, 58, 0.5)"
      >
        <Icon name="lucide:eye" class="w-4 h-4" />
        {{ showPreview ? 'Hide' : 'Show' }} Preview
      </button>
    </div>

    <!-- Theme Selection -->
    <div class="mb-6">
      <h4 class="text-sm font-semibold text-white mb-3">Theme</h4>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <button
          v-for="theme in themes"
          :key="theme.id"
          @click="handleThemeSelect(theme.id)"
          :class="[
            'p-3 rounded-lg border-2 transition-all text-left',
            selectedTheme === theme.id ? 'border-[#7c3aed]' : 'border-[#2b1d3a] hover:border-[#7c3aed]/50'
          ]"
          :style="{
            background: selectedTheme === theme.id ? 'rgba(124, 58, 237, 0.2)' : 'rgba(27, 22, 38, 0.5)'
          }"
        >
          <div class="flex items-center gap-2 mb-2">
            <div
              class="w-4 h-4 rounded-full"
              :style="{ background: theme.primaryColor }"
            />
            <div
              class="w-4 h-4 rounded-full"
              :style="{ background: theme.secondaryColor }"
            />
          </div>
          <p class="text-xs font-semibold text-white mb-1">{{ theme.name }}</p>
          <p class="text-xs text-white/50">{{ theme.preview }}</p>
        </button>
      </div>
    </div>

    <!-- Layout Selection -->
    <div class="mb-6">
      <h4 class="text-sm font-semibold text-white mb-3">Layout</h4>
      <div class="space-y-2">
        <button
          v-for="layout in layouts"
          :key="layout.id"
          @click="handleLayoutSelect(layout.id)"
          :class="[
            'w-full p-3 rounded-lg border-2 transition-all text-left',
            selectedLayout === layout.id ? 'border-[#7c3aed]' : 'border-[#2b1d3a] hover:border-[#7c3aed]/50'
          ]"
          :style="{
            background: selectedLayout === layout.id ? 'rgba(124, 58, 237, 0.2)' : 'rgba(27, 22, 38, 0.5)'
          }"
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-white mb-1">{{ layout.name }}</p>
              <p class="text-xs text-white/50">{{ layout.description }}</p>
            </div>
            <Icon name="lucide:layout" class="w-5 h-5 text-white/40" />
          </div>
        </button>
      </div>
    </div>

    <!-- Preview -->
    <div v-if="showPreview" class="mb-6 p-4 rounded-lg border border-[#2b1d3a]" style="background: rgba(27, 22, 38, 0.5)">
      <p class="text-xs text-white/60 mb-2">Preview</p>
      <div class="text-xs text-white/40">
        Theme: {{ themes.find(t => t.id === selectedTheme)?.name }} • 
        Layout: {{ layouts.find(l => l.id === selectedLayout)?.name }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import Icon from '../ui/Icon.vue'

const props = defineProps({
  currentTheme: {
    type: String,
    default: 'default'
  },
  currentLayout: {
    type: String,
    default: 'standard'
  }
})

const emit = defineEmits(['theme-change', 'layout-change', 'save'])

const selectedTheme = ref(props.currentTheme)
const selectedLayout = ref(props.currentLayout)
const showPreview = ref(false)

const themes = [
  {
    id: 'default',
    name: 'End Dimension',
    primaryColor: '#7c3aed',
    secondaryColor: '#22c55e',
    preview: 'Default Minecraft End theme'
  },
  {
    id: 'purple',
    name: 'Purple Void',
    primaryColor: '#8b5cf6',
    secondaryColor: '#a78bfa',
    preview: 'Deep purple aesthetic'
  },
  {
    id: 'green',
    name: 'Ender Green',
    primaryColor: '#22c55e',
    secondaryColor: '#16a34a',
    preview: 'Vibrant green theme'
  },
  {
    id: 'dark',
    name: 'Obsidian',
    primaryColor: '#1a1a1a',
    secondaryColor: '#2b2b2b',
    preview: 'Dark minimalist theme'
  }
]

const layouts = [
  {
    id: 'standard',
    name: 'Standard',
    description: 'Classic layout with sidebar and main content',
    preview: 'Sidebar + Main content'
  },
  {
    id: 'compact',
    name: 'Compact',
    description: 'Dense layout with more information visible',
    preview: 'Compact grid layout'
  },
  {
    id: 'wide',
    name: 'Wide',
    description: 'Full-width layout for maximum content',
    preview: 'Full-width single column'
  }
]

const handleThemeSelect = (themeId) => {
  selectedTheme.value = themeId
  emit('theme-change', themeId)
}

const handleLayoutSelect = (layoutId) => {
  selectedLayout.value = layoutId
  emit('layout-change', layoutId)
}

watch(() => props.currentTheme, (newVal) => {
  selectedTheme.value = newVal
})

watch(() => props.currentLayout, (newVal) => {
  selectedLayout.value = newVal
})
</script>

