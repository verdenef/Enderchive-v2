<template>
  <component :is="iconComponent" :size="size" :class="className" />
</template>

<script setup>
import * as icons from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  size: {
    type: [String, Number],
    default: 24
  },
  className: {
    type: String,
    default: ''
  }
})

const iconComponent = computed(() => {
  const iconName = props.name.replace('lucide:', '')
  const pascalName = iconName.split('-').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join('')
  return icons[pascalName] || icons.AlertCircle
})
</script>

