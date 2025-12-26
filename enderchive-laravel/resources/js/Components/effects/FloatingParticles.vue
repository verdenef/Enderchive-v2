<template>
  <canvas
    ref="canvasRef"
    class="fixed top-0 left-0 w-full h-full pointer-events-none z-0"
  />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const canvasRef = ref(null)
let animationId = null

onMounted(() => {
  const canvas = canvasRef.value
  if (!canvas) return

  const ctx = canvas.getContext('2d')
  if (!ctx) return

  canvas.width = window.innerWidth
  canvas.height = window.innerHeight

  const enchantedChars = [
    'ᚠ', 'ᚢ', 'ᚦ', 'ᚨ', 'ᚱ', 'ᚲ', 'ᚷ', 'ᚹ', 'ᚺ', 'ᚾ', 'ᛁ', 'ᛃ', 'ᛇ', 'ᛈ', 'ᛉ', 'ᛊ', 'ᛏ', 'ᛒ', 'ᛖ', 'ᛗ', 'ᛚ', 'ᛜ', 'ᛞ', 'ᛟ',
    '⌰', '⍜', '⎎', '⍀', '⟒', '⌇', '⏁', '⊑', '⟟', '⌿', '⌰', '⏃', '⋏', '⟒', '⏁',
    '◎', '○', '◉', '◇', '◆', '△', '▽', '◈'
  ]

  const particles = []

  for (let i = 0; i < 40; i++) {
    particles.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      char: enchantedChars[Math.floor(Math.random() * enchantedChars.length)],
      size: Math.random() * 8 + 10,
      speedY: Math.random() * 0.8 + 0.4,
      speedX: (Math.random() - 0.5) * 0.3,
      opacity: 0,
      maxOpacity: Math.random() * 0.4 + 0.3,
      phase: Math.random() * Math.PI * 2,
      lifetime: Math.random() * 200 + 150,
      age: Math.random() * 200
    })
  }

  const animate = () => {
    if (!ctx || !canvas) return
    ctx.clearRect(0, 0, canvas.width, canvas.height)

    particles.forEach((particle) => {
      particle.age++

      if (particle.age < 50) {
        particle.opacity = (particle.age / 50) * particle.maxOpacity
      } else if (particle.age > particle.lifetime - 50) {
        particle.opacity = ((particle.lifetime - particle.age) / 50) * particle.maxOpacity
      } else {
        particle.opacity = particle.maxOpacity
      }

      particle.phase += 0.02
      const wavyX = Math.sin(particle.phase) * 15

      ctx.save()
      ctx.font = `${particle.size}px Arial`
      ctx.textAlign = 'center'
      ctx.textBaseline = 'middle'

      ctx.shadowBlur = 15
      ctx.shadowColor = `rgba(139, 92, 246, ${particle.opacity * 0.8})`
      ctx.fillStyle = `rgba(139, 92, 246, ${particle.opacity})`
      ctx.fillText(particle.char, particle.x + wavyX, particle.y)

      ctx.shadowBlur = 8
      ctx.shadowColor = `rgba(196, 181, 253, ${particle.opacity})`
      ctx.fillStyle = `rgba(196, 181, 253, ${particle.opacity})`
      ctx.fillText(particle.char, particle.x + wavyX, particle.y)

      ctx.restore()

      particle.y -= particle.speedY
      particle.x += particle.speedX

      if (particle.y < -20 || particle.age > particle.lifetime) {
        particle.y = canvas.height + 20
        particle.x = Math.random() * canvas.width
        particle.char = enchantedChars[Math.floor(Math.random() * enchantedChars.length)]
        particle.age = 0
        particle.lifetime = Math.random() * 200 + 150
        particle.speedY = Math.random() * 0.8 + 0.4
        particle.speedX = (Math.random() - 0.5) * 0.3
        particle.maxOpacity = Math.random() * 0.4 + 0.3
      }

      if (particle.x < -50) particle.x = canvas.width + 50
      if (particle.x > canvas.width + 50) particle.x = -50
    })

    animationId = requestAnimationFrame(animate)
  }

  animate()

  const handleResize = () => {
    if (!canvas) return
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight
  }

  window.addEventListener('resize', handleResize)

  onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
    if (animationId !== null) {
      cancelAnimationFrame(animationId)
    }
  })
})
</script>

