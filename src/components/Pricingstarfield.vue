<template>
    <canvas ref="canvasRef" class="pricing-starfield"></canvas>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import * as THREE from 'three'

    const canvasRef = ref(null)
    let renderer, scene, camera, group, glow, lightA, lightB, animationId

    let mouseX = 0, mouseY = 0
    let targetMouseX = 0, targetMouseY = 0

    const shards = []

    function makeGlowTexture() {
        const size = 512
        const canvas = document.createElement('canvas')
        canvas.width = size
        canvas.height = size
        const ctx = canvas.getContext('2d')
        const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2)
        grad.addColorStop(0, 'rgba(124,58,237,0.16)')
        grad.addColorStop(0.45, 'rgba(192,132,252,0.06)')
        grad.addColorStop(1, 'rgba(10,4,23,0)')
        ctx.fillStyle = grad
        ctx.fillRect(0, 0, size, size)
        return new THREE.CanvasTexture(canvas)
    }

    function makeShardGeometry() {
        const kind = Math.floor(Math.random() * 3)
        const r = 1
        if (kind === 0) return new THREE.IcosahedronGeometry(r, 0)
        if (kind === 1) return new THREE.OctahedronGeometry(r, 0)
        return new THREE.TetrahedronGeometry(r, 0)
    }

    function handleMouseMove(e) {
        targetMouseX = (e.clientX / window.innerWidth) - 0.5
        targetMouseY = (e.clientY / window.innerHeight) - 0.5
    }

    function handleResize() {
        const parent = canvasRef.value.parentElement
        camera.aspect = parent.clientWidth / parent.clientHeight
        camera.updateProjectionMatrix()
        renderer.setSize(parent.clientWidth, parent.clientHeight)
    }

    onMounted(() => {
        const canvas = canvasRef.value
        const parent = canvas.parentElement

        scene = new THREE.Scene()
        camera = new THREE.PerspectiveCamera(55, parent.clientWidth / parent.clientHeight, 0.1, 1000)
        camera.position.z = 34

        renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true })
        renderer.setSize(parent.clientWidth, parent.clientHeight)
        renderer.setPixelRatio(window.devicePixelRatio)

        scene.add(new THREE.AmbientLight('#3a2a5c', 1.1))

        lightA = new THREE.PointLight('#C084FC', 60, 120)
        lightA.position.set(-25, 15, 20)
        scene.add(lightA)

        lightB = new THREE.PointLight('#FFD34D', 45, 120)
        lightB.position.set(28, -12, 15)
        scene.add(lightB)

        const rimLight = new THREE.DirectionalLight('#FFFFFF', 0.6)
        rimLight.position.set(0, 0, 40)
        scene.add(rimLight)

        glow = new THREE.Sprite(new THREE.SpriteMaterial({
            map: makeGlowTexture(),
            transparent: true,
            depthWrite: false
        }))
        glow.scale.set(110, 110, 1)
        glow.position.set(0, 0, -50)
        scene.add(glow)

        const palette = [
            { color: '#7C3AED', emissive: '#3a1d78' },
            { color: '#C084FC', emissive: '#5a3a8c' },
            { color: '#FFD34D', emissive: '#7a5a12' },
            { color: '#F4F0FA', emissive: '#3a3450' }
        ]

        group = new THREE.Group()
        const isSmall = window.innerWidth < 768
        const shardCount = isSmall ? 9 : 16
        const topExtraCount = isSmall ? 4 : 7

        function createShard(forceTop) {
            const geometry = makeShardGeometry()
            const p = palette[Math.floor(Math.random() * palette.length)]
            const material = new THREE.MeshStandardMaterial({
                color: p.color,
                emissive: p.emissive,
                emissiveIntensity: 0.4,
                flatShading: true,
                metalness: 0.25,
                roughness: 0.15,
                transparent: true,
                opacity: 0.8
            })

            const mesh = new THREE.Mesh(geometry, material)

            const depth = -8 - Math.random() * 55
            const depthRatio = (depth + 8) / -55
            const spread = 16 + depthRatio * 30
            const y = forceTop
                ? 14 + Math.random() * (spread * 1.1)
                : (Math.random() - 0.5) * spread * 1.1
            mesh.position.set(
                (Math.random() - 0.5) * spread * 2,
                y,
                depth
            )

            const baseScale = (1 - depthRatio * 0.6) * (1.4 + Math.random() * 1.6)
            mesh.scale.set(
                baseScale * (0.6 + Math.random() * 0.5),
                baseScale * (1.1 + Math.random() * 0.9),
                baseScale * (0.6 + Math.random() * 0.5)
            )

            mesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI)

            mesh.userData = {
                spinX: (Math.random() - 0.5) * 0.06,
                spinY: (Math.random() - 0.5) * 0.08,
                bobSpeed: 0.15 + Math.random() * 0.2,
                bobAmount: 0.6 + Math.random() * 1,
                phase: Math.random() * Math.PI * 2,
                baseY: mesh.position.y,
                parallax: 0.4 + (1 - depthRatio) * 0.6
            }

            shards.push(mesh)
            group.add(mesh)
        }

        for (let i = 0; i < shardCount; i++) {
            createShard(false)
        }

        for (let i = 0; i < topExtraCount; i++) {
            createShard(true)
        }

        scene.add(group)

        let startTime = performance.now()

        function animate() {
            animationId = requestAnimationFrame(animate)
            const t = (performance.now() - startTime) / 1000

            mouseX += (targetMouseX - mouseX) * 0.03
            mouseY += (targetMouseY - mouseY) * 0.03

            shards.forEach(mesh => {
                const d = mesh.userData
                mesh.rotation.x += d.spinX * 0.02
                mesh.rotation.y += d.spinY * 0.02
                mesh.position.y = d.baseY + Math.sin(t * d.bobSpeed + d.phase) * d.bobAmount
                mesh.position.x += mouseX * d.parallax * 0.15
            })

            group.rotation.y = t * 0.01 + mouseX * 0.06
            group.rotation.x = mouseY * 0.04

            lightA.intensity = 55 + Math.sin(t * 0.4) * 10
            lightB.intensity = 40 + Math.cos(t * 0.35) * 10
            glow.material.opacity = 0.85 + Math.sin(t * 0.25) * 0.15

            renderer.render(scene, camera)
        }
        animate()

        window.addEventListener('mousemove', handleMouseMove)
        window.addEventListener('resize', handleResize)
    })

    onBeforeUnmount(() => {
        cancelAnimationFrame(animationId)
        window.removeEventListener('mousemove', handleMouseMove)
        window.removeEventListener('resize', handleResize)
        renderer.dispose()
        shards.forEach(mesh => {
            mesh.geometry.dispose()
            mesh.material.dispose()
        })
        glow.material.dispose()
    })
</script>

<style scoped>
    .pricing-starfield {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }
</style>
