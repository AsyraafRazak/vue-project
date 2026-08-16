<script setup>
    import NavBar from './components/NavBar.vue'
    import FooterBar from './components/FooterBar.vue'
    import SparkleCursor from './components/SparkleCursor.vue'
    import ThreeBackground from './components/ThreeBackground.vue'
</script>

<template>
    <div class="app-layout">
        <SparkleCursor />
        <!-- Navigation Bar -->
        <NavBar />

        <!-- Main Content Area -->
        <main class="main-content">

            <!-- Hero Section -->
            <section id="hero" class="hero-section">
                <!-- Background video -->
                <video class="hero-bg-video" autoplay muted loop playsinline>
                    <source src="https://twodazzle.com/wp-content/uploads/2026/08/copy_F1BD6054-509C-417B-9A22-BBB84A59D313-4.mov" type="video/mp4" />
                </video>
                <div class="hero-overlay"></div>

                <!-- Decorative star dots -->
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

                    <!-- Mock Interactive UI Dashboard Showcase -->
                    <div class="hero-showcase">

                        <!-- Back card (offset, smaller, dimmer) -->
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

                        <!-- Front card (main, your original one) -->
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

                    <div class="features-grid">
                        <!-- Feature 1: Design -->
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                </svg>
                            </div>
                            <h3 class="feature-name">Thoughtful Design</h3>
                            <p class="feature-desc">
                                Clean, modern interfaces built around your brand — not generic templates.
                                Every layout designed with purpose.
                            </p>
                        </div>

                        <!-- Feature 2: Development -->
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <polyline points="16 18 22 12 16 6" />
                                    <polyline points="8 6 2 12 8 18" />
                                </svg>
                            </div>
                            <h3 class="feature-name">Solid Development</h3>
                            <p class="feature-desc">
                                Fast, reliable builds using modern tools — from simple landing pages
                                to fully custom interactive experiences.
                            </p>
                        </div>

                        <!-- Feature 3: Support -->
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                </svg>
                            </div>
                            <h3 class="feature-name">Ongoing Support</h3>
                            <p class="feature-desc">
                                Sites evolve. We stick around after launch for updates, fixes,
                                and improvements — no disappearing after handoff.
                            </p>
                        </div>

                        <!-- Feature 4: Interactive/3D -->
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                                    <line x1="12" y1="22.08" x2="12" y2="12" />
                                </svg>
                            </div>
                            <h3 class="feature-name">Interactive Experiences</h3>
                            <p class="feature-desc">
                                3D visuals, animations, and interactive elements that make your
                                site feel alive — not just another static page.
                            </p>
                        </div>

                        <!-- Feature 5: Responsive -->
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                                    <line x1="12" y1="18" x2="12.01" y2="18" />
                                </svg>
                            </div>
                            <h3 class="feature-name">Fully Responsive</h3>
                            <p class="feature-desc">
                                Every site works flawlessly across desktop, tablet, and mobile —
                                tested and polished on every screen size.
                            </p>
                        </div>

                        <!-- Feature 6: Brand Identity -->
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5" />
                                    <circle cx="7.5" cy="10.5" r="0.5" />
                                    <circle cx="12" cy="7.5" r="0.5" />
                                    <circle cx="16.5" cy="10.5" r="0.5" />
                                </svg>
                            </div>
                            <h3 class="feature-name">Brand Identity</h3>
                            <p class="feature-desc">
                                Color palettes, typography, and visual direction that make your
                                brand instantly recognizable and consistent.
                            </p>
                        </div>
                    </div>

                    <div class="features-cta">
                        <button class="btn-demo" disabled>See Our Demo</button>
                    </div>
                </div>
            </section>

            <!-- Benefits / Call to Action Banner -->
            <section id="benefits" class="cta-banner-section">
                <div class="container">
                    <div class="cta-banner">
                        <h2 class="cta-banner-title">Ready to bring your vision to life?</h2>
                        <p class="cta-banner-text">
                            Two devs, one focus — your project. Let's build something that actually stands out.
                        </p>
                        <div class="cta-banner-actions">
                            <a href="#signup" class="btn btn-light">Get Started </a>
                            <a href="#docs" class="btn btn-outline-light">View Our Work</a>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- Footer Component -->
        <FooterBar />
    </div>
</template>

<style>
    /* Layout wrap */
    .app-layout {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding-top: 75px;
        /* Navbar height compensation */
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
        padding: 5rem 0 6rem 0;
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
        overflow: hidden;
    }

        .features-section .container {
            position: relative;
            z-index: 1;
        }
</style>