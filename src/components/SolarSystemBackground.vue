<template>
  <div ref="containerRef" class="solar-system-container">
    <canvas ref="canvasRef" class="solar-system-canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import * as THREE from 'three'

const props = defineProps({
  isInteracting: {
    type: Boolean,
    default: false
  }
})

const containerRef = ref(null)
const canvasRef = ref(null)

let renderer, scene, camera, systemGroup, starfield, animationId
let sunMesh, sunGlowSprite, pointLight
let targetSpeedFactor = 1.0
let currentSpeedFactor = 1.0
let targetGlowScale = 1.0
let currentGlowScale = 1.0

let mouseX = 0, mouseY = 0
let targetMouseX = 0, targetMouseY = 0

// Planet definitions matching the beautiful "TwoDazzle" palette
const planetData = [
  {
    name: 'Mercury',
    radius: 0.45,
    distance: 6.5,
    color: '#ffd34d', // Gold
    speed: 0.024,
    angle: Math.random() * Math.PI * 2
  },
  {
    name: 'Venus',
    radius: 0.75,
    distance: 10,
    color: '#a99bc2', // Lavender-grey
    speed: 0.016,
    angle: Math.random() * Math.PI * 2
  },
  {
    name: 'Earth',
    radius: 0.85,
    distance: 14.5,
    color: '#38bdf8', // Cyan
    speed: 0.011,
    angle: Math.random() * Math.PI * 2,
    hasMoon: true,
    moonRadius: 0.18,
    moonDistance: 1.5,
    moonColor: '#cbd5e1',
    moonSpeed: 0.035,
    moonAngle: Math.random() * Math.PI * 2
  },
  {
    name: 'Mars',
    radius: 0.65,
    distance: 19,
    color: '#f43f5e', // Rose
    speed: 0.008,
    angle: Math.random() * Math.PI * 2
  },
  {
    name: 'Jupiter',
    radius: 1.5,
    distance: 25,
    color: '#c084fc', // Accent Violet
    speed: 0.004,
    angle: Math.random() * Math.PI * 2,
    hasRing: true,
    ringInner: 2.0,
    ringOuter: 3.2,
    ringColor: '#a78bfa'
  }
]

const planets = []

function makeSunGlowTexture() {
  const size = 128
  const canvas = document.createElement('canvas')
  canvas.width = size
  canvas.height = size
  const ctx = canvas.getContext('2d')
  const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2)
  grad.addColorStop(0, 'rgba(255, 211, 77, 1)')
  grad.addColorStop(0.2, 'rgba(255, 211, 77, 0.8)')
  grad.addColorStop(0.5, 'rgba(192, 132, 252, 0.35)')
  grad.addColorStop(1, 'rgba(10, 4, 23, 0)')
  ctx.fillStyle = grad
  ctx.fillRect(0, 0, size, size)
  return new THREE.CanvasTexture(canvas)
}

function handleMouseMove(e) {
  targetMouseX = (e.clientX / window.innerWidth) - 0.5
  targetMouseY = (e.clientY / window.innerHeight) - 0.5
}

function handleResize() {
  if (!containerRef.value || !renderer || !camera) return
  const width = containerRef.value.clientWidth
  const height = containerRef.value.clientHeight
  camera.aspect = width / height
  camera.updateProjectionMatrix()
  renderer.setSize(width, height)
}

