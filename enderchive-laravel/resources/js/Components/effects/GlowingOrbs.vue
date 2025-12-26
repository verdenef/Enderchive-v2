<template>
  <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
    <div
      class="absolute w-96 h-96 rounded-full blur-3xl opacity-20"
      style="
        background: radial-gradient(circle, rgba(124, 58, 237, 0.4) 0%, transparent 70%);
        top: -10%;
        left: -10%;
        animation: pulse 8s ease-in-out infinite;
      "
    />
    <div
      class="absolute w-96 h-96 rounded-full blur-3xl opacity-20"
      style="
        background: radial-gradient(circle, rgba(34, 197, 94, 0.4) 0%, transparent 70%);
        bottom: -10%;
        right: -10%;
        animation: pulse 10s ease-in-out infinite;
        animation-delay: 2s;
      "
    />
  </div>
</template>

<script setup>
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 0.2;
  }
  50% {
    transform: scale(1.2);
    opacity: 0.3;
  }
}
</style>
