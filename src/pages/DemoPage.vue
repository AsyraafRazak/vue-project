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

            <div class="demo-grid">
                <a v-for="project in projects"
                   :key="project.name"
                   :href="project.url"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="demo-card">
                    <div class="demo-preview-wrap">
                        <iframe :src="project.url"
                                class="demo-preview-iframe"
                                loading="lazy"
                                tabindex="-1"
                                scrolling="no"></iframe>
                        <div class="demo-preview-overlay">
                            <span class="visit-label">Visit site →</span>
                        </div>
                    </div>
                    <div class="demo-info">
                        <h3 class="demo-name">{{ project.name }}</h3>
                        <p class="demo-desc">{{ project.desc }}</p>
                        <div class="demo-tags">
                            <span v-for="tag in project.tags" :key="tag" class="demo-tag">{{ tag }}</span>
                        </div>
                    </div>
                </a>
            </div>

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
    import DeepFieldBackground from '../components/DeepFieldBackground.vue'

    const projects = [
        {
            name: 'TwoDazzle',
            desc: 'Our own studio site — galaxy-themed portfolio built with Vue and Three.js.',
            url: 'https://twodazzle.com',
            tags: ['Vue', 'Three.js', 'Design']
        },
        {
            name: 'TwoDazzle',
            desc: 'Our own studio site — galaxy-themed portfolio built with Vue and Three.js.',
            url: 'https://twodazzle.com',
            tags: ['Vue', 'Three.js', 'Design']

        }
        // Add more projects here as you build them
    ]
</script>

<style scoped>
    .demo-section {
        position: relative;
        padding: 6rem 0;
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
        overflow: hidden;
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
    }

    .demo-preview-iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 250%;
        height: 250%;
        transform: scale(0.4);
        transform-origin: top left;
        border: none;
        pointer-events: none;
    }

    .demo-preview-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(10, 4, 23, 0);
        transition: background 0.25s ease;
    }

    .demo-card:hover .demo-preview-overlay {
        background: rgba(10, 4, 23, 0.55);
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