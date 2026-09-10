<template>
    <section class="demo-section">
        <DeepFieldBackground />
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Our Work</span>
                <h2 class="section-title">See what two devs can build</h2>
                <p class="section-desc">
                    Every project below is live and running. Click through to see it in action.
                </p>
            </div>

            <TransitionGroup name="demo-fade" tag="div" class="demo-grid">
                <a v-for="(project, i) in projects"
                   :key="project.name"
                   :href="project.url"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="demo-card"
                   :style="{ transitionDelay: (i * 0.1) + 's' }">
                    <div class="demo-preview-wrap">
                        <img :src="project.images && project.images[0]"
                             class="demo-preview-main"
                             loading="lazy"
                             alt="" />

                        <div class="demo-preview-overlay">
                            <span class="visit-label">Visit site →</span>
                        </div>
                    </div>

                    <div v-if="project.images && project.images.length > 1"
                         class="demo-popup-collage"
                         :class="'collage-' + Math.min(project.images.length, 4)">
                        <img v-for="(img, idx) in project.images.slice(0, 4)"
                             :key="idx"
                             :src="img"
                             alt="" />
                    </div>
                    <div class="demo-info">
                        <h3 class="demo-name">{{ project.name }}</h3>
                        <p class="demo-desc">{{ project.desc }}</p>
                        <div class="demo-tags">
                            <span v-for="tag in project.tags" :key="tag" class="demo-tag">{{ tag }}</span>
                        </div>
                    </div>
                </a>
            </TransitionGroup>

            <p class="demo-note">More projects coming soon — we're just getting started.</p>

            <div class="demo-cta">
                <h3 class="demo-cta-title">Like what you see?</h3>
                <p class="demo-cta-text">Let's build something just as bold for you.</p>
                <router-link to="/contact" class="btn btn-primary btn-large">Start Your Project</router-link>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import DeepFieldBackground from '../components/DeepFieldBackground.vue'

    // Fetched from api/projects.php instead of hardcoded here.
    // Change this if your API is deployed at a different path.
    const API_URL = '/api/projects.php'

    // Local-only test card so you can see the popup collage without the
    // hosted JSON — only appears when running `npm run dev`, never in a
    // production build. Swap the image URLs for real ones any time, or
    // delete this block once you don't need it anymore.
    const hardcodedProject = {
        name: 'Local Preview',
        desc: 'Hardcoded card for testing the hover popup locally.',
        url: 'https://twodazzle.com',
        tags: ['Test'],
        images: [
            'https://picsum.photos/seed/tdmain/640/400',
            'https://picsum.photos/seed/tdtwo/640/400',
            'https://picsum.photos/seed/tdthree/640/400'
        ]
    }

    const projects = ref(import.meta.env.DEV ? [hardcodedProject] : [])

    onMounted(async () => {
        try {
            const res = await fetch(API_URL)
            const data = await res.json()
            if (Array.isArray(data)) {
                projects.value = import.meta.env.DEV ? [hardcodedProject, ...data] : data
            }
        } catch (err) {
            console.error('Failed to load projects:', err)
        }
    })
</script>

<style scoped>
    .demo-section {
        position: relative;
        margin-top: -75px;
        padding: calc(6rem + 75px) 0 6rem 0;
        background-color: var(--color-background-soft);
        overflow: hidden;
    }

        .demo-section .container {
            position: relative;
            z-index: 1;
        }

    .demo-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        margin-top: 3rem;
    }

    @media (max-width: 768px) {
        .demo-grid {
            grid-template-columns: 1fr;
        }
    }

    .demo-card {
        display: block;
        text-decoration: none;
        color: inherit;
        background: var(--td-card-bg);
        border: 1px solid var(--td-card-border);
        border-radius: 14px;
        overflow: visible;
        position: relative;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

        .demo-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(124, 58, 237, 0.2);
            border-color: var(--td-accent);
        }

    .demo-preview-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #0A0417;
        border-radius: 14px 14px 0 0;
    }

    .demo-preview-main {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.6s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .demo-card:hover .demo-preview-main {
        transform: scale(1.05);
    }

    .demo-preview-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(10, 4, 23, 0);
        transition: background 0.25s ease;
        z-index: 1;
    }

    .demo-card:hover .demo-preview-overlay {
        background: rgba(10, 4, 23, 0.4);
    }

    /* Popup collage — floats above the card on hover, showing all photos.
       Swap grid-template-columns/rows here to change the layout per count. */
    .demo-popup-collage {
        position: absolute;
        top: -18px;
        left: -18px;
        right: -18px;
        aspect-ratio: 16 / 10;
        display: grid;
        gap: 3px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 0.25s cubic-bezier(0.19, 1, 0.22, 1), transform 0.25s cubic-bezier(0.19, 1, 0.22, 1);
        pointer-events: none;
        z-index: 5;
    }

    .demo-card:hover .demo-popup-collage {
        opacity: 1;
        transform: scale(1);
    }

    .demo-popup-collage img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* 2 photos: side by side */
    .collage-2 {
        grid-template-columns: 1fr 1fr;
    }

    /* 3 photos: one big on the left, two stacked on the right */
    .collage-3 {
        grid-template-columns: 2fr 1fr;
        grid-template-rows: 1fr 1fr;
    }

        .collage-3 img:first-child {
            grid-row: 1 / 3;
        }

    /* 4 photos: 2x2 grid */
    .collage-4 {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }

    .visit-label {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.25s ease, transform 0.25s ease;
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        background: var(--td-primary);
        padding: 0.6rem 1.2rem;
        border-radius: 9999px;
    }

    .demo-card:hover .visit-label {
        opacity: 1;
        transform: translateY(0);
    }

    .demo-info {
        padding: 1.5rem;
    }

    .demo-name {
        font-size: 1.15rem;
        margin-bottom: 0.5rem;
    }

    .demo-desc {
        font-size: 0.9rem;
        opacity: 0.75;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .demo-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .demo-tag {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--td-accent);
        background: rgba(124, 58, 237, 0.12);
        border: 1px solid rgba(192, 132, 252, 0.25);
        padding: 0.25rem 0.7rem;
        border-radius: 9999px;
    }

    .demo-fade-enter-active {
        transition: opacity 0.6s cubic-bezier(0.19, 1, 0.22, 1), transform 0.6s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .demo-fade-enter-from {
        opacity: 0;
        transform: translateY(28px) scale(0.97);
    }

    .demo-fade-move {
        transition: transform 0.5s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .demo-note {
        text-align: center;
        font-size: 0.9rem;
        opacity: 0.55;
        margin-top: 2.5rem;
    }

    .demo-cta {
        text-align: center;
        margin-top: 5rem;
        padding-top: 3rem;
        border-top: 1px solid var(--td-card-border);
    }

    .demo-cta-title {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .demo-cta-text {
        font-size: 1rem;
        opacity: 0.75;
        margin-bottom: 1.75rem;
    }
</style>
