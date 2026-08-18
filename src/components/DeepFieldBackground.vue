<template>
    <canvas ref="canvasRef" class="saturn-bg"></canvas>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import * as THREE from 'three'

    const canvasRef = ref(null)
    let renderer, scene, camera, planetGroup, ringMesh, rockGroup, starfield, animationId
    let targetZoom = 1
    let currentZoom = 1

    function makeStarGlowTexture() {
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

    function makeRingTexture() {
        const size = 512
        const canvas = document.createElement('canvas')
        canvas.width = size
        canvas.height = size
        const ctx = canvas.getContext('2d')
        const center = size / 2
        const grad = ctx.createRadialGradient(center, center, size * 0.24, center, center, size * 0.5)
        grad.addColorStop(0, 'rgba(230,200,150,0.85)')
        grad.addColorStop(0.35, 'rgba(192,132,252,0.55)')
        grad.addColorStop(0.7, 'rgba(124,58,237,0.35)')
        grad.addColorStop(1, 'rgba(124,58,237,0)')
        ctx.fillStyle = grad
        ctx.fillRect(0, 0, size, size)

        ctx.strokeStyle = 'rgba(10,4,23,0.12)'
        for (let r = size * 0.26; r < size * 0.5; r += 6 + Math.random() * 6) {
            ctx.lineWidth = 1 + Math.random() * 2
            ctx.beginPath()
            ctx.arc(center, center, r, 0, Math.PI * 2)
            ctx.stroke()
        }
        return new THREE.CanvasTexture(canvas)
    }

    onMounted(() => {
        const canvas = canvasRef.value
        const parent = canvas.parentElement

        scene = new THREE.Scene()
        camera = new THREE.PerspectiveCamera(50, parent.clientWidth / parent.clientHeight, 0.1, 1000)
        camera.position.set(0, 0, 26)

        renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true })
        renderer.setSize(parent.clientWidth, parent.clientHeight)
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))

        const keyLight = new THREE.DirectionalLight('#FFF3D6', 1.6)
        keyLight.position.set(-6, 4, 8)
        scene.add(keyLight)
        scene.add(new THREE.AmbientLight('#7C3AED', 0.5))

        planetGroup = new THREE.Group()
        planetGroup.position.set(-11, -1, 0)
        planetGroup.rotation.z = 0.3
        planetGroup.rotation.x = 0.1
        scene.add(planetGroup)

        const planetGeom = new THREE.SphereGeometry(4.5, 48, 48)
        const planetMat = new THREE.MeshStandardMaterial({
            color: '#E0B978',
            roughness: 0.7,
            metalness: 0.05
        })
        const planet = new THREE.Mesh(planetGeom, planetMat)
        planetGroup.add(planet)

        const ringTexture = makeRingTexture()
        const ringGeom = new THREE.RingGeometry(6.5, 15, 128)
        const pos = ringGeom.attributes.position
        const uv = ringGeom.attributes.uv
        const v3 = new THREE.Vector3()
        for (let i = 0; i < pos.count; i++) {
            v3.fromBufferAttribute(pos, i)
            const distance = v3.length()
            const normalized = (distance - 6.5) / (15 - 6.5)
            uv.setXY(i, normalized, normalized)
        }
        const ringMat = new THREE.MeshBasicMaterial({
            map: ringTexture,
            side: THREE.DoubleSide,
            transparent: true,
            opacity: 0.9
        })
        ringMesh = new THREE.Mesh(ringGeom, ringMat)
        ringMesh.rotation.x = Math.PI / 2 - 0.32
        planetGroup.add(ringMesh)

        rockGroup = new THREE.Group()
        planetGroup.add(rockGroup)
        const rockGeom = new THREE.IcosahedronGeometry(0.16, 0)
        const rockMat = new THREE.MeshStandardMaterial({ color: '#DCD2E1', roughness: 0.9 })
        const rocks = []
        const rockCount = 24
        for (let i = 0; i < rockCount; i++) {
            const rock = new THREE.Mesh(rockGeom, rockMat)
            const radius = 7 + Math.random() * 7
            const angle = Math.random() * Math.PI * 2
            rock.position.set(Math.cos(angle) * radius, 0, Math.sin(angle) * radius)
            rock.scale.setScalar(0.6 + Math.random() * 1.2)
            rockGroup.add(rock)
            rocks.push({ mesh: rock, angle, radius, speed: 0.05 + Math.random() * 0.08 })
        }
        rockGroup.rotation.x = Math.PI / 2 - 0.32

        const glowTexture = makeStarGlowTexture()
        const palette = [
            new THREE.Color('#FFFFFF'),
            new THREE.Color('#FFFFFF'),
            new THREE.Color('#C084FC'),
            new THREE.Color('#FFD34D')
        ]
        const starCount = window.innerWidth < 768 ? 200 : 380
        const starPositions = []
        const starColors = []
        for (let i = 0; i < starCount; i++) {
            const r = 30 + Math.random() * 90
            const theta = Math.random() * Math.PI * 2
            const phi = Math.acos((Math.random() * 2) - 1)
            starPositions.push(
                r * Math.sin(phi) * Math.cos(theta),
                r * Math.sin(phi) * Math.sin(theta) * 0.55,
                r * Math.cos(phi) * 0.7 - 20
            )
            const c = palette[Math.floor(Math.random() * palette.length)]
            starColors.push(c.r, c.g, c.b)
        }
        const starGeom = new THREE.BufferGeometry()
        starGeom.setAttribute('position', new THREE.Float32BufferAttribute(starPositions, 3))
        starGeom.setAttribute('color', new THREE.Float32BufferAttribute(starColors, 3))
        const starMat = new THREE.PointsMaterial({
            size: 0.6,
            map: glowTexture,
            vertexColors: true,
            transparent: true,
            opacity: 0.65,
            sizeAttenuation: true,
            blending: THREE.AdditiveBlending,
            depthWrite: false
        })
        starfield = new THREE.Points(starGeom, starMat)
        scene.add(starfield)

        let startTime = performance.now()

        function animate() {
            animationId = requestAnimationFrame(animate)
            const t = (performance.now() - startTime) / 1000

            currentZoom += (targetZoom - currentZoom) * 0.05
            camera.position.z = 26 - currentZoom * 7
            camera.position.x = -currentZoom * 8

            rocks.forEach(r => {
                r.angle += r.speed / 60
                r.mesh.position.set(Math.cos(r.angle) * r.radius, 0, Math.sin(r.angle) * r.radius)
            })

            starfield.rotation.y += 0.0003
            starMat.opacity = 0.55 + Math.sin(t * 0.5) * 0.1

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
        targetZoom = scrollProgress
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
        starfield.geometry.dispose()
        starfield.material.dispose()
        ringMesh.geometry.dispose()
        ringMesh.material.dispose()
    })
</script>

<style scoped>
    .saturn-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }
</style>