<template>
  <img
    :src="imgSrc"
    :alt="alt"
    :class="className"
    @error="handleError"
  />
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    required: true
  },
  fallback: {
    type: String,
    default: 'https://via.placeholder.com/300x400?text=No+Image'
  },
  className: {
    type: String,
    default: ''
  }
})

const imgSrc = ref(props.src)
const hasError = ref(false)

watch(() => props.src, (newSrc) => {
  imgSrc.value = newSrc
  hasError.value = false
})

const handleError = () => {
  if (!hasError.value && imgSrc.value !== props.fallback) {
    hasError.value = true
    imgSrc.value = props.fallback
  }
}
</script>

