<template>
    <section class="play-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">You found it 🚀</span>
                <h2 class="section-title">Dodge the asteroids</h2>
                <p class="section-desc">
                    A little easter egg from the team — built with the same Three.js
                    setup we use on real client sites. Move your cursor to steer.
                </p>
            </div>

            <div class="game-frame">
                <div ref="mountRef" class="three-mount"></div>

                <div class="hud">
                    <span class="hud-score">Score <span>{{ score }}</span></span>
                    <span class="hud-hearts">
                        <span v-for="n in maxHealth" :key="n">{{ health >= n ? '❤️' : '🖤' }}</span>
                    </span>
                    <span class="hud-speed">{{ Math.round(speedKmh) }} km/h</span>
                </div>

                <div class="controls">
                    <button class="btn-icon" @click="toggleMute" :aria-label="muted ? 'Unmute' : 'Mute'">
                        {{ muted ? '🔇' : '🔊' }}
                    </button>
                    <button class="btn btn-secondary btn-small" @click="reset">Restart</button>
                </div>

                <div class="over-msg" v-if="!alive">
                    <span class="over-title">Crashed</span>
                    <span class="over-sub">Score: {{ score }}</span>
                    <button class="btn btn-primary btn-small" @click="reset">Restart</button>
                </div>
            </div>

            <div class="how-to-grid">
                <div class="how-card">
                    <span class="how-icon">🖱️</span>
                    <h3>Steer</h3>
                    <p>Move your cursor (or drag on mobile) to guide the rocket left, right, up and down.</p>
                </div>
                <div class="how-card">
                    <span class="how-icon">☄️</span>
                    <h3>Dodge</h3>
                    <p>Small asteroids are fast, big ones are slow but hit harder — watch which side they drift in from.</p>
                </div>
                <div class="how-card">
                    <span class="how-icon">✨</span>
                    <h3>Collect</h3>
                    <p>Fly near a glowing orb — it gets pulled in automatically — for +5 bonus points.</p>
                </div>
                <div class="how-card">
                    <span class="how-icon">❤️</span>
                    <h3>Heal</h3>
                    <p>You've got 3 hearts. Rare pink hearts drift by occasionally to top you back up.</p>
                </div>
            </div>

            <div class="cta-band">
                <p class="cta-text">Like the little details? This is the same care we put into every site we build.</p>
                <router-link to="/contact" class="btn btn-primary">Start a project</router-link>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
    import * as THREE from 'three'

    const mountRef = ref(null)
    const score = ref(0)
    const speedKmh = ref(160)
    const alive = ref(true)
    const muted = ref(false)
    const maxHealth = 3
    const health = ref(maxHealth)

    let renderer, scene, camera, rocket, rocketLight, stars
    let orbMat, haloMatInner, haloMatOuter
    let heartMat, heartHaloInner, heartHaloOuter
    function makeAsteroidMaterial(baseHue, baseLight) {
        const hue = baseHue + (Math.random() - 0.5) * 0.04
        const lightness = baseLight + (Math.random() - 0.5) * 0.08
        const color = new THREE.Color().setHSL(hue, 0.55, lightness)
        return new THREE.MeshStandardMaterial({
            color,
            flatShading: true,
            roughness: 0.55 + Math.random() * 0.25,
            metalness: 0.15 + Math.random() * 0.15
        })
    }
    function makeHeartGeometry(size, depth) {
        const s = size
        const shape = new THREE.Shape()
        shape.moveTo(0, s * 0.35)
        shape.bezierCurveTo(0, s * 0.35, -s * 0.5, -s * 0.15, -s, s * 0.35)
        shape.bezierCurveTo(-s * 1.55, s * 0.85, -s * 0.55, s * 1.35, 0, s * 1.9)
        shape.bezierCurveTo(s * 0.55, s * 1.35, s * 1.55, s * 0.85, s, s * 0.35)
        shape.bezierCurveTo(s * 0.5, -s * 0.15, 0, s * 0.35, 0, s * 0.35)
        const geo = new THREE.ExtrudeGeometry(shape, { depth, bevelEnabled: false })
        geo.center()
        return geo
    }

    let asteroids = []
    let orbs = []
    let hearts = []
    let clock
    let animationId
    let actx = null
    let invulnerableUntil = 0

    const laneX = 4.4
    const laneY = 2.9
    const target = { x: 0, y: 0 }
    const pos = { x: 0, y: 0 }
    const SPAWN_Z = -80
    const BASE_KMH = 160
    const MAX_KMH = 700
    const MAGNET_RADIUS = 1.6
    const MAGNET_PULL = 0.18
    const PICKUP_RADIUS = 0.55
    const INVULN_SECONDS = 1.4

    let frame = 0
    let spawnEvery = 120
    let orbEvery = 110
    let bigEvery = 420
    let heartEvery = 780
    let speedMult = 1

    function ensureAudio() {
        if (!actx) {
            const AC = window.AudioContext || window.webkitAudioContext
            actx = new AC()
        }
        if (actx.state === 'suspended') actx.resume()
    }

    function tone(freq, dur, type, peak, delay, glideTo) {
        if (muted.value || !actx) return
        const t0 = actx.currentTime + (delay || 0)
        const osc = actx.createOscillator()
        const g = actx.createGain()
        osc.type = type
        osc.frequency.setValueAtTime(freq, t0)
        if (glideTo) osc.frequency.exponentialRampToValueAtTime(glideTo, t0 + dur)
        g.gain.setValueAtTime(0.0001, t0)
        g.gain.exponentialRampToValueAtTime(peak, t0 + 0.02)
        g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur)
        osc.connect(g)
        g.connect(actx.destination)
        osc.start(t0)
        osc.stop(t0 + dur + 0.05)
    }

    function playCollect() { ensureAudio(); tone(700, 0.09, 'triangle', 0.18, 0); tone(1050, 0.12, 'triangle', 0.15, 0.06) }
    function playHeal() { ensureAudio(); tone(500, 0.12, 'sine', 0.2, 0); tone(650, 0.14, 'sine', 0.16, 0.08); tone(820, 0.16, 'sine', 0.12, 0.16) }
    function playHit() { ensureAudio(); tone(160, 0.18, 'square', 0.18, 0, 110) }
    function playCrash() { ensureAudio(); tone(220, 0.35, 'sawtooth', 0.22, 0, 60); tone(90, 0.3, 'square', 0.14, 0.03) }
    function playRumble() { ensureAudio(); tone(70, 0.5, 'sawtooth', 0.12, 0, 45) }

    function toggleMute() {
        muted.value = !muted.value
    }

    function spawnAsteroid(mult) {
        const fromLeft = Math.random() < 0.5
        const startX = (fromLeft ? -1 : 1) * (laneX * 0.5 + Math.random() * laneX * 0.7)
        const geo = new THREE.IcosahedronGeometry(0.35 + Math.random() * 0.4, 0)
        const m = new THREE.Mesh(geo, makeAsteroidMaterial(0.728, 0.58))
        m.position.set(startX, (Math.random() - 0.5) * laneY * 2.2, SPAWN_Z - Math.random() * 10)
        m.userData.speed = (0.075 + Math.random() * 0.02) * mult
        m.userData.vx = (fromLeft ? 1 : -1) * (0.012 + Math.random() * 0.02)
        m.userData.vy = (Math.random() - 0.5) * 0.016
        m.userData.rot = (Math.random() - 0.5) * 0.03
        m.userData.r = 0.55
        scene.add(m)
        asteroids.push(m)
    }

    function spawnBigAsteroid(mult) {
        const fromLeft = Math.random() < 0.5
        const startX = (fromLeft ? -1 : 1) * (laneX * 0.4 + Math.random() * laneX * 0.5)
        const r = 1.1 + Math.random() * 0.7
        const geo = new THREE.IcosahedronGeometry(r, 1)
        const m = new THREE.Mesh(geo, makeAsteroidMaterial(0.68, 0.5))
        m.position.set(startX, (Math.random() - 0.5) * laneY * 1.8, SPAWN_Z - 15)
        m.userData.speed = (0.045 + Math.random() * 0.015) * mult
        m.userData.vx = (fromLeft ? 1 : -1) * (0.006 + Math.random() * 0.01)
        m.userData.vy = (Math.random() - 0.5) * 0.008
        m.userData.rot = (Math.random() - 0.5) * 0.015
        m.userData.r = r
        m.userData.big = true
        scene.add(m)
        asteroids.push(m)
        playRumble()
    }

    function spawnOrb(mult) {
        const m = new THREE.Mesh(new THREE.SphereGeometry(0.18, 10, 10), orbMat)
        const haloIn = new THREE.Mesh(new THREE.SphereGeometry(0.34, 10, 10), haloMatInner)
        const haloOut = new THREE.Mesh(new THREE.SphereGeometry(0.56, 10, 10), haloMatOuter)
        m.add(haloIn)
        m.add(haloOut)
        m.position.set((Math.random() - 0.5) * laneX * 2, (Math.random() - 0.5) * laneY * 2, SPAWN_Z - Math.random() * 10)
        m.userData.speed = (0.075 + Math.random() * 0.02) * mult
        const pl = new THREE.PointLight(0xFFD34D, 1.0, 3, 2)
        m.add(pl)
        scene.add(m)
        orbs.push(m)
    }

    function spawnHeart(mult) {
        const geo = makeHeartGeometry(0.16, 0.09)
        const m = new THREE.Mesh(geo, heartMat)
        const haloIn = new THREE.Mesh(new THREE.SphereGeometry(0.32, 10, 10), heartHaloInner)
        const haloOut = new THREE.Mesh(new THREE.SphereGeometry(0.52, 10, 10), heartHaloOuter)
        m.add(haloIn)
        m.add(haloOut)
        m.position.set((Math.random() - 0.5) * laneX * 1.6, (Math.random() - 0.5) * laneY * 1.6, SPAWN_Z - Math.random() * 10)
        m.userData.speed = (0.075 + Math.random() * 0.02) * mult
        const pl = new THREE.PointLight(0xFF5C7A, 1.0, 3, 2)
        m.add(pl)
        scene.add(m)
        hearts.push(m)
    }

    function disposeMesh(mesh) {
        scene.remove(mesh)
        const sharedMats = [orbMat, haloMatInner, haloMatOuter, heartMat, heartHaloInner, heartHaloOuter]
        mesh.traverse((child) => {
            if (child.geometry) child.geometry.dispose()
            if (child.material && !sharedMats.includes(child.material)) {
                child.material.dispose()
            }
        })
    }

    function reset() {
        ensureAudio()
        asteroids.forEach(a => disposeMesh(a))
        orbs.forEach(o => disposeMesh(o))
        hearts.forEach(h => disposeMesh(h))
        asteroids = []
        orbs = []
        hearts = []
        pos.x = 0; pos.y = 0; target.x = 0; target.y = 0
        rocket.position.set(0, 0, 0)
        rocket.visible = true
        frame = 0
        spawnEvery = 120
        speedMult = 1
        invulnerableUntil = 0
        score.value = 0
        health.value = maxHealth
        speedKmh.value = BASE_KMH
        alive.value = true
        clock.start()
    }

    function onMove(clientX, clientY) {
        const rect = renderer.domElement.getBoundingClientRect()
        const nx = ((clientX - rect.left) / rect.width) * 2 - 1
        const ny = ((clientY - rect.top) / rect.height) * 2 - 1
        target.x = nx * laneX
        target.y = -ny * laneY
    }

    function handleMouseMove(e) { ensureAudio(); onMove(e.clientX, e.clientY) }
    function handleTouchMove(e) {
        ensureAudio()
        const t = e.touches[0]
        onMove(t.clientX, t.clientY)
        e.preventDefault()
    }

    function resize() {
        const mount = mountRef.value
        if (!mount) return
        const w = mount.clientWidth
        const h = mount.clientHeight
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2))
        renderer.setSize(w, h)
        camera.aspect = w / h
        camera.updateProjectionMatrix()
    }

    function animate() {
        animationId = requestAnimationFrame(animate)
        const dt = Math.min(clock.getDelta(), 0.05)
        const t = clock.getElapsedTime()

        if (alive.value) {
            frame++
            pos.x += (target.x - pos.x) * 0.1
            pos.y += (target.y - pos.y) * 0.1
            rocket.position.x = pos.x
            rocket.position.y = pos.y
            rocket.rotation.z = (target.x - pos.x) * -0.6
            rocket.rotation.x = (target.y - pos.y) * 0.4

            // Blink the rocket while invulnerable right after taking a hit
            rocket.visible = t < invulnerableUntil ? Math.floor(t * 12) % 2 === 0 : true

            speedKmh.value = Math.min(MAX_KMH, BASE_KMH + Math.floor(score.value / 60) * 25)
            speedMult = speedKmh.value / BASE_KMH
            spawnEvery = Math.max(42, 120 - Math.floor(score.value / 60) * 8)

            if (frame % spawnEvery === 0) {
                spawnAsteroid(speedMult)
            }
            if (frame % bigEvery === 0) spawnBigAsteroid(speedMult)
            if (frame % orbEvery === 0) spawnOrb(speedMult)
            if (frame % heartEvery === 0 && health.value < maxHealth && Math.random() < 0.5) {
                spawnHeart(speedMult)
            }
            if (frame % 15 === 0) score.value += 1

            for (let i = asteroids.length - 1; i >= 0; i--) {
                const a = asteroids[i]
                a.position.z += a.userData.speed
                a.position.x += a.userData.vx
                a.position.y += a.userData.vy
                a.rotation.x += a.userData.rot
                a.rotation.y += a.userData.rot * 1.3
                if (a.position.z > 3) { disposeMesh(a); asteroids.splice(i, 1); continue }
                const dx = a.position.x - rocket.position.x
                const dy = a.position.y - rocket.position.y
                const dz = a.position.z - rocket.position.z
                const hitDist = 0.45 + (a.userData.r || 0.4)
                if (Math.sqrt(dx * dx + dy * dy + dz * dz) < hitDist) {
                    disposeMesh(a)
                    asteroids.splice(i, 1)
                    if (t > invulnerableUntil) {
                        health.value -= 1
                        invulnerableUntil = t + INVULN_SECONDS
                        if (health.value <= 0) {
                            alive.value = false
                            playCrash()
                        } else {
                            playHit()
                        }
                    }
                }
            }

            for (let k = orbs.length - 1; k >= 0; k--) {
                const o = orbs[k]
                o.position.z += o.userData.speed
                const odx = rocket.position.x - o.position.x
                const ody = rocket.position.y - o.position.y
                const odz = rocket.position.z - o.position.z
                const odist = Math.sqrt(odx * odx + ody * ody + odz * odz)
                if (odist < MAGNET_RADIUS) {
                    o.position.x += odx * MAGNET_PULL
                    o.position.y += ody * MAGNET_PULL
                    o.position.z += odz * MAGNET_PULL
                }
                o.rotation.y += 0.06
                const pulse = 1 + Math.sin(t * 4 + k) * 0.08
                o.scale.set(pulse, pulse, pulse)
                if (o.position.z > 3) { disposeMesh(o); orbs.splice(k, 1); continue }
                if (odist < PICKUP_RADIUS) {
                    score.value += 5
                    playCollect()
                    disposeMesh(o)
                    orbs.splice(k, 1)
                }
            }

            for (let j = hearts.length - 1; j >= 0; j--) {
                const h = hearts[j]
                h.position.z += h.userData.speed
                const hdx = rocket.position.x - h.position.x
                const hdy = rocket.position.y - h.position.y
                const hdz = rocket.position.z - h.position.z
                const hdist = Math.sqrt(hdx * hdx + hdy * hdy + hdz * hdz)
                if (hdist < MAGNET_RADIUS) {
                    h.position.x += hdx * MAGNET_PULL
                    h.position.y += hdy * MAGNET_PULL
                    h.position.z += hdz * MAGNET_PULL
                }
                h.rotation.y += 0.05
                const pulse = 1 + Math.sin(t * 3.5 + j) * 0.1
                h.scale.set(pulse, pulse, pulse)
                if (h.position.z > 3) { disposeMesh(h); hearts.splice(j, 1); continue }
                if (hdist < PICKUP_RADIUS) {
                    if (health.value < maxHealth) health.value += 1
                    playHeal()
                    disposeMesh(h)
                    hearts.splice(j, 1)
                }
            }

            stars.position.z += dt * (1.0 + speedMult * 0.6)
            if (stars.position.z > 20) stars.position.z = 0
        }

        const camTargetPos = new THREE.Vector3(rocket.position.x * 0.6, rocket.position.y * 0.5 + 0.6, rocket.position.z + 2.8)
        camera.position.lerp(camTargetPos, 0.08)
        const lookAt = new THREE.Vector3(rocket.position.x * 0.8, rocket.position.y * 0.8, rocket.position.z - 4)
        camera.lookAt(lookAt)

        renderer.render(scene, camera)
    }

    onMounted(() => {
        const mount = mountRef.value

        scene = new THREE.Scene()
        camera = new THREE.PerspectiveCamera(68, mount.clientWidth / mount.clientHeight, 0.1, 200)
        renderer = new THREE.WebGLRenderer({ antialias: true })
        renderer.setClearColor(0x0A0417, 1)
        mount.appendChild(renderer.domElement)

        scene.add(new THREE.AmbientLight(0x6a5aa8, 0.55))
        const dirLight = new THREE.DirectionalLight(0xffd34d, 0.7)
        dirLight.position.set(3, 4, 2)
        scene.add(dirLight)

        const rimLight = new THREE.DirectionalLight(0xC084FC, 0.5)
        rimLight.position.set(-4, -2, -3)
        scene.add(rimLight)

        rocketLight = new THREE.PointLight(0xFFD34D, 1.4, 6, 2)
        rocketLight.position.set(0, 0, 0.6)

        const starGeo = new THREE.BufferGeometry()
        const starCount = 400
        const starPos = new Float32Array(starCount * 3)
        for (let i = 0; i < starCount; i++) {
            starPos[i * 3] = (Math.random() - 0.5) * 100
            starPos[i * 3 + 1] = (Math.random() - 0.5) * 60
            starPos[i * 3 + 2] = -Math.random() * 140
        }
        starGeo.setAttribute('position', new THREE.BufferAttribute(starPos, 3))
        const starMat = new THREE.PointsMaterial({ color: 0xffffff, size: 0.18, transparent: true, opacity: 0.85 })
        stars = new THREE.Points(starGeo, starMat)
        scene.add(stars)

        rocket = new THREE.Group()
        const bodyMat = new THREE.MeshStandardMaterial({ color: 0xC084FC, flatShading: true, roughness: 0.5 })
        const body = new THREE.Mesh(new THREE.ConeGeometry(0.35, 1.2, 6), bodyMat)
        body.rotation.x = Math.PI / 2
        rocket.add(body)

        const finMat = new THREE.MeshStandardMaterial({ color: 0x7C3AED, flatShading: true, roughness: 0.7 })
        for (let f = 0; f < 3; f++) {
            const fin = new THREE.Mesh(new THREE.ConeGeometry(0.12, 0.4, 3), finMat)
            fin.rotation.x = Math.PI / 2
            const ang = (Math.PI * 2 / 3) * f
            fin.position.set(Math.cos(ang) * 0.3, Math.sin(ang) * 0.3, 0.4)
            rocket.add(fin)
        }
        const noseGlow = new THREE.Mesh(new THREE.SphereGeometry(0.14, 6, 6), new THREE.MeshStandardMaterial({ color: 0xFFD34D, emissive: 0xFFD34D, emissiveIntensity: 1.4, flatShading: true }))
        noseGlow.position.set(0, 0, 0.65)
        rocket.add(noseGlow)
        rocket.add(rocketLight)
        scene.add(rocket)

        orbMat = new THREE.MeshStandardMaterial({ color: 0xFFD34D, emissive: 0xFFD34D, emissiveIntensity: 1.1, flatShading: true })
        haloMatInner = new THREE.MeshBasicMaterial({ color: 0xFFD34D, transparent: true, opacity: 0.16, blending: THREE.AdditiveBlending, depthWrite: false })
        haloMatOuter = new THREE.MeshBasicMaterial({ color: 0xFFD34D, transparent: true, opacity: 0.06, blending: THREE.AdditiveBlending, depthWrite: false })

        heartMat = new THREE.MeshStandardMaterial({ color: 0xFF5C7A, emissive: 0xFF5C7A, emissiveIntensity: 1.1, flatShading: true })
        heartHaloInner = new THREE.MeshBasicMaterial({ color: 0xFF5C7A, transparent: true, opacity: 0.18, blending: THREE.AdditiveBlending, depthWrite: false })
        heartHaloOuter = new THREE.MeshBasicMaterial({ color: 0xFF5C7A, transparent: true, opacity: 0.07, blending: THREE.AdditiveBlending, depthWrite: false })

        clock = new THREE.Clock()
        camera.position.set(0, 0.6, 2.8)

        resize()
        reset()
        animate()

        renderer.domElement.addEventListener('mousemove', handleMouseMove)
        renderer.domElement.addEventListener('touchmove', handleTouchMove, { passive: false })
        window.addEventListener('resize', resize)
    })

    onBeforeUnmount(() => {
        cancelAnimationFrame(animationId)
        window.removeEventListener('resize', resize)
        if (renderer) {
            renderer.domElement.removeEventListener('mousemove', handleMouseMove)
            renderer.domElement.removeEventListener('touchmove', handleTouchMove)
            renderer.dispose()
        }
        if (actx) actx.close()
    })
