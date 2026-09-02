<script setup>
</script>

<template>
    <footer class="footer">
        <div class="footer-container">
            <!-- Grid Layout -->
            <div class="footer-grid">
                <!-- Col 1: Branding & Intro -->
                <div class="footer-brand">
                    <router-link to="/" class="logo">
                        <span class="logo-dot"></span>
                        <span class="logo-text">Two<span>Dazzle</span></span>
                    </router-link>
                    <p class="brand-desc">
                        Two developers designing and building custom websites for small
                        businesses across Malaysia — from simple landing pages to fully
                        interactive builds.
                    </p>
                </div>

                <!-- Col 2: Site Links -->
                <div class="footer-links-col">
                    <h3 class="footer-title">Site</h3>
                    <ul class="footer-links">
                        <li><router-link to="/">Home</router-link></li>
                        <li><router-link to="/demo">Demo</router-link></li>
                        <li><router-link to="/pricing">Pricing</router-link></li>
                        <li><router-link to="/contact">Contact</router-link></li>
                    </ul>
                </div>

                <!-- Col 3: Get in Touch -->
                <div class="footer-links-col">
                    <h3 class="footer-title">Get in touch</h3>
                    <ul class="footer-links">
                        <li><a href="mailto:hello@twodazzle.com">hello@twodazzle.com</a></li>
                        <li class="footer-text">Klang, Selangor, Malaysia</li>
                        <li class="footer-text">Usually replies within 24 hours</li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p>&copy; {{ new Date().getFullYear() }} TwoDazzle. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
    .footer {
        background-color: var(--color-background-soft);
        border-top: 1px solid var(--color-border);
        padding: 5rem 0 2.5rem 0;
        transition: background-color 0.5s, border-color 0.5s;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 4rem;
        margin-bottom: 4rem;
    }

    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
    }

    @media (max-width: 576px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }
    }

    /* Brand Details */
    .footer-brand {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--color-heading);
        letter-spacing: -0.5px;
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

    .brand-desc {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--color-text);
        opacity: 0.8;
    }

    /* Footer Link Columns */
    .footer-links-col {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .footer-title {
        font-size: 0.95rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--color-heading);
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

        .footer-links a {
            font-size: 0.95rem;
            color: var(--color-text);
            opacity: 0.8;
            transition: all 0.2s ease;
        }

            .footer-links a:hover {
                opacity: 1;
                color: var(--td-accent);
                padding-left: 4px;
            }

    .footer-text {
        font-size: 0.95rem;
        color: var(--color-text);
        opacity: 0.8;
    }

    /* Bottom Bar Copyrights styling */
    .footer-bottom {
        border-top: 1px solid var(--color-border);
        padding-top: 2rem;
        font-size: 0.85rem;
        color: var(--color-text);
        opacity: 0.7;
        text-align: center;
    }
</style>