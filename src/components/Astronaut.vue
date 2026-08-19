<template>
    <div ref="wrapRef" class="astro-wrap" :style="{ height: height }">
        <div v-if="hint" class="astro-hint">drag to spin · hover to say hi</div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import * as THREE from 'three'

    const props = defineProps({
        height: { type: String, default: '420px' },
        hint: { type: Boolean, default: true },
        autoRotate: { type: Boolean, default: true }
    })

    const wrapRef = ref(null)

    let renderer, scene, camera, astro, animId
    let resizeObserver

    onMounted(() => {
        const wrap = wrapRef.value

        scene = new THREE.Scene()
        camera = new THREE.PerspectiveCamera(42, 1, 0.1, 100)
        camera.position.set(0, 0.1, 9.6)

        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
        renderer.domElement.style.display = 'block'
        renderer.domElement.style.width = '100%'
        renderer.domElement.style.height = '100%'
        wrap.appendChild(renderer.domElement)

        const resize = () => {
            const w = wrap.clientWidth
            const h = wrap.clientHeight
            camera.aspect = w / h
            camera.updateProjectionMatrix()
            renderer.setSize(w, h, false)
        }
        resizeObserver = new ResizeObserver(resize)
        resizeObserver.observe(wrap)
        resize()

        // lights — tuned toward the brand palette (purple key, gold rim)
        const key = new THREE.DirectionalLight(0xffffff, 1.0)
        key.position.set(3, 4, 5)
        scene.add(key)
        const fillLight = new THREE.DirectionalLight(0x7c3aed, 0.5)
        fillLight.position.set(-4, -2, 3)
        scene.add(fillLight)
        const rimLight = new THREE.DirectionalLight(0xffd34d, 0.6)
        rimLight.position.set(-2, 3, -4)
        scene.add(rimLight)
        scene.add(new THREE.AmbientLight(0xffffff, 0.5))

        // brand palette
        const cream = 0xf1efe8
        const purple = 0x7c3aed
        const lilac = 0xc084fc
        const dark = 0x0a0417
        const gold = 0xffd34d

        const bodyMat = new THREE.MeshStandardMaterial({ color: cream, roughness: 0.55, metalness: 0.05 })
        const suitMat = new THREE.MeshStandardMaterial({ color: purple, roughness: 0.5, metalness: 0.1 })
        const visorMat = new THREE.MeshBasicMaterial({ color: dark })
        const accentMat = new THREE.MeshStandardMaterial({
            color: gold,
            roughness: 0.4,
            metalness: 0.2,
            emissive: gold,
            emissiveIntensity: 0.4
        })
        const cheekMat = new THREE.MeshBasicMaterial({ color: lilac })

        astro = new THREE.Group()
        scene.add(astro)

        // chibi proportions: smaller body, bigger head
        const body = new THREE.Mesh(new THREE.CapsuleGeometry(0.72, 0.5, 8, 16), bodyMat)
        body.position.y = -0.05
        astro.add(body)

        const chestStripe = new THREE.Mesh(new THREE.BoxGeometry(0.46, 0.22, 0.05), suitMat)
        chestStripe.position.set(0, 0.05, 0.72)
        astro.add(chestStripe)

        const backpack = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.75, 0.3), suitMat)
        backpack.position.set(0, -0.05, -0.65)
        astro.add(backpack)

        // jetpack flame — three stacked cones (outer glow, mid, hot core), pointing down
        const flame = new THREE.Group()
        flame.position.set(0, -0.44, -0.65)
        astro.add(flame)

        const flameOuterMat = new THREE.MeshBasicMaterial({
            color: purple, transparent: true, opacity: 0.35, blending: THREE.AdditiveBlending, depthWrite: false
        })
        const flameMidMat = new THREE.MeshBasicMaterial({
            color: lilac, transparent: true, opacity: 0.55, blending: THREE.AdditiveBlending, depthWrite: false
        })
        const flameCoreMat = new THREE.MeshBasicMaterial({
            color: gold, transparent: true, opacity: 0.85, blending: THREE.AdditiveBlending, depthWrite: false
        })

        const flameOuter = new THREE.Mesh(new THREE.ConeGeometry(0.62, 1.6, 16), flameOuterMat)
        flameOuter.rotation.x = Math.PI
        flameOuter.position.y = -0.75
        flame.add(flameOuter)

        const flameMid = new THREE.Mesh(new THREE.ConeGeometry(0.4, 1.2, 16), flameMidMat)
        flameMid.rotation.x = Math.PI
        flameMid.position.y = -0.56
        flame.add(flameMid)

        const flameCore = new THREE.Mesh(new THREE.ConeGeometry(0.2, 0.78, 16), flameCoreMat)
        flameCore.rotation.x = Math.PI
        flameCore.position.y = -0.36
        flame.add(flameCore)

        const flameLight = new THREE.PointLight(gold, 2.4, 4.5)
        flameLight.position.set(0, -0.3, -0.65)
        astro.add(flameLight)

        // floating rocks scattered all around him — same spherical-scatter approach as
        // the starfield in ThreeBackground.vue, just at a smaller radius and with meshes instead of points.
        // Parented to `astro` (not `scene`) so dragging/rotating him also drags the rocks along with him.
        const rockGroup = new THREE.Group()
        astro.add(rockGroup)
        const rockGeom = new THREE.IcosahedronGeometry(0.1, 0)
        const rockMat = new THREE.MeshStandardMaterial({ color: 0xdcd2e1, roughness: 0.9 })
        const rocks = []
        const rockCount = 28
        for (let i = 0; i < rockCount; i++) {
            const rock = new THREE.Mesh(rockGeom, rockMat)
            const r = 1.4 + Math.random() * 6.5
            const theta = Math.random() * Math.PI * 2
            const phi = Math.acos(Math.random() * 2 - 1)
            const basePos = new THREE.Vector3(
                r * Math.sin(phi) * Math.cos(theta),
                r * Math.sin(phi) * Math.sin(theta) * 0.7,
                r * Math.cos(phi) * 0.7
            )
            rock.position.copy(basePos)
            rock.scale.setScalar(0.4 + Math.random() * 1.4)
            rock.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI)
            rockGroup.add(rock)
            rocks.push({
                mesh: rock,
                basePos,
                spinX: 0.1 + Math.random() * 0.4,
                spinY: 0.1 + Math.random() * 0.4,
                driftPhase: Math.random() * Math.PI * 2,
                driftSpeed: 0.3 + Math.random() * 0.4
            })
        }

        const head = new THREE.Mesh(new THREE.SphereGeometry(0.82, 28, 28), bodyMat)
        head.position.set(0, 1.05, 0)
        astro.add(head)

        const visor = new THREE.Mesh(
            new THREE.SphereGeometry(0.66, 28, 28, 0, Math.PI * 2, 0, Math.PI * 0.65),
            visorMat
        )
        visor.position.set(0, 1.08, 0.2)
        visor.rotation.x = -0.15
        astro.add(visor)

        const cheekGeo = new THREE.CircleGeometry(0.07, 12)
            ;[-1, 1].forEach((s) => {
                const cheek = new THREE.Mesh(cheekGeo, cheekMat)
                cheek.position.set(0.32 * s, 0.95, 0.6)
                cheek.rotation.y = 0.5 * s
                astro.add(cheek)
            })

        const antenna = new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.3, 8), suitMat)
        antenna.position.set(0.35, 1.75, 0)
        antenna.rotation.z = -0.3
        astro.add(antenna)
        const antennaTip = new THREE.Mesh(new THREE.SphereGeometry(0.07, 12, 12), accentMat)
        antennaTip.position.set(0.46, 1.9, 0)
        astro.add(antennaTip)

        function makeArm() {
            const pivot = new THREE.Group()
            const upper = new THREE.Mesh(new THREE.CapsuleGeometry(0.16, 0.42, 6, 12), bodyMat)
            upper.position.y = -0.27
            pivot.add(upper)
            const glove = new THREE.Mesh(new THREE.SphereGeometry(0.19, 16, 16), suitMat)
            glove.position.y = -0.56
            pivot.add(glove)
            return pivot
        }

        // idle stance: arms held slightly out from the body, not stiff and straight down
        const leftArmPivot = makeArm()
        leftArmPivot.position.set(-0.82, 0.4, 0)
        leftArmPivot.rotation.z = 0.35
        astro.add(leftArmPivot)

        const rightArmPivot = makeArm()
        rightArmPivot.position.set(0.82, 0.4, 0)
        rightArmPivot.rotation.z = -0.35
        astro.add(rightArmPivot)

        function makeLeg() {
            const pivot = new THREE.Group()
            const upper = new THREE.Mesh(new THREE.CapsuleGeometry(0.19, 0.38, 6, 12), bodyMat)
            upper.position.y = -0.26
            pivot.add(upper)
            const boot = new THREE.Mesh(new THREE.BoxGeometry(0.26, 0.18, 0.36), suitMat)
            boot.position.y = -0.54
            boot.position.z = 0.06
            pivot.add(boot)
            return pivot
        }
        const leftLeg = makeLeg()
        leftLeg.position.set(-0.3, -0.55, 0)
        astro.add(leftLeg)
        const rightLeg = makeLeg()
        rightLeg.position.set(0.3, -0.55, 0)
        astro.add(rightLeg)

        astro.scale.setScalar(1.15)
        astro.position.y = -0.25

        // interaction state
        const raycaster = new THREE.Raycaster()
        const mouse = new THREE.Vector2()
        let hovering = false
        let waveTime = 0
        let dragging = false
        let lastX = 0
        let lastY = 0
        let rotY = 0
        let rotX = -0.05
        let velY = 0.002
        let userInteracted = false

        function setMouseFromEvent(e) {
            const rect = renderer.domElement.getBoundingClientRect()
            const cx = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left
            const cy = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top
            mouse.x = (cx / rect.width) * 2 - 1
            mouse.y = -(cy / rect.height) * 2 + 1
        }

        function onMove(e) {
            setMouseFromEvent(e)
            raycaster.setFromCamera(mouse, camera)
            const hit = raycaster.intersectObject(astro, true)
            hovering = hit.length > 0
            wrap.style.cursor = dragging ? 'grabbing' : hovering ? 'pointer' : 'grab'

            if (dragging) {
                const clientX = e.touches ? e.touches[0].clientX : e.clientX
                const clientY = e.touches ? e.touches[0].clientY : e.clientY
                const dx = clientX - lastX
                const dy = clientY - lastY
                rotY += dx * 0.008
                rotX += dy * 0.006
                rotX = Math.max(-0.6, Math.min(0.6, rotX))
                lastX = clientX
                lastY = clientY
                velY = dx * 0.0006
            }
        }

        function onDown(e) {
            dragging = true
            userInteracted = true
            lastX = e.touches ? e.touches[0].clientX : e.clientX
            lastY = e.touches ? e.touches[0].clientY : e.clientY
        }
        function onUp() {
            dragging = false
        }

        renderer.domElement.addEventListener('mousemove', onMove)
        renderer.domElement.addEventListener('mousedown', onDown)
        window.addEventListener('mouseup', onUp)
        renderer.domElement.addEventListener('touchstart', onDown, { passive: true })
        renderer.domElement.addEventListener('touchmove', onMove, { passive: true })
        renderer.domElement.addEventListener('touchend', onUp)

        const clock = new THREE.Clock()

        function animate() {
            animId = requestAnimationFrame(animate)
            const dt = clock.getDelta()

            if (!dragging) {
                rotY += velY
                velY *= 0.96
            }
            // continuous idle spin, same approach as RocketScene.vue — always on, drag adds extra on top
            if (props.autoRotate) rotY += 0.004

            astro.rotation.y = rotY
            astro.rotation.x = rotX

            // weightless float: slower, larger bob + a gentle tumble on the other axes
            const bobFreq = 1.1
            const bob = Math.sin(clock.elapsedTime * bobFreq) * 0.14
            const bobVelocity = Math.cos(clock.elapsedTime * bobFreq) // drives flame intensity

            // base sideways lean — shifted right (into the empty space beside the text)
            // and rolled, with the float/tumble layered on top. Tune leanX to taste once
            // you see it in the wider full-bleed canvas — bigger number = further right.
            const leanX = 3.2
            const leanZ = 0.22
            astro.position.y = -0.25 + bob
            astro.position.x = leanX + Math.sin(clock.elapsedTime * 0.5) * 0.06
            astro.rotation.z = leanZ + Math.sin(clock.elapsedTime * 0.7) * 0.05

            // jetpack flame: flares when rising (bobVelocity > 0), flickers constantly
            const thrust = Math.max(0.35, (bobVelocity + 1) / 2) // 0.35–1 range, never fully off
            const flicker = 0.85 + Math.sin(clock.elapsedTime * 28) * 0.1 + (Math.random() - 0.5) * 0.08
            flame.scale.set(flicker, thrust * flicker, flicker)
            flameLight.intensity = 0.6 + thrust * 1.2

            // rocks: gentle individual drift + spin, plus the whole field slowly turning
            rockGroup.rotation.y += dt * 0.06
            rocks.forEach((r) => {
                const wobble = Math.sin(clock.elapsedTime * r.driftSpeed + r.driftPhase) * 0.12
                r.mesh.position.set(
                    r.basePos.x,
                    r.basePos.y + wobble,
                    r.basePos.z
                )
                r.mesh.rotation.x += dt * r.spinX
                r.mesh.rotation.y += dt * r.spinY
            })

            if (hovering) {
                // raise the arm up and OUTWARD (away from the body), then wag side to side.
                // positive z-rotation on the right arm swings it up-and-out; the old
                // negative value swung it inward across the chest, which read as "wrong direction".
                waveTime += dt * 6
                const wag = Math.sin(waveTime) * 0.35
                rightArmPivot.rotation.z = THREE.MathUtils.lerp(rightArmPivot.rotation.z, 2.5 + wag, 0.25)
                rightArmPivot.rotation.x = THREE.MathUtils.lerp(rightArmPivot.rotation.x, -0.2, 0.2)
                leftArmPivot.rotation.z = THREE.MathUtils.lerp(leftArmPivot.rotation.z, 0.35, 0.1)
            } else {
                waveTime = 0
                rightArmPivot.rotation.z = THREE.MathUtils.lerp(rightArmPivot.rotation.z, -0.35, 0.12)
                rightArmPivot.rotation.x = THREE.MathUtils.lerp(rightArmPivot.rotation.x, 0, 0.12)
                leftArmPivot.rotation.z = THREE.MathUtils.lerp(
                    leftArmPivot.rotation.z,
                    0.35 + Math.sin(clock.elapsedTime * 1.2) * 0.03,
                    0.1
                )
            }

            renderer.render(scene, camera)
        }
        animate()

        // cleanup handlers stored for onBeforeUnmount
        wrap.__cleanup = () => {
            renderer.domElement.removeEventListener('mousemove', onMove)
            renderer.domElement.removeEventListener('mousedown', onDown)
            window.removeEventListener('mouseup', onUp)
            renderer.domElement.removeEventListener('touchstart', onDown)
            renderer.domElement.removeEventListener('touchmove', onMove)
            renderer.domElement.removeEventListener('touchend', onUp)
        }
    })

    onBeforeUnmount(() => {
        if (animId) cancelAnimationFrame(animId)
        if (resizeObserver) resizeObserver.disconnect()
        const wrap = wrapRef.value
        if (wrap && wrap.__cleanup) wrap.__cleanup()
        if (renderer) {
            renderer.dispose()
            if (renderer.domElement && renderer.domElement.parentNode) {
                renderer.domElement.parentNode.removeChild(renderer.domElement)
            }
        }
        if (scene) {
            scene.traverse((obj) => {
                if (obj.geometry) obj.geometry.dispose()
                if (obj.material) {
                    if (Array.isArray(obj.material)) obj.material.forEach((m) => m.dispose())
                    else obj.material.dispose()
                }
            })
        }
    })
</script>

<style scoped>
    .astro-wrap {
        position: relative;
        width: 100%;
        touch-action: none;
        cursor: grab;
    }

    .astro-hint {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        pointer-events: none;
        z-index: 2;
    }
</style>