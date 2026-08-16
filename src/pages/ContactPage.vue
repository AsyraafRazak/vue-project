<template>
    <section class="contact-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Get in touch</span>
                <h2 class="section-title">Let's talk about your project</h2>
                <p class="section-desc">
                    Tell us a bit about what you're building — we usually reply within a day.
                </p>
            </div>

            <div class="contact-grid">
                <form class="contact-form" @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" v-model="form.name" type="text" placeholder="Your name" required />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" v-model="form.email" type="email" placeholder="you@example.com" required />
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" v-model="form.message" rows="6" placeholder="Tell us about your project..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large" :disabled="submitted">
                        {{ submitted ? 'Message sent' : 'Send message' }}
                    </button>

                    <p v-if="submitted" class="form-success">
                        Thanks for reaching out — we'll get back to you soon.
                    </p>
                </form>

                <div class="contact-info">
                    <div class="info-card">
                        <h3>Email us</h3>
                        <a href="mailto:hello@twodazzle.com">hello@twodazzle.com</a>
                    </div>
                    <div class="info-card">
                        <h3>Response time</h3>
                        <p>Usually within 24 hours</p>
                    </div>
                    <div class="info-card">
                        <h3>Based in</h3>
                        <p>Klang, Selangor, Malaysia</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  message: ''
})

const submitted = ref(false)

function handleSubmit() {
  // TODO: wire this up to an actual email service (e.g. Formspree, EmailJS, or your own backend)
  console.log('Form submitted:', form.value)
  submitted.value = true
}
</script>

<style scoped>
    .contact-section {
        padding: 6rem 0;
        background-color: var(--color-background-soft);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 3rem;
        margin-top: 3rem;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0.85;
        }

        .form-group input,
        .form-group textarea {
            background: rgba(124, 58, 237, 0.06);
            border: 1px solid var(--td-border-outline);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            color: inherit;
            font-family: inherit;
            resize: vertical;
        }

            .form-group input:focus,
            .form-group textarea:focus {
                outline: none;
                border-color: var(--td-accent);
            }

    .form-success {
        font-size: 0.9rem;
        color: var(--td-accent);
    }

    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .info-card {
        background: var(--td-card-bg);
        border: 1px solid var(--td-card-border);
        border-radius: 12px;
        padding: 1.5rem;
    }

        .info-card h3 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .info-card a,
        .info-card p {
            font-size: 0.95rem;
            opacity: 0.85;
            color: inherit;
            text-decoration: none;
        }

            .info-card a:hover {
                color: var(--td-accent);
            }
</style>