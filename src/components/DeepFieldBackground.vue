<template>
    <canvas ref="canvasRef" class="deepfield-bg"></canvas>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'

const canvasRef = ref(null)
let renderer, scene, camera, particles, lines, nebula, galaxyGroup, comets, animationId
let targetZoom = 1
let currentZoom = 1

const constellationDefs = [
    { center: [-40, 24], scale: 3.4, points: [[0, 0], [1.2, -0.4], [2.2, -0.1], [3, 0.6], [2, 1.2], [0.6, 1.0], [0, 0]] },
    { center: [42, -22], scale: 3.0, points: [[0, 0], [1, -0.7], [2, -0.2]] },
    { center: [0, -30], scale: 2.6, points: [[0, 0], [1, 0.2], [1.9, 0.6], [2.5, 1.4], [3.2, 1.8]] },
    { center: [46, 20], scale: 2.8, points: [[0, 0], [0, -1.2], [-0.8, -1.4]] }
]

const galaxyDefs = [
    { center: [-56, -34, -30], scale: 18, colorA: '#C084FC', colorB: '#FFD34D', rot: 0.4 },
    { center: [50, 30, -35], scale: 15, colorA: '#7C3AED', colorB: '#C084FC', rot: -0.8 },
    { center: [40, -40, -25], scale: 11, colorA: '#FFD34D', colorB: '#C084FC', rot: 1.1 },
    { center: [-10, 42, -40], scale: 10, colorA: '#C084FC', colorB: '#7C3AED', rot: 2.0 },
    { center: [4, -12, -20], scale: 13, colorA: '#FFD34D', colorB: '#7C3AED', rot: -1.5 },
    { center: [-32, 6, -32], scale: 8, colorA: '#C084FC', colorB: '#FFD34D', rot: 0.9 }
]

function makeGlowTexture() {
    const size = 32
    const canvas = document.createElement('canvas')
    canvas.width = size
    canvas.height = size
    const ctx = canvas.getContext('2d')
    const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2)
    grad.addColorStop(0, 'rgba(255,255,255,1)')
    grad.addColorStop(0.3, 'rgba(255,255,255,0.7)')
    grad.addColorStop(1, 'rgba(255,255,255,0)')
    ctx.fillStyle = grad
    ctx.fillRect(0, 0, size, size)
    return new THREE.CanvasTexture(canvas)
}

function makeNebulaTexture() {
    const size = 512
    const canvas = document.createElement('canvas')
    canvas.width = size
    canvas.height = size
    const ctx = canvas.getContext('2d')
    const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2)
    grad.addColorStop(0, 'rgba(124,58,237,0.2)')
    grad.addColorStop(0.4, 'rgba(192,132,252,0.09)')
    grad.addColorStop(1, 'rgba(10,4,23,0)')
    ctx.fillStyle = grad
    ctx.fillRect(0, 0, size, size)
    return new THREE.CanvasTexture(canvas)
}

function makeCometTexture() {
    const size = 64
    const canvas = document.createElement('canvas')
    canvas.width = size
    canvas.height = size
    const ctx = canvas.getContext('2d')
    const grad = ctx.createLinearGradient(0, 0, size, 0)
    grad.addColorStop(0, 'rgba(255,255,255,0)')
    grad.addColorStop(0.7, 'rgba(255,255,255,0.6)')
    grad.addColorStop(1, 'rgba(255,255,255,1)')
    ctx.fillStyle = grad
    ctx.fillRect(0, 28, size, 8)
    return new THREE.CanvasTexture(canvas)
}

