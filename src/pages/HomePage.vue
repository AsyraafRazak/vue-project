<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import ThreeBackground from '../components/ThreeBackground.vue'
import Astronaut from '../components/Astronaut.vue'

const ctaSection = ref(null)
const astronautActive = ref(false)
const parallaxOffset = ref(0)
const whyUsContent = ref(null)
const contentActive = ref(false)

const features = [
    {
        title: 'Thoughtful design',
        desc: 'Clean, modern interfaces built around your brand — not generic templates. Every layout designed with purpose.',
        image: '/images/pictures/thoughtful-design.png',
        icon: '<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />',
    },
    {
        title: 'Solid development',
        desc: 'Fast, reliable builds using modern tools — from simple landing pages to fully custom interactive experiences.',
        image: '/images/pictures/solid-development.png',
        icon: '<polyline points="16 18 22 12 16 6" /><polyline points="8 6 2 12 8 18" />',
    },
    {
        title: 'Ongoing support',
        desc: 'Sites evolve. We stick around after launch for updates, fixes, and improvements — no disappearing after handoff.',
        image: '/images/pictures/ongoing-support.png',
        icon: '<circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />',
    },
    {
        title: 'Interactive experiences',
        desc: '3D visuals, animations, and interactive elements that make your site feel alive — not just another static page.',
        image: '/images/pictures/interactive-experiences.png',
        icon: '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" /><polyline points="3.27 6.96 12 12.01 20.73 6.96" /><line x1="12" y1="22.08" x2="12" y2="12" />',
    },
    {
        title: 'Fully responsive',
        desc: 'Every site works flawlessly across desktop, tablet, and mobile — tested and polished on every screen size.',
        image: '/images/pictures/fully-responsive.png',
        icon: '<rect x="4" y="2" width="16" height="20" rx="2" ry="2" /><line x1="12" y1="18" x2="12.01" y2="18" />',
    },
    {
        title: 'Brand identity',
        desc: 'Color palettes, typography, and visual direction that make your brand instantly recognizable and consistent.',
        image: '/images/pictures/brand-identity.png',
        icon: '<path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5" /><circle cx="7.5" cy="10.5" r="0.5" /><circle cx="12" cy="7.5" r="0.5" /><circle cx="16.5" cy="10.5" r="0.5" />',
    }
]

const activeFeatureIndex = ref(0)
const featureStepEls = ref([])
const sliderRef = ref(null)

function setFeatureStepRef(el, i) {
    if (el) featureStepEls.value[i] = el
}

function scrollToFeature(index) {
    activeFeatureIndex.value = index
    const targetEl = featureStepEls.value[index]
    if (targetEl) {
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' })
    }
}

function onSliderScroll() {
    if (window.innerWidth > 900 || !sliderRef.value) return
    const container = sliderRef.value
    const center = container.scrollLeft + container.clientWidth / 2

    let closestIdx = activeFeatureIndex.value
    let closestDist = Infinity

    featureStepEls.value.forEach((el, i) => {
        if (!el) return
        const elCenter = el.offsetLeft + el.offsetWidth / 2
        const dist = Math.abs(elCenter - center)
        if (dist < closestDist) {
            closestDist = dist
            closestIdx = i
        }
    })

    activeFeatureIndex.value = closestIdx
}

function updateActiveFeature() {
    if (window.innerWidth <= 900) return
    const viewportMid = window.innerHeight / 2
    let closestIndex = activeFeatureIndex.value
    let closestDist = Infinity

    featureStepEls.value.forEach((el, i) => {
        if (!el) return
        const rect = el.getBoundingClientRect()
        const dist = Math.abs((rect.top + rect.height / 2) - viewportMid)
        if (dist < closestDist) {
            closestDist = dist
            closestIndex = i
        }
    })

    activeFeatureIndex.value = closestIndex
}

