<template>
    <canvas ref="canvasRef" class="three-bg"></canvas>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import * as THREE from 'three'

    const canvasRef = ref(null)
    let renderer, scene, camera, particles, lines, nebula, animationId
    let targetZoom = 1
    let currentZoom = 1

    const constellationDefs = [
        { center: [-14, -9], scale: 2.2, points: [[0, 0], [1, -0.3], [2, -0.2], [2.6, 0.4], [1.8, 0.9], [0.6, 0.8], [0, 0]] },
        { center: [13, -11], scale: 2.0, points: [[0, 0], [0.8, -0.6], [1.6, -0.3]] },
        { center: [-1, -14], scale: 1.7, points: [[0, 0], [0.9, 0.15], [1.7, 0.5], [2.3, 1.2], [2.9, 1.6], [3.3, 1.2]] },
        { center: [1, 12], scale: 2.4, points: [[0, 0], [0, -1.1], [0.7, -1.3]] },
        { center: [8, 12], scale: 2.4, points: [[0, 0], [0, -1.1], [-0.7, -1.3]] },
        { center: [-12, 11], scale: 2.0, points: [[0, 0], [0.7, -0.4], [1.5, -0.2], [1.9, 0.5], [1.2, 0.9], [0.4, 0.7], [0, 0]] },
        { center: [13, 3], scale: 1.8, points: [[0, 0], [0.9, -0.5], [1.5, 0.1], [0.9, 0.6], [0, 0]] }
    ]

    function makeGlowTexture() {
        const size = 128
        const canvas = document.createElement('canvas')
        canvas.width = size
        canvas.height = size
        const ctx = canvas.getContext('2d')
        const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2)
        grad.addColorStop(0, 'rgba(255,255,255,1)')
        grad.addColorStop(0.2, 'rgba(255,255,255,0.9)')
        grad.addColorStop(0.5, 'rgba(255,255,255,0.2)')
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
        grad.addColorStop(0, 'rgba(124,58,237,0.18)')
        grad.addColorStop(0.4, 'rgba(192,132,252,0.08)')
        grad.addColorStop(1, 'rgba(10,4,23,0)')
        ctx.fillStyle = grad
        ctx.fillRect(0, 0, size, size)
        return new THREE.CanvasTexture(canvas)
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
            [-45, 25, -60], [42, -20, -70], [-30, -28, -55], [38, 22, -65]
        ]
        nebula = new THREE.Group()
        nebulaPositions.forEach(pos => {
            const mat = new THREE.SpriteMaterial({ map: nebulaTexture, transparent: true, depthWrite: false })
            const sprite = new THREE.Sprite(mat)
            sprite.scale.set(60, 60, 1)
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
        const twinklePhases = []

        const starCount = window.innerWidth < 768 ? 300 : 700
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
            twinklePhases.push(Math.random() * Math.PI * 2)
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
                twinklePhases.push(Math.random() * Math.PI * 2)
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

        let startTime = performance.now()

        function animate() {
            animationId = requestAnimationFrame(animate)
            const t = (performance.now() - startTime) / 1000

            particles.rotation.y += 0.0003
            particles.rotation.x = Math.sin(t * 0.04) * 0.02
            lines.rotation.y = particles.rotation.y
            lines.rotation.x = particles.rotation.x
            nebula.rotation.z = t * 0.003

            currentZoom += (targetZoom - currentZoom) * 0.06
            particles.scale.set(currentZoom, currentZoom, currentZoom)
            lines.scale.set(currentZoom, currentZoom, currentZoom)

            material.opacity = 0.75 + Math.sin(t * 0.6) * 0.1

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
    })
</script>

<style scoped>
    .three-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }
</style>