onMounted(() => {
  const container = containerRef.value
  const canvas = canvasRef.value
  if (!container || !canvas) return

  const width = container.clientWidth
  const height = container.clientHeight

  // Create Scene
  scene = new THREE.Scene()

  // Create Camera - tilted angle looking down slightly
  camera = new THREE.PerspectiveCamera(60, width / height, 0.1, 1000)
  camera.position.set(0, 18, 33)
  camera.lookAt(0, 0, 0)

  // Create WebGL Renderer with Alpha transparent channel
  renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true })
  renderer.setSize(width, height)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))

  // Create systemGroup to hold all orbits, planets, and sun
  systemGroup = new THREE.Group()
  // Add a nice starting 3D tilt
  systemGroup.rotation.x = 0.35
  scene.add(systemGroup)

  // Sunlight (Point Light at center of Sun)
  pointLight = new THREE.PointLight('#ffd34d', 5, 80, 0.5)
  systemGroup.add(pointLight)

  // Soft purple Ambient Light for realistic fill shadows
  const ambientLight = new THREE.AmbientLight('#180c35', 1.2)
  scene.add(ambientLight)

  // Sun
  const sunGeometry = new THREE.SphereGeometry(2.2, 32, 32)
  const sunMaterial = new THREE.MeshBasicMaterial({
    color: '#ffd34d'
  })
  sunMesh = new THREE.Mesh(sunGeometry, sunMaterial)
  systemGroup.add(sunMesh)

  // Sun Glow Sprite (for aesthetic atmospheric flare)
  const glowTexture = makeSunGlowTexture()
  const glowMaterial = new THREE.SpriteMaterial({
    map: glowTexture,
    transparent: true,
    blending: THREE.AdditiveBlending,
    depthWrite: false
  })
  sunGlowSprite = new THREE.Sprite(glowMaterial)
  sunGlowSprite.scale.set(9, 9, 1)
  systemGroup.add(sunGlowSprite)

  // Construct Orbit Circles and Planet groups
  planetData.forEach(data => {
    // Group representing the individual planet's system (so it orbits around the sun)
    const planetGroup = new THREE.Group()

    // Planet Sphere Mesh
    const planetGeom = new THREE.SphereGeometry(data.radius, 32, 32)
    const planetMat = new THREE.MeshStandardMaterial({
      color: data.color,
      roughness: 0.6,
      metalness: 0.1
    })
    const planetMesh = new THREE.Mesh(planetGeom, planetMat)
    planetGroup.add(planetMesh)

    // Saturn/Jupiter-style Rings
    let ringMesh = null
    if (data.hasRing) {
      const ringGeom = new THREE.RingGeometry(data.ringInner, data.ringOuter, 64)
      ringGeom.rotateX(-Math.PI / 2)
      const ringMat = new THREE.MeshStandardMaterial({
        color: data.ringColor,
        side: THREE.DoubleSide,
        transparent: true,
        opacity: 0.4,
        roughness: 0.8
      })
      ringMesh = new THREE.Mesh(ringGeom, ringMat)
      planetGroup.add(ringMesh)
    }

    // Moon Mesh
    let moonMesh = null
    if (data.hasMoon) {
      const moonGeom = new THREE.SphereGeometry(data.moonRadius, 16, 16)
      const moonMat = new THREE.MeshStandardMaterial({
        color: data.moonColor,
        roughness: 0.8
      })
      moonMesh = new THREE.Mesh(moonGeom, moonMat)
      planetGroup.add(moonMesh)
    }

    systemGroup.add(planetGroup)

    // Orbit visual path line
    const points = []
    const segments = 128
    for (let i = 0; i <= segments; i++) {
      const theta = (i / segments) * Math.PI * 2
      points.push(new THREE.Vector3(Math.cos(theta) * data.distance, 0, Math.sin(theta) * data.distance))
    }
    const orbitGeom = new THREE.BufferGeometry().setFromPoints(points)
    const orbitMat = new THREE.LineBasicMaterial({
      color: '#7c3aed',
      transparent: true,
      opacity: 0.15
    })
    const orbitLine = new THREE.LineLoop(orbitGeom, orbitMat)
    systemGroup.add(orbitLine)

    planets.push({
      ...data,
      group: planetGroup,
      mesh: planetMesh,
      ring: ringMesh,
      moon: moonMesh
    })
  })

  // Deep Starfield Background
  const starCount = 180
  const starGeom = new THREE.BufferGeometry()
  const starPositions = []
  const starColors = []
  const starPalette = [
    new THREE.Color('#ffffff'),
    new THREE.Color('#c084fc'),
    new THREE.Color('#ffd34d')
  ]
  for (let i = 0; i < starCount; i++) {
    const x = (Math.random() - 0.5) * 120
    const y = (Math.random() - 0.5) * 80
    const z = (Math.random() - 0.5) * 100 - 25
    starPositions.push(x, y, z)
    const col = starPalette[Math.floor(Math.random() * starPalette.length)]
    starColors.push(col.r, col.g, col.b)
  }
  starGeom.setAttribute('position', new THREE.Float32BufferAttribute(starPositions, 3))
  starGeom.setAttribute('color', new THREE.Float32BufferAttribute(starColors, 3))

  // Soft spherical particle texture
  const canvasGlow = document.createElement('canvas')
  canvasGlow.width = 16
  canvasGlow.height = 16
  const ctxGlow = canvasGlow.getContext('2d')
  const grad = ctxGlow.createRadialGradient(8, 8, 0, 8, 8, 8)
  grad.addColorStop(0, 'rgba(255,255,255,1)')
  grad.addColorStop(1, 'rgba(255,255,255,0)')
  ctxGlow.fillStyle = grad
  ctxGlow.fillRect(0, 0, 16, 16)
  const starTexture = new THREE.CanvasTexture(canvasGlow)

  const starMat = new THREE.PointsMaterial({
    size: 0.75,
    map: starTexture,
    vertexColors: true,
    transparent: true,
    opacity: 0.55,
    blending: THREE.AdditiveBlending,
    depthWrite: false
  })
  starfield = new THREE.Points(starGeom, starMat)
  scene.add(starfield)

  let startTime = performance.now()

  // Main animation frame loop
  function animate() {
    animationId = requestAnimationFrame(animate)
    const t = (performance.now() - startTime) / 1000

    // Smooth speed factors transitions
    targetSpeedFactor = props.isInteracting ? 2.8 : 1.0
    currentSpeedFactor += (targetSpeedFactor - currentSpeedFactor) * 0.06

    // Smooth glow transition
    targetGlowScale = props.isInteracting ? 1.4 : 1.0
    currentGlowScale += (targetGlowScale - currentGlowScale) * 0.06

    // Rotate Sun
    if (sunMesh) {
      sunMesh.rotation.y += 0.004 * currentSpeedFactor
    }

    // Pulse Sun Glow
    if (sunGlowSprite) {
      const pulse = 1.0 + Math.sin(t * 2.5) * 0.05
      const size = 9.0 * pulse * currentGlowScale
      sunGlowSprite.scale.set(size, size, 1)
    }

    // Animate Planets
    planets.forEach(p => {
      p.angle += p.speed * currentSpeedFactor
      
      // Calculate planet location in orbit plane (XZ)
      p.group.position.set(
        Math.cos(p.angle) * p.distance,
        0,
        Math.sin(p.angle) * p.distance
      )

      // Self-rotation on axis
      p.mesh.rotation.y += 0.012 * currentSpeedFactor

      // Orbit Moon
      if (p.hasMoon && p.moon) {
        p.moonAngle += p.moonSpeed * currentSpeedFactor
        p.moon.position.set(
          Math.cos(p.moonAngle) * p.moonDistance,
          0,
          Math.sin(p.moonAngle) * p.moonDistance
        )
        p.moon.rotation.y += 0.02 * currentSpeedFactor
      }
    })

    // Update mouse interactive parallax camera tilting
    mouseX += (targetMouseX - mouseX) * 0.05
    mouseY += (targetMouseY - mouseY) * 0.05

    // Apply interactive yaw/pitch tilt of solar system group
    systemGroup.rotation.x = 0.35 + mouseY * 0.18
    systemGroup.rotation.y = mouseX * 0.22

    // Stars subtle drift
    starfield.rotation.y += 0.00015
    starfield.rotation.x = Math.sin(t * 0.01) * 0.015

    renderer.render(scene, camera)
  }

  animate()

  // Event Listeners
  window.addEventListener('mousemove', handleMouseMove)
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  cancelAnimationFrame(animationId)
  window.removeEventListener('mousemove', handleMouseMove)
  window.removeEventListener('resize', handleResize)

  if (renderer) {
    renderer.dispose()
  }

  if (sunMesh) {
    sunMesh.geometry.dispose()
    sunMesh.material.dispose()
  }
  if (sunGlowSprite) {
    sunGlowSprite.material.dispose()
  }

  planets.forEach(p => {
    p.mesh.geometry.dispose()
    p.mesh.material.dispose()
    if (p.ring) {
      p.ring.geometry.dispose()
      p.ring.material.dispose()
    }
    if (p.moon) {
      p.moon.geometry.dispose()
      p.moon.material.dispose()
    }
  })

  if (starfield) {
    starfield.geometry.dispose()
    starfield.material.dispose()
  }
})
</script>

<style scoped>
.solar-system-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
}

.solar-system-canvas {
  display: block;
  width: 100%;
  height: 100%;
  opacity: 0.45; /* elegant fading to keep UI/Form readable */
  transition: opacity 0.5s ease;
}
</style>