function handleScroll() {
    const el = ctaSection.value
    if (!el) return

    const rect = el.getBoundingClientRect()
    const windowHeight = window.innerHeight

    astronautActive.value = rect.top < windowHeight * 0.85

    const progress = 1 - Math.max(0, Math.min(1, rect.top / windowHeight))
    parallaxOffset.value = progress * 40

    const contentEl = whyUsContent.value
    if (contentEl) {
        const contentRect = contentEl.getBoundingClientRect()
        contentActive.value = contentRect.top < windowHeight * 0.8
    }

    updateActiveFeature()
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true })
    handleScroll()
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <!-- Hero Section -->
    <section id="hero" class="hero-section">
        <video class="hero-bg-video" autoplay muted loop playsinline>
            <source src="https://slategrey-pig-993603.hostingersite.com/wp-content/uploads/2026/08/copy_F1BD6054-509C-417B-9A22-BBB84A59D313-4.mov" type="video/mp4" />
        </video>
        <div class="hero-overlay"></div>

        <span class="star-dot star-gold" style="top: 12%; left: 8%;">✦</span>
        <span class="star-dot star-purple" style="top: 22%; left: 46%;">✦</span>
        <span class="star-dot star-white" style="top: 8%; left: 62%;">✦</span>
        <span class="star-dot star-purple" style="top: 68%; left: 4%;">✦</span>
        <span class="star-dot star-gold" style="top: 78%; left: 52%;">✦</span>
        <span class="star-dot star-white" style="top: 40%; left: 92%;">✦</span>

        <div class="container">
            <div class="hero-content">
                <span class="kicker">TWO DEVS. ONE VISION.</span>
                <h1 class="hero-title">
                    Design bold. <br />
                    <span>Build Fast.</span>
                </h1>
                <p class="hero-lead">
                    We design and develop websites, apps, and digital products — from clean landing pages to full custom builds. Small team, direct communication, zero fluff.
                </p>
                <div class="hero-actions">
                    <a href="#signup" class="btn btn-primary btn-large">Start Your Project</a>
                    <a href="#features" class="btn btn-secondary btn-large">
                        See What We Do
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                    <div class="hero-social-proof">
                        <p>Built With Tools We Trust</p>
                        <div class="brand-logos">
                            <img src="https://cdn.simpleicons.org/vuedotjs/A99BC2" alt="Vue" class="brand-logo-icon" />
                            <img src="https://cdn.simpleicons.org/threedotjs/A99BC2" alt="Three.js" class="brand-logo-icon" />
                            <img src="https://cdn.simpleicons.org/figma/A99BC2" alt="Figma" class="brand-logo-icon" />
                            <img src="https://cdn.simpleicons.org/wordpress/A99BC2" alt="WordPress" class="brand-logo-icon" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-showcase">
                <div class="mock-browser mock-browser-back">
                    <div class="browser-header">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                        <span class="browser-address">app.twodazzle.io/analytics</span>
                    </div>
                    <div class="browser-body">
                        <div class="mock-sidebar">
                            <div class="mock-nav-item"></div>
                            <div class="mock-nav-item active"></div>
                            <div class="mock-nav-item"></div>
                            <div class="mock-nav-item"></div>
                        </div>
                        <div class="mock-main">
                            <div class="mock-chart-container">
                                <div class="mock-card">
                                    <div class="mock-card-header"></div>
                                    <div class="mock-chart-bar-group">
                                        <div class="mock-bar bar-1"></div>
                                        <div class="mock-bar bar-2"></div>
                                        <div class="mock-bar bar-3"></div>
                                        <div class="mock-bar bar-4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mock-browser mock-browser-front">
                    <div class="browser-header">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                        <span class="browser-address">app.twodazzle.io/dashboard</span>
                    </div>
                    <div class="browser-body">
                        <div class="mock-sidebar">
                            <div class="mock-nav-item active"></div>
                            <div class="mock-nav-item"></div>
                            <div class="mock-nav-item"></div>
                            <div class="mock-nav-item"></div>
                        </div>
                        <div class="mock-main">
                            <div class="mock-chart-container">
                                <div class="mock-card">
                                    <div class="mock-card-header"></div>
                                    <div class="mock-chart-bar-group">
                                        <div class="mock-bar bar-1"></div>
                                        <div class="mock-bar bar-2"></div>
                                        <div class="mock-bar bar-3"></div>
                                        <div class="mock-bar bar-4"></div>
                                    </div>
                                </div>
                                <div class="mock-card double">
                                    <div class="mock-card-header"></div>
                                    <div class="mock-lines">
                                        <div class="mock-line line-1"></div>
                                        <div class="mock-line line-2"></div>
                                        <div class="mock-line line-3"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mock-table">
                                <div class="mock-row header"></div>
                                <div class="mock-row"></div>
                                <div class="mock-row"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <ThreeBackground />
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Services</span>
                <h2 class="section-title">Everything you need to launch</h2>
                <p class="section-desc">
                    From first sketch to live site — TwoDazzle handles design, development,
                    and everything in between so you don't have to juggle multiple freelancers.
                </p>
            </div>

            <div class="pinned-features">
                <div class="pinned-visual">
                    <div class="pinned-visual-backdrop"></div>
                    <div class="pinned-visual-images">
                        <img v-for="(feature, i) in features"
                             :key="feature.title"
                             :src="feature.image"
                             :alt="feature.title"
                             class="pinned-visual-img"
                             :class="{ 'is-active': activeFeatureIndex === i }" />
                    </div>
                    <div class="pinned-visual-counter">
                        {{ String(activeFeatureIndex + 1).padStart(2, '0') }} / {{ String(features.length).padStart(2, '0') }}
                    </div>
                </div>

                <div class="feature-steps-wrapper">
                    <div class="feature-steps" ref="sliderRef" @scroll.passive="onSliderScroll">
                        <div v-for="(feature, i) in features"
                             :key="feature.title"
                             class="feature-step"
                             :class="{ 'is-active': activeFeatureIndex === i }"
                             :ref="el => setFeatureStepRef(el, i)">
                            <div class="feature-step-top">
                                <span class="feature-step-index">{{ String(i + 1).padStart(2, '0') }}</span>
                                <div class="feature-step-mobile-icon-box">
                                    <img :src="feature.image"
                                         :alt="feature.title"
                                         class="feature-step-mobile-img" />
                                </div>
                            </div>
                            <h3 class="feature-step-title">{{ feature.title }}</h3>
                            <p class="feature-step-desc">{{ feature.desc }}</p>
                        </div>
                    </div>

                    <!-- Mobile Carousel Pagination Dots -->
                    <div class="feature-slider-dots">
                        <button v-for="(_, i) in features"
                                :key="i"
                                class="feature-slider-dot"
                                :class="{ 'is-active': activeFeatureIndex === i }"
                                @click="scrollToFeature(i)"
                                :aria-label="`Go to feature ${i + 1}`"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits / Call to Action Banner -->
   <!-- Why Us Section -->
