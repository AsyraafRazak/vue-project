<script setup>
    import { ref, onMounted, onUnmounted } from 'vue'
    import { useRouter } from 'vue-router'
    import RocketScene from './Rocketscene.vue'

    const router = useRouter()

    const isMenuOpen = ref(false)
    const isScrolled = ref(false)
    const rocketClicks = ref(0)
    let rocketClickTimer = null

    const EASTER_EGG_THRESHOLD = 5

    const toggleMenu = () => {
        isMenuOpen.value = !isMenuOpen.value
    }

    const closeMenu = () => {
        isMenuOpen.value = false
    }

    const handleScroll = () => {
        isScrolled.value = window.scrollY > 20
    }

    const handleRocketClick = (event) => {
        // Intercepts clicks on the rocket itself so we can count them
        // before letting the logo's normal "go home" link take over.
        event.preventDefault()
        event.stopPropagation()

        rocketClicks.value++
        clearTimeout(rocketClickTimer)
        rocketClickTimer = setTimeout(() => {
            rocketClicks.value = 0
        }, 1500)

        if (rocketClicks.value >= EASTER_EGG_THRESHOLD) {
            rocketClicks.value = 0
            closeMenu()
            router.push('/play')
        } else {
            router.push('/')
        }
    }

    onMounted(() => {
        window.addEventListener('scroll', handleScroll)
    })

    onUnmounted(() => {
        window.removeEventListener('scroll', handleScroll)
        clearTimeout(rocketClickTimer)
    })
</script>

<template>
    <nav :class="['navbar', { 'navbar-scrolled': isScrolled, 'menu-open': isMenuOpen }]">
        <div class="nav-container">
            <!-- Logo -->
            <router-link to="/" class="logo" @click="closeMenu">
                <span class="logo-rocket"
                      :class="{ 'is-charging': rocketClicks >= 3 }"
                      @click="handleRocketClick"
                      title="🚀">
                    <RocketScene />
                </span>
                <span class="logo-text">Two<span>Dazzle</span></span>
            </router-link>

            <!-- Desktop Nav Links -->
            <ul class="nav-links">
                <li><a href="/#features" class="nav-link">Features</a></li>
                <li><router-link to="/demo" class="nav-link">Demo</router-link></li>
                <li><router-link to="/pricing" class="nav-link">Pricing</router-link></li>
            </ul>

            <!-- Desktop CTA Button -->
            <div class="nav-cta">
                <router-link to="/contact" class="btn btn-primary">Contact Us</router-link>
            </div>

            <!-- Hamburger Menu Button -->
            <button class="menu-toggle" @click="toggleMenu" :aria-expanded="isMenuOpen" aria-label="Toggle navigation menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>

        <!-- Mobile Navigation Menu Drawer -->
        <transition name="slide">
            <div v-if="isMenuOpen" class="mobile-drawer">
                <ul class="mobile-nav-links">
                    <li><a href="/#features" class="mobile-nav-link" @click="closeMenu">Features</a></li>
                    <li><router-link to="/demo" class="mobile-nav-link" @click="closeMenu">Demo</router-link></li>
                    <li><router-link to="/pricing" class="mobile-nav-link" @click="closeMenu">Pricing</router-link></li>

                    <li class="mobile-cta-li">
                        <router-link to="/contact" class="btn btn-primary mobile-cta" @click="closeMenu">Contact Us</router-link>
                    </li>
                </ul>
            </div>
        </transition>
    </nav>
</template>

<style scoped>
    /* Navbar Container styling */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        padding: 0;
        transition: padding 0.45s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .navbar-scrolled {
        padding: 14px 20px 0;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.25rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(10, 4, 23, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 0px;
        box-shadow: none;
        transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .navbar-scrolled .nav-container {
        padding: 0.7rem 1.75rem;
        background-color: rgba(10, 4, 23, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 9999px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--color-heading);
        letter-spacing: -0.5px;
        text-decoration: none;
    }

    .logo-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--td-primary), var(--td-accent));
        display: inline-block;
    }

    .logo-text span {
        color: var(--td-accent);
    }

    .logo-rocket {
        position: relative;
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 50%;
        cursor: pointer;
        transition: box-shadow 0.25s ease;
    }

        .logo-rocket.is-charging {
            animation: rocket-charge 0.6s ease-in-out infinite;
        }

    @keyframes rocket-charge {
        0%, 100% {
            box-shadow: 0 0 0 rgba(255, 211, 77, 0);
        }

        50% {
            box-shadow: 0 0 10px 2px rgba(255, 211, 77, 0.55);
        }
    }

    .nav-links {
        display: flex;
        list-style: none;
        gap: 2.25rem;
        margin: 0;
        padding: 0;
        align-items: center;
    }

    .nav-link {
        color: var(--td-body-text);
        font-weight: 500;
        font-size: 0.95rem;
        position: relative;
        opacity: 0.85;
        padding: 0.25rem 0;
        text-decoration: none;
    }

        .nav-link:hover {
            opacity: 1;
            color: var(--td-accent);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 100%;
            transform: scaleX(0);
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--td-accent);
            transform-origin: bottom right;
            transition: transform 0.25s ease-out;
        }

        .nav-link:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.65rem 1.4rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 9999px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--td-primary);
        color: white;
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
    }

        .btn-primary:hover {
            background-color: var(--td-accent);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(192, 132, 252, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

    .menu-toggle {
        display: none;
        flex-direction: column;
        justify-content: space-between;
        width: 24px;
        height: 18px;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        z-index: 1001;
    }

        .menu-toggle .bar {
            width: 100%;
            height: 2px;
            background-color: var(--color-heading);
            border-radius: 4px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

    .menu-open .menu-toggle .bar:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }

    .menu-open .menu-toggle .bar:nth-child(2) {
        opacity: 0;
    }

    .menu-open .menu-toggle .bar:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    .mobile-drawer {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: var(--color-background);
        z-index: 999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .mobile-nav-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2rem;
    }

    .mobile-nav-link {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-heading);
        transition: color 0.2s;
        text-decoration: none;
    }

        .mobile-nav-link:hover {
            color: var(--td-accent);
        }

    .mobile-cta-li {
        margin-top: 1.5rem;
        width: 100%;
    }

    .mobile-cta {
        width: 100%;
        max-width: 280px;
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .nav-links,
        .nav-cta {
            display: none;
        }

        .menu-toggle {
            display: flex;
        }
    }

    .slide-enter-active,
    .slide-leave-active {
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s;
    }

    .slide-enter-from,
    .slide-leave-to {
        transform: translateY(-100%);
        opacity: 0;
    }
</style>