</script>


<style scoped>
    .play-section {
        position: relative;
        margin-top: -75px;
        padding: calc(6rem + 75px) 0 6rem 0;
        background-color: var(--color-background-soft);
        background-image: radial-gradient(circle at 15% 10%, rgba(124, 58, 237, 0.16), transparent 45%), radial-gradient(circle at 85% 85%, rgba(192, 132, 252, 0.12), transparent 50%);
        color: var(--td-white);
        overflow: hidden;
    }

    .container {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .section-header {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 3rem auto;
    }

    .section-subtitle {
        text-transform: uppercase;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--td-accent);
        letter-spacing: 1px;
    }

    .section-title {
        font-size: 2.25rem;
        margin: 0.75rem 0 1rem 0;
        letter-spacing: -0.75px;
    }

    .section-desc {
        font-size: 1.05rem;
        line-height: 1.6;
        opacity: 0.8;
    }

    .game-frame {
        position: relative;
        border-radius: 16px;
        border: 1px solid var(--td-card-border);
        overflow: hidden;
        background: var(--td-bg-page);
    }

    .three-mount {
        width: 100%;
        height: 560px;
        display: block;
    }

    @media (max-width: 768px) {
        .three-mount {
            height: 420px;
        }
    }

    .hud {
        position: absolute;
        top: 14px;
        left: 16px;
        z-index: 3;
        display: flex;
        flex-direction: column;
        gap: 4px;
        pointer-events: none;
    }

    .hud-score {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--td-star-gold);
    }

    .hud-hearts {
        font-size: 0.95rem;
        letter-spacing: 2px;
    }

    .hud-speed {
        font-size: 0.85rem;
        color: var(--td-accent);
    }

    .controls {
        position: absolute;
        top: 14px;
        right: 16px;
        z-index: 3;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-icon {
        background: rgba(124, 58, 237, 0.25);
        border: 1px solid var(--td-card-border);
        border-radius: 9999px;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.65rem 1.4rem;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 9999px;
        transition: all 0.25s ease;
        cursor: pointer;
        border: none;
    }

    .btn-small {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .btn-secondary {
        background-color: transparent;
        color: var(--color-heading);
        border: 1px solid var(--td-border-outline);
    }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.06);
            border-color: var(--td-accent);
        }

    .over-msg {
        position: absolute;
        inset: 0;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
        background: rgba(10, 4, 23, 0.65);
    }

    .over-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--td-star-gold);
    }

    .over-sub {
        font-size: 0.9rem;
        color: var(--td-accent);
    }

    .how-to-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-top: 2.5rem;
    }

    @media (max-width: 900px) {
        .how-to-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .how-to-grid {
            grid-template-columns: 1fr;
        }
    }

    .how-card {
        background-color: var(--td-card-bg);
        border: 1px solid var(--td-card-border);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .how-icon {
        font-size: 1.75rem;
        display: block;
        margin-bottom: 0.75rem;
    }

    .how-card h3 {
        font-size: 1.1rem;
        margin: 0 0 0.5rem 0;
    }

    .how-card p {
        font-size: 0.9rem;
        line-height: 1.55;
        opacity: 0.8;
        margin: 0;
    }

    .cta-band {
        margin-top: 3.5rem;
        padding: 2.5rem;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.18), rgba(192, 132, 252, 0.08));
        border: 1px solid var(--td-card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        text-align: left;
    }

    .cta-text {
        font-size: 1.05rem;
        margin: 0;
        max-width: 460px;
    }

    @media (max-width: 640px) {
        .cta-band {
            flex-direction: column;
            text-align: center;
        }
    }
</style>