<!-- Why Us Section -->
<!-- Why Us Section -->
<section id="why-us" class="why-us-section" ref="ctaSection">
    <!-- Full-bleed 3D layer, sits behind the text -->
    <div class="why-us-scene">
        <Astronaut height="100%" />
    </div>

    <div class="container why-us-grid">
        <div class="why-us-content" ref="whyUsContent" :class="{ 'is-active': contentActive }">
            <span class="section-subtitle">Why Us</span>
            <h2 class="why-us-title">Two devs, one focus — your project</h2>
            <p class="why-us-text">
                No account managers, no hand-offs between teams. You talk directly
                to the people building your site, from first sketch to launch and beyond.
            </p>

            <ul class="why-us-list">
                <li><span class="why-us-check">✓</span> Direct access to the developers, not a middleman</li>
                <li><span class="why-us-check">✓</span> Small by design — fewer clients means more attention on yours</li>
                <li><span class="why-us-check">✓</span> 3D & interactive work most agencies can't touch</li>
                <li><span class="why-us-check">✓</span> We stick around after launch — real support, not silence</li>
            </ul>

            <div class="why-us-actions">
               
               <router-link to="/demo" class="btn btn-primary btn-large">View Our Demo</router-link>
            </div>
        </div>
    </div>
</section>
</template>