function buildGalaxy(def, glowTexture) {
    const group = new THREE.Group()
    group.position.set(...def.center)

    const colorA = new THREE.Color(def.colorA)
    const colorB = new THREE.Color(def.colorB)

    const positions = []
    const colors = []
    const sizes = []

    const arms = 2
    const perArm = 55
    for (let a = 0; a < arms; a++) {
        for (let i = 0; i < perArm; i++) {
            const frac = i / perArm
            const angle = frac * Math.PI * 3 + a * Math.PI + def.rot
            const r = frac * def.scale
            const spr = (Math.random() - 0.5) * def.scale * 0.15
            const x = Math.cos(angle) * r + spr
            const y = Math.sin(angle) * r * 0.35 + spr * 0.4
            const z = (Math.random() - 0.5) * def.scale * 0.2
            positions.push(x, y, z)
            const c = Math.random() < 0.5 ? colorA : colorB
            colors.push(c.r, c.g, c.b)
            sizes.push(0.4 + Math.random() * 0.7)
        }
    }

    const geometry = new THREE.BufferGeometry()
    geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3))
    geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3))
    geometry.setAttribute('size', new THREE.Float32BufferAttribute(sizes, 1))

    const material = new THREE.PointsMaterial({
        size: 0.5,
        map: glowTexture,
        vertexColors: true,
        transparent: true,
        opacity: 0.75,
        sizeAttenuation: true,
        blending: THREE.AdditiveBlending,
        depthWrite: false
    })

    const points = new THREE.Points(geometry, material)
    group.add(points)

    const coreMat = new THREE.SpriteMaterial({
        map: glowTexture,
        color: def.colorA,
        transparent: true,
        opacity: 0.6,
        blending: THREE.AdditiveBlending,
        depthWrite: false
    })
    const core = new THREE.Sprite(coreMat)
    core.scale.set(def.scale * 0.5, def.scale * 0.5, 1)
    group.add(core)

    return group
}

