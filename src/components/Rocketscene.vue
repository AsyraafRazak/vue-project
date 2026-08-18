<template>
    <canvas ref="canvasRef" class="rocket-canvas"></canvas>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import * as THREE from 'three'

    const canvasRef = ref(null)
    let renderer, scene, camera, rocketGroup, flameMesh, flameCore, smokeGroup, animationId

    function makeSmokeTexture() {
        const size = 32
        const canvas = document.createElement('canvas')
        canvas.width = size
        canvas.height = size
        const ctx = canvas.getContext('2d')
        const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2)
        grad.addColorStop(0, 'rgba(230,220,240,0.9)')
        grad.addColorStop(0.5, 'rgba(200,190,220,0.4)')
        grad.addColorStop(1, 'rgba(200,190,220,0)')
        ctx.fillStyle = grad
        ctx.fillRect(0, 0, size, size)
        return new THREE.CanvasTexture(canvas)
    }

    onMounted(() => {
        const canvas = canvasRef.value
        const size = 34

        scene = new THREE.Scene()
        camera = new THREE.PerspectiveCamera(35, 1, 0.1, 100)
        camera.position.set(0, 0, 5)

        renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true })
        renderer.setSize(size, size)
        renderer.setPixelRatio(window.devicePixelRatio)

        rocketGroup = new THREE.Group()
        scene.add(rocketGroup)

        // Nose cone (top, gold)
        const noseGeom = new THREE.ConeGeometry(0.5, 1.0, 16)
        const noseMat = new THREE.MeshStandardMaterial({
            color: '#FFD34D',
            roughness: 0.4,
            emissive: '#FFD34D',
            emissiveIntensity: 0.2
        })
        const nose = new THREE.Mesh(noseGeom, noseMat)
        nose.position.y = 0.9
        rocketGroup.add(nose)

        // Body cylinder (bottom, purple)
        const bodyGeom = new THREE.CylinderGeometry(0.5, 0.5, 1.4, 16)
        const bodyMat = new THREE.MeshStandardMaterial({
            color: '#C084FC',
            roughness: 0.4,
            emissive: '#7C3AED',
            emissiveIntensity: 0.2
        })
        const body = new THREE.Mesh(bodyGeom, bodyMat)
        body.position.y = 0
        rocketGroup.add(body)

        // Bigger fins sticking out sideways
        const finShape = new THREE.Shape()
        finShape.moveTo(0, 0.1)
        finShape.lineTo(0.75, -0.4)
        finShape.lineTo(0.55, -1.0)
        finShape.lineTo(0, -0.6)
        finShape.closePath()
        const finExtrude = new THREE.ExtrudeGeometry(finShape, { depth: 0.1, bevelEnabled: false })
        const finMat = new THREE.MeshStandardMaterial({ color: '#7C3AED', roughness: 0.5 })

        const finLeft = new THREE.Mesh(finExtrude, finMat)
        finLeft.position.set(-0.48, -0.15, -0.05)
        rocketGroup.add(finLeft)

        const finRight = new THREE.Mesh(finExtrude.clone(), finMat)
        finRight.scale.x = -1
        finRight.position.set(0.48, -0.15, -0.05)
        rocketGroup.add(finRight)

        // Window
        const windowGeom = new THREE.CircleGeometry(0.18, 16)
        const windowMat = new THREE.MeshBasicMaterial({ color: '#0A0417' })
        const windowMesh = new THREE.Mesh(windowGeom, windowMat)
        windowMesh.position.set(0, 0.2, 0.5)
        rocketGroup.add(windowMesh)

        // Engine flame at the base
        const flameGeom = new THREE.ConeGeometry(0.32, 0.9, 12)
        const flameMat = new THREE.MeshBasicMaterial({
            color: '#FFD34D',
            transparent: true,
            opacity: 0.9
        })
        flameMesh = new THREE.Mesh(flameGeom, flameMat)
        flameMesh.position.y = -1.25
        flameMesh.rotation.x = Math.PI
        rocketGroup.add(flameMesh)

        const flameCoreGeom = new THREE.ConeGeometry(0.16, 0.55, 12)
        const flameCoreMat = new THREE.MeshBasicMaterial({
            color: '#ffffff',
            transparent: true,
            opacity: 0.85
        })
        flameCore = new THREE.Mesh(flameCoreGeom, flameCoreMat)
        flameCore.position.y = -1.15
        flameCore.rotation.x = Math.PI
        rocketGroup.add(flameCore)

        // Smoke puffs - small pool of sprites recycled continuously
        const smokeTexture = makeSmokeTexture()
        smokeGroup = new THREE.Group()
        rocketGroup.add(smokeGroup)

        const smokeCount = 6
        const smokeParticles = []
        for (let i = 0; i < smokeCount; i++) {
            const mat = new THREE.SpriteMaterial({
                map: smokeTexture,
                transparent: true,
                opacity: 0,
                depthWrite: false
            })
            const sprite = new THREE.Sprite(mat)
            sprite.scale.set(0.01, 0.01, 1)
            smokeGroup.add(sprite)
            smokeParticles.push({
                sprite,
                life: Math.random(),
                speed: 0.3 + Math.random() * 0.2,
                drift: (Math.random() - 0.5) * 0.4
            })
        }

        // Tilt for a "taking off" pose
        rocketGroup.rotation.z = -0.35

        // Lighting
        const light1 = new THREE.PointLight('#FFD34D', 3, 20)
        light1.position.set(2, 2, 4)
        scene.add(light1)

        const light2 = new THREE.PointLight('#C084FC', 2, 20)
        light2.position.set(-2, -1, 3)
        scene.add(light2)

        scene.add(new THREE.AmbientLight('#ffffff', 0.6))

        let startTime = performance.now()
        let lastTime = startTime

        function animate() {
            animationId = requestAnimationFrame(animate)
            const now = performance.now()
            const t = (now - startTime) / 1000
            const dt = Math.min((now - lastTime) / 1000, 0.05)
            lastTime = now

            rocketGroup.position.y = Math.sin(t * 1.5) * 0.12
            rocketGroup.rotation.y += 0.012

            // Flicker the flame
            const flicker = 1 + Math.sin(t * 18) * 0.15
            flameMesh.scale.set(flicker, flicker * (1 + Math.sin(t * 22) * 0.2), flicker)
            flameMesh.material.opacity = 0.7 + Math.sin(t * 20) * 0.2

            const coreFlicker = 1 + Math.sin(t * 24) * 0.2
            flameCore.scale.set(coreFlicker, coreFlicker, coreFlicker)

            // Update smoke puffs - rise, drift, fade, then recycle
            smokeParticles.forEach(p => {
                p.life += dt * p.speed
                if (p.life > 1) {
                    p.life = 0
                }
                const y = -1.3 - p.life * 1.3
                const x = p.drift * p.life
                p.sprite.position.set(x, y, 0)
                const scale = 0.15 + p.life * 0.5
                p.sprite.scale.set(scale, scale, 1)
                p.sprite.material.opacity = Math.sin(p.life * Math.PI) * 0.5
            })

            renderer.render(scene, camera)
        }
        animate()
    })

    onBeforeUnmount(() => {
        cancelAnimationFrame(animationId)
        if (renderer) renderer.dispose()
    })
</script>

<style scoped>
    .rocket-canvas {
        width: 34px;
        height: 34px;
        display: block;
    }
</style>