<style>
    /* Layout wrap */
    .app-layout {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding-top: 75px;
        /* Navbar height compensation. Every top-level page section now
           bleeds its own real background over this reserved strip via
           margin-top: -75px, so this div's own background never shows. */
    }

    .main-content {
        flex-grow: 1;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Typography resets and defaults */
    h1,
    h2,
    h3 {
        color: var(--color-heading);
        font-weight: 800;
        line-height: 1.25;
    }

    /* Buttons global definition */
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
        gap: 0.5rem;
    }

    .btn-large {
        padding: 0.85rem 1.8rem;
        font-size: 1.05rem;
    }

    .btn-primary {
        background-color: var(--td-primary);
        color: white;
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
    }

        .btn-primary:hover {
            background-color: var(--td-accent);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(192, 132, 252, 0.4);
        }

    .btn-secondary {
        background-color: transparent;
        color: var(--color-heading);
        border: 1px solid var(--td-border-outline);
    }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.06);
            border-color: var(--td-accent);
            transform: translateY(-2px);
        }

    .btn-light {
        background-color: white;
        color: #111827;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
    }

        .btn-light:hover {
            background-color: #f9fafb;
            transform: translateY(-2px);
        }

    .btn-outline-light {
        background-color: transparent;
        color: white;
        border: 1px solid var(--td-border-outline);
    }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-2px);
        }

    /* Kicker text */
    .kicker {
        display: inline-block;
        color: var(--td-accent);
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }

    /* Decorative star dots */
    .star-dot {
        position: absolute;
        font-size: 0.9rem;
        line-height: 1;
        pointer-events: none;
        animation: twinkle 3.5s ease-in-out infinite;
        z-index: 2;
    }

    .star-gold {
        color: var(--td-star-gold);
    }

    .star-purple {
        color: var(--td-star-purple);
    }

    .star-white {
        color: var(--td-star-white);
    }

    .star-dot:nth-child(3) {
        animation-delay: 0.5s;
    }

    .star-dot:nth-child(4) {
        animation-delay: 1s;
    }

    .star-dot:nth-child(5) {
        animation-delay: 1.5s;
    }

    .star-dot:nth-child(6) {
        animation-delay: 2s;
    }

    .star-dot:nth-child(7) {
        animation-delay: 2.5s;
    }

    @keyframes twinkle {

        0%, 100% {
            opacity: 0.35;
            transform: scale(0.85);
        }

        50% {
            opacity: 1;
            transform: scale(1.1);
        }
    }

    /* Hero Section styles */
    .hero-section {
        position: relative;
        margin-top: -75px;
        padding: calc(5rem + 75px) 0 6rem 0;
        overflow: hidden;
        background: var(--td-bg-page);
    }

    /* Hero background video */
    .hero-bg-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(10, 4, 23, 0.9); /* tweak 0.7 = 70% dark */
        z-index: 1;
    }

    .hero-section .container {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        align-items: center;
        gap: 4rem;
    }

    @media (max-width: 968px) {
        .hero-section .container {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 3rem;
        }

        .hero-showcase {
            display: none;
        }
    }

    .hero-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    @media (max-width: 968px) {
        .hero-content {
            align-items: center;
        }
    }

    .hero-title {
        font-size: 3.5rem;
        letter-spacing: -1.5px;
        margin-bottom: 1.5rem;
    }

        .hero-title span {
            color: var(--td-accent);
        }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 2.5rem;
        }
    }

    .hero-lead {
        font-size: 1.15rem;
        line-height: 1.6;
        opacity: 0.85;
        margin-bottom: 2.25rem;
        max-width: 540px;
    }

    .hero-actions {
        display: flex;
        gap: 1rem;
        margin-bottom: 3.5rem;
        flex-wrap: wrap;
    }

    .hero-social-proof {
        font-size: 0.85rem;
        opacity: 0.7;
    }

    .brand-logos {
        display: flex;
        gap: 2rem;
        margin-top: 0.75rem;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Mock Showcase browser frame styling */
    .hero-showcase {
        width: 100%;
    }

    .mock-browser {
        background-color: var(--color-background-soft);
        border: 1px solid var(--color-border);
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        aspect-ratio: 16 / 10;
    }

    .browser-header {
        background-color: var(--color-background-mute);
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid var(--color-border);
    }

        .browser-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

    .dot.red {
        background-color: #ef4444;
    }

    .dot.yellow {
        background-color: #f59e0b;
    }

    .dot.green {
        background-color: #10b981;
    }

    .browser-address {
        margin: 0 auto;
        font-size: 0.75rem;
        background-color: var(--color-background);
        padding: 0.2rem 2.5rem;
        border-radius: 6px;
        opacity: 0.8;
        border: 1px solid var(--color-border);
    }

    .browser-body {
        flex-grow: 1;
        display: flex;
        background-color: var(--color-background);
    }

    .mock-sidebar {
        width: 50px;
        border-right: 1px solid var(--color-border);
        background-color: var(--color-background-soft);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1rem 0;
        gap: 0.75rem;
    }

    .mock-nav-item {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background-color: var(--color-border);
    }

        .mock-nav-item.active {
            background-color: var(--td-primary);
        }

    .mock-main {
        flex-grow: 1;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .mock-chart-container {
        display: flex;
        gap: 1rem;
        height: 90px;
    }

    .mock-card {
        flex: 1;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

        .mock-card.double {
            flex: 1.5;
        }

    .mock-card-header {
        width: 50%;
        height: 8px;
        background-color: var(--color-border);
        border-radius: 4px;
    }

    .mock-chart-bar-group {
        display: flex;
        align-items: flex-end;
        height: 100%;
        gap: 0.5rem;
        padding-top: 0.5rem;
    }

    .mock-bar {
        flex: 1;
        background-color: var(--color-border);
        border-radius: 2px 2px 0 0;
    }

        .mock-bar.bar-1 {
            height: 40%;
            background-color: var(--td-primary);
        }

        .mock-bar.bar-2 {
            height: 80%;
        }

        .mock-bar.bar-3 {
            height: 60%;
        }

        .mock-bar.bar-4 {
            height: 95%;
            background-color: var(--td-accent);
        }

    .mock-lines {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        height: 100%;
        justify-content: center;
    }

    .mock-line {
        height: 6px;
        background-color: var(--color-border);
        border-radius: 3px;
    }

        .mock-line.line-1 {
            width: 100%;
        }

        .mock-line.line-2 {
            width: 75%;
        }

        .mock-line.line-3 {
            width: 90%;
        }

    .mock-table {
        flex-grow: 1;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .mock-row {
        flex: 1;
        border-bottom: 1px solid var(--color-border);
        background-color: var(--color-background-soft);
    }

        .mock-row:last-child {
            border-bottom: none;
        }

        .mock-row.header {
            background-color: var(--color-background-mute);
        }

    /* Features section styling */
    .features-section {
        padding: 6rem 0;
        background-color: var(--color-background-soft);
        border-top: 1px solid var(--color-border);
        border-bottom: 1px solid var(--color-border);
        transition: background-color 0.5s, border-color 0.5s;
    }

    .section-header {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 4rem auto;
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

    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    @media (max-width: 868px) {
        .features-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    .feature-card {
        background-color: var(--td-card-bg);
        border: 1px solid var(--td-card-border);
        border-radius: 12px;
        padding: 2.25rem;
        transition: all 0.3s ease;
    }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(124, 58, 237, 0.15);
            border-color: var(--td-accent);
        }

    .feature-icon {
        width: 48px;
        height: 48px;
        background-color: rgba(124, 58, 237, 0.25);
        color: var(--td-accent);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .feature-name {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        letter-spacing: -0.25px;
    }

    .feature-desc {
        font-size: 0.95rem;
        line-height: 1.6;
        opacity: 0.8;
    }

    /* Call to Action Banner section styles */
    .cta-banner-section {
        padding: 6rem 0;
    }

    .cta-banner {
        background: linear-gradient(135deg, var(--td-primary) 0%, #4c1d95 100%);
        border-radius: 20px;
        padding: 4.5rem 2rem;
        text-align: center;
        color: white;
        box-shadow: 0 20px 40px rgba(124, 58, 237, 0.25);
    }

    .cta-banner-title {
        color: white;
        font-size: 2.25rem;
        margin-bottom: 1rem;
    }

    .cta-banner-text {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 550px;
        margin: 0 auto 2.25rem auto;
        line-height: 1.6;
    }

    .cta-banner-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    @media (max-width: 640px) {
        .hero-section {
            padding: 3rem 0 3.5rem 0;
            padding: calc(3rem + 75px) 0 3.5rem 0;
        }

        .container {
            padding: 0 1.25rem;
        }

        .hero-title {
            font-size: 2.1rem;
            letter-spacing: -0.5px;
        }

        .hero-lead {
            font-size: 1rem;
            max-width: 100%;
        }

        .hero-actions {
            flex-direction: column;
            align-items: center;
            width: 100%;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }

        .btn-large {
            width: 100%;
            justify-content: center;
        }

        .hero-social-proof {
            margin-top: 0.5rem;
            text-align: center;
        }

        .brand-logos {
            justify-content: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .kicker {
            font-size: 0.75rem;
            margin-bottom: 1rem;
        }

        .star-dot {
            display: none;
        }

        .hero-showcase {
            height: 220px;
            margin-top: 1rem;
        }

        .mock-browser-back {
            display: none;
        }

        .mock-browser-front {
            top: 0;
        }

        .mock-sidebar {
            width: 36px;
        }

        .mock-nav-item {
            width: 18px;
            height: 18px;
        }

        .browser-address {
            font-size: 0.65rem;
            padding: 0.15rem 1rem;
        }
    }

    @media (max-width: 400px) {
        .hero-title {
            font-size: 1.8rem;
        }
    }

 .why-us-section {
      padding: 60px 0;      /* was 100px 0 */
    background: #0A0417;
    position: relative;
    overflow: hidden;
    min-height: 480px;  
}

.why-us-scene {
    position: absolute;
    inset: 0;
    z-index: 0;
}

.why-us-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr;
    pointer-events: none;
    min-height: 640px; /* keeps the grid itself matching the section, so content
                           stays vertically centered in that space rather than
                           collapsing to just its own content height */
    align-content: center;
}

.why-us-content {
    max-width: 560px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.why-us-content .section-subtitle,
.why-us-title,
.why-us-text,
.why-us-list li,
.why-us-actions {
    opacity: 0;
    transform: translateY(44px);
    transition: opacity 0.85s cubic-bezier(0.19, 1, 0.22, 1), transform 0.85s cubic-bezier(0.19, 1, 0.22, 1);
}

.why-us-content.is-active .section-subtitle {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0s;
}

.why-us-content.is-active .why-us-title {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.08s;
}

.why-us-content.is-active .why-us-text {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.18s;
}

.why-us-content.is-active .why-us-list li:nth-child(1) {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.28s;
}

.why-us-content.is-active .why-us-list li:nth-child(2) {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.35s;
}

.why-us-content.is-active .why-us-list li:nth-child(3) {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.42s;
}

.why-us-content.is-active .why-us-list li:nth-child(4) {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.49s;
}

.why-us-content.is-active .why-us-actions {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.58s;
}

.why-us-actions {
    display: flex;
    gap: 1rem;
    margin-top: 12px;
}

.why-us-actions a {
    pointer-events: auto;
}

@media (max-width: 900px) {
    .why-us-section {
        min-height: auto;
        display: flex;
        flex-direction: column;
    }

    .why-us-scene {
        position: relative;
        inset: auto;
        order: 2;
        width: 100%;
        height: 280px;
    }

    .why-us-grid {
        order: 1;
        min-height: auto;
        pointer-events: auto;
    }
}
</style>
<style scoped>
    .brand-logo-icon {
        height: 24px;
        width: auto;
        opacity: 0.6;
    }
</style>
<style scoped>
    .hero-showcase {
        position: relative;
        width: 100%;
        height: 300px;
        overflow: visible;
    }

    .mock-browser {
        position: absolute;
        border-radius: 10px;
        overflow: hidden;
        background: #12081F;
        border: 1px solid rgba(192, 132, 252, 0.2);
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }

    .mock-browser-front {
        top: 20px;
        left: 0;
        width: 100%;
        z-index: 2;
    }

    .mock-browser-back {
        top: -100px;
        left: 0;
        width: 100%;
        z-index: 1;
        opacity: 0.5;
        transform: scale(0.95) translateX(30%);
        filter: blur(0.5px);
    }
</style>
<style scoped>
    .features-section {
        position: relative;
    }

        .features-section .container {
            position: relative;
            z-index: 1;
        }
</style>

<style scoped>
    .cta-banner-section {
        position: relative;
        overflow: hidden;
    }

    .astronaut-parallax {
        position: absolute;
        bottom: 14px;
        left: 0;
        width: 100%;
        height: 220px;
        z-index: 2;
        pointer-events: none;
        transition: transform 0.1s linear;
    }

    .astronaut-track {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

        .astronaut-track.astro-active {
            animation: astro-cycle 28s ease-in-out infinite;
        }

    .astronaut {
        position: absolute;
        bottom: 0;
        left: 0;
    }

    @keyframes astro-cycle {
        0% {
            transform: translate(0%, 0px);
        }

        26% {
            transform: translate(78%, 0px);
        }

        29% {
            transform: translate(78%, 0px) scale(1.05);
        }

        40% {
            transform: translate(82%, -170px) scale(0.85);
        }

        48% {
            transform: translate(82%, -170px) scale(0.85);
        }

        59% {
            transform: translate(78%, 0px) scale(1.05);
        }

        62% {
            transform: translate(78%, 0px);
        }

        88% {
            transform: translate(0%, 0px);
        }

        91% {
            transform: translate(0%, 0px) scale(1.05);
        }

        96% {
            transform: translate(-4%, -170px) scale(0.85);
        }

        99% {
            transform: translate(-4%, -170px) scale(0.85);
        }

        100% {
            transform: translate(0%, 0px) scale(1.05);
        }
    }

    .astro-hop {
        transform-origin: 50px 62px;
    }

    .astronaut-track.astro-active .astro-hop {
        animation: astro-hop-bounce 28s ease-in-out infinite;
    }

    @keyframes astro-hop-bounce {
        0%, 24% {
            transform: translateY(0) rotate(0deg);
        }

        6%, 18% {
            transform: translateY(-10px) rotate(-3deg);
        }

        12% {
            transform: translateY(0) rotate(3deg);
        }

        26%, 62% {
            transform: translateY(0) rotate(0deg);
        }

        66%, 86% {
            transform: translateY(-10px) rotate(3deg);
        }

        76% {
            transform: translateY(0) rotate(-3deg);
        }

        88%, 100% {
            transform: translateY(0) rotate(0deg);
        }
    }

    .astronaut-track.astro-active .astro-flame {
        animation: flame-flicker 28s ease-in-out infinite;
    }

    @keyframes flame-flicker {
        0%, 27% {
            opacity: 0;
        }

        29%, 59% {
            opacity: 1;
        }

        62%, 90% {
            opacity: 0;
        }

        92%, 100% {
            opacity: 1;
        }
    }

    .astronaut-track.astro-active .astro-shadow {
        animation: shadow-fade 28s ease-in-out infinite;
    }

    @keyframes shadow-fade {
        0%, 27% {
            opacity: 0.2;
        }

        29%, 59% {
            opacity: 0.05;
        }

        62%, 90% {
            opacity: 0.2;
        }

        92%, 100% {
            opacity: 0.05;
        }
    }
   
</style>

<style scoped>
    .pinned-features {
        display: grid;
        grid-template-columns: 0.85fr 1.15fr;
        gap: 4rem;
        margin-top: 4rem;
    }

    .pinned-visual {
        position: sticky;
        top: 110px;
        height: calc(100vh - 220px);
        max-height: 480px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 24px;
        overflow: hidden;
        background: radial-gradient(circle at center, rgba(124, 58, 237, 0.16) 0%, rgba(10, 6, 22, 0.65) 80%);
        border: 1px solid rgba(192, 132, 252, 0.25);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .pinned-visual-backdrop {
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.3) 0%, transparent 70%);
        filter: blur(35px);
        pointer-events: none;
    }

    .pinned-visual-images {
        position: relative;
        width: clamp(160px, 45%, 260px);
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pinned-visual-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        opacity: 0;
        transform: scale(0.82) rotate(-8deg) translateY(14px);
        transition: opacity 0.65s cubic-bezier(0.19, 1, 0.22, 1), transform 0.65s cubic-bezier(0.19, 1, 0.22, 1), filter 0.65s ease;
        filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 20px rgba(168, 85, 247, 0.3));
        pointer-events: none;
    }

        .pinned-visual-img.is-active {
            opacity: 1;
            transform: scale(1) rotate(0deg) translateY(0);
            filter: drop-shadow(0 20px 35px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 30px rgba(192, 132, 252, 0.45));
        }

    .pinned-visual-counter {
        position: absolute;
        bottom: 20px;
        left: 20px;
        font-size: 0.85rem;
        letter-spacing: 2px;
        color: var(--td-body-text);
        opacity: 0.7;
    }

    .feature-steps-wrapper {
        position: relative;
        width: 100%;
    }

    .feature-steps {
        display: flex;
        flex-direction: column;
    }

    .feature-step {
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2rem 0;
        opacity: 0.3;
        transform: translateY(8px);
        transition: opacity 0.5s cubic-bezier(0.19, 1, 0.22, 1), transform 0.5s cubic-bezier(0.19, 1, 0.22, 1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

        .feature-step:last-child {
            border-bottom: none;
        }

        .feature-step.is-active {
            opacity: 1;
            transform: translateY(0);
        }

    .feature-step-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .feature-step-index {
        font-size: 0.85rem;
        color: var(--td-accent);
        letter-spacing: 2px;
        display: block;
    }

    .feature-step-mobile-icon-box {
        display: none;
    }

    .feature-step-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-heading);
        margin-bottom: 0.75rem;
    }

    .feature-step-desc {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--td-body-text);
        max-width: 480px;
    }

    .feature-slider-dots {
        display: none;
    }

    @media (max-width: 900px) {
        .pinned-features {
            display: block;
            margin-top: 2.5rem;
        }

        .pinned-visual {
            display: none;
        }

        .feature-steps {
            flex-direction: row;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            gap: 1.25rem;
            padding: 0.5rem 0.25rem 1.25rem 0.25rem;
            scrollbar-width: none;
        }

            .feature-steps::-webkit-scrollbar {
                display: none;
            }

        .feature-step {
            flex: 0 0 85%;
            min-height: auto;
            opacity: 1;
            transform: none;
            scroll-snap-align: center;
            scroll-snap-stop: always;
            padding: 1.85rem 1.6rem;
            border-radius: 20px;
            background: var(--td-card-bg, rgba(255, 255, 255, 0.03));
            border: 1px solid var(--td-card-border, rgba(255, 255, 255, 0.08));
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.35s ease;
        }

            .feature-step.is-active {
                border-color: rgba(192, 132, 252, 0.45);
                background: rgba(124, 58, 237, 0.09);
                box-shadow: 0 10px 28px rgba(124, 58, 237, 0.16);
            }

        .feature-step-mobile-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(124, 58, 237, 0.15);
            border: 1px solid rgba(192, 132, 252, 0.25);
            padding: 6px;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.2);
            flex-shrink: 0;
        }

        .feature-step-mobile-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
        }

        .feature-slider-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 1.25rem;
        }

        .feature-slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            padding: 0;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.19, 1, 0.22, 1);
        }

            .feature-slider-dot.is-active {
                width: 26px;
                background: var(--td-accent);
                box-shadow: 0 0 10px rgba(168, 85, 247, 0.6);
            }
    }

    @media (max-width: 560px) {
        .feature-step {
            flex: 0 0 88%;
            padding: 1.5rem 1.25rem;
            border-radius: 16px;
        }

        .feature-step-mobile-icon-box {
            width: 44px;
            height: 44px;
            padding: 4px;
        }

        .feature-step-title {
            font-size: 1.35rem;
            margin-bottom: 0.5rem;
        }

        .feature-step-desc {
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .feature-step-index {
            font-size: 0.75rem;
        }
    }
</style>