onMounted(() => {
    const canvas = canvasRef.value
    const parent = canvas.parentElement

    scene = new THREE.Scene()
    camera = new THREE.PerspectiveCamera(60, parent.clientWidth / parent.clientHeight, 0.1, 1000)
    camera.position.z = 30

    renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true })
    renderer.setSize(parent.clientWidth, parent.clientHeight)
    renderer.setPixelRatio(window.devicePixelRatio)

    const nebulaTexture = makeNebulaTexture()
    const nebulaPositions = [
        [-50, 28, -70], [46, -22, -80], [-32, -30, -60], [40, 24, -75], [0, 0, -110]
    ]
    nebula = new THREE.Group()
    nebulaPositions.forEach(pos => {
        const mat = new THREE.SpriteMaterial({ map: nebulaTexture, transparent: true, depthWrite: false })
        const sprite = new THREE.Sprite(mat)
        sprite.scale.set(75, 75, 1)
        sprite.position.set(...pos)
        nebula.add(sprite)
    })
    scene.add(nebula)

    const glowTexture = makeGlowTexture()
    const palette = [
        new THREE.Color('#FFFFFF'),
        new THREE.Color('#FFFFFF'),
        new THREE.Color('#C084FC'),
        new THREE.Color('#FFD34D')
    ]

    const positions = []
    const colors = []
    const sizes = []

    const starCount = window.innerWidth < 768 ? 220 : 480
    for (let i = 0; i < starCount; i++) {
        const r = 30 + Math.random() * 90
        const theta = Math.random() * Math.PI * 2
        const phi = Math.acos((Math.random() * 2) - 1)
        positions.push(
            r * Math.sin(phi) * Math.cos(theta),
            r * Math.sin(phi) * Math.sin(theta) * 0.55,
            r * Math.cos(phi) * 0.7 - 20
        )
        const c = palette[Math.floor(Math.random() * palette.length)]
        colors.push(c.r, c.g, c.b)
        sizes.push(Math.random() < 0.05 ? 2.0 + Math.random() * 1.2 : 0.3 + Math.random() * 0.6)
    }

    const linePositions = []
    const constColor = new THREE.Color('#C084FC')
    constellationDefs.forEach(def => {
        const pts = def.points.map(p => [
            def.center[0] + p[0] * def.scale,
            def.center[1] + p[1] * def.scale,
            (Math.random() - 0.5) * 6
        ])
        pts.forEach(p => {
            positions.push(p[0], p[1], p[2])
            colors.push(constColor.r, constColor.g, constColor.b)
            sizes.push(1.4)
        })
        for (let i = 0; i < pts.length - 1; i++) {
            linePositions.push(...pts[i], ...pts[i + 1])
        }
    })

    const geometry = new THREE.BufferGeometry()
    geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3))
    geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3))
    geometry.setAttribute('size', new THREE.Float32BufferAttribute(sizes, 1))

    const material = new THREE.PointsMaterial({
        size: 1.1,
        map: glowTexture,
        vertexColors: true,
        transparent: true,
        opacity: 0.85,
        sizeAttenuation: true,
        blending: THREE.AdditiveBlending,
        depthWrite: false
    })

    particles = new THREE.Points(geometry, material)
    scene.add(particles)

    const lineGeometry = new THREE.BufferGeometry()
    lineGeometry.setAttribute('position', new THREE.Float32BufferAttribute(linePositions, 3))
    const lineMaterial = new THREE.LineBasicMaterial({
        color: '#C084FC',
        transparent: true,
        opacity: 0.15
    })
    lines = new THREE.LineSegments(lineGeometry, lineMaterial)
    scene.add(lines)

    galaxyGroup = new THREE.Group()
    galaxyDefs.forEach(def => {
        galaxyGroup.add(buildGalaxy(def, glowTexture))
    })
    scene.add(galaxyGroup)

    const cometTexture = makeCometTexture()
    comets = []
    const cometCount = 4
    for (let i = 0; i < cometCount; i++) {
        const mat = new THREE.SpriteMaterial({
            map: cometTexture,
            transparent: true,
            opacity: 0,
            blending: THREE.AdditiveBlending,
            depthWrite: false
        })
        const sprite = new THREE.Sprite(mat)
        sprite.scale.set(14, 2, 1)
        scene.add(sprite)
        comets.push({ sprite, active: false, delay: Math.random() * 10, progress: 0 })
    }

    function resetComet(c) {
        const startX = -70 - Math.random() * 20
        const startY = 20 + Math.random() * 30
        const endY = startY - (40 + Math.random() * 30)
        c.startX = startX
        c.startY = startY
        c.endX = startX + 100 + Math.random() * 40
        c.endY = endY
        c.progress = 0
        c.active = true
        c.speed = 0.4 + Math.random() * 0.3
        const angle = Math.atan2(c.endY - c.startY, c.endX - c.startX)
        c.sprite.material.rotation = angle
    }

    let startTime = performance.now()

    function animate() {
        animationId = requestAnimationFrame(animate)
        const t = (performance.now() - startTime) / 1000

        particles.rotation.y += 0.0004
        particles.rotation.x = Math.sin(t * 0.05) * 0.03
        lines.rotation.y = particles.rotation.y
        lines.rotation.x = particles.rotation.x
        galaxyGroup.rotation.y = particles.rotation.y
        galaxyGroup.rotation.x = particles.rotation.x
        nebula.rotation.z = t * 0.002

        currentZoom += (targetZoom - currentZoom) * 0.06
        particles.scale.set(currentZoom, currentZoom, currentZoom)
        lines.scale.set(currentZoom, currentZoom, currentZoom)
        galaxyGroup.scale.set(currentZoom, currentZoom, currentZoom)

        material.opacity = 0.75 + Math.sin(t * 0.6) * 0.1

        comets.forEach(c => {
            if (!c.active) {
                c.delay -= 1 / 60
                if (c.delay <= 0) resetComet(c)
                return
            }
            c.progress += c.speed / 60
            if (c.progress >= 1) {
                c.active = false
                c.delay = 4 + Math.random() * 8
                c.sprite.material.opacity = 0
                return
            }
            const x = c.startX + (c.endX - c.startX) * c.progress
            const y = c.startY + (c.endY - c.startY) * c.progress
            c.sprite.position.set(x, y, -10)
            c.sprite.material.opacity = Math.sin(c.progress * Math.PI) * 0.8
        })

        renderer.render(scene, camera)
    }
    animate()

    window.addEventListener('resize', handleResize)
    window.addEventListener('scroll', handleScroll)
    handleScroll()
})

function handleScroll() {
    const maxScroll = document.documentElement.scrollHeight - window.innerHeight
    const scrollProgress = maxScroll > 0 ? window.scrollY / maxScroll : 0
    targetZoom = 1 + scrollProgress * 1.2
}

function handleResize() {
    const parent = canvasRef.value.parentElement
    camera.aspect = parent.clientWidth / parent.clientHeight
    camera.updateProjectionMatrix()
    renderer.setSize(parent.clientWidth, parent.clientHeight)
}

onBeforeUnmount(() => {
    cancelAnimationFrame(animationId)
    window.removeEventListener('resize', handleResize)
    window.removeEventListener('scroll', handleScroll)
    renderer.dispose()
    particles.geometry.dispose()
    particles.material.dispose()
    lines.geometry.dispose()
    lines.material.dispose()
    comets.forEach(c => c.sprite.material.dispose())
    galaxyGroup.children.forEach(g => {
        g.children.forEach(child => {
            if (child.geometry) child.geometry.dispose()
            if (child.material) child.material.dispose()
        })
    })
})
</script>

<style scoped>
    .deepfield-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }
</style>