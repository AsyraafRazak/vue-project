<script setup>
import { onMounted, onUnmounted } from 'vue'

let sparkleId = 0
const colors = ['var(--td-star-gold)', 'var(--td-star-purple)', 'var(--td-star-white)']
let lastSpawn = 0

const spawnSparkle = (x, y) => {
  const el = document.createElement('span')
  el.className = 'sparkle-particle'
  el.textContent = '✦'
  el.style.left = `${x}px`
  el.style.top = `${y}px`
  el.style.color = colors[Math.floor(Math.random() * colors.length)]
  el.style.fontSize = `${8 + Math.random() * 8}px`

  const driftX = (Math.random() - 0.5) * 40
  el.style.setProperty('--drift-x', `${driftX}px`)

  document.body.appendChild(el)

  el.addEventListener('animationend', () => {
    el.remove()
  })
}

const handleMouseMove = (e) => {
  const now = Date.now()
  // throttle so it doesn't spawn a sparkle on every single pixel of movement
  if (now - lastSpawn > 40) {
    spawnSparkle(e.clientX, e.clientY)
    lastSpawn = now
  }
}

onMounted(() => {
  window.addEventListener('mousemove', handleMouseMove)
})

onUnmounted(() => {
  window.removeEventListener('mousemove', handleMouseMove)
})
</script>

<template>
    <!-- No template output needed, particles are appended directly to body -->
</template>