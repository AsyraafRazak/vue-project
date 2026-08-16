<script setup>
import { ref } from 'vue'

const email = ref('')
const isSubscribed = ref(false)

const handleSubscribe = () => {
  if (email.value.trim()) {
    isSubscribed.value = true
    email.value = ''
    setTimeout(() => {
      isSubscribed.value = false
    }, 4000)
  }
}
</script>

<template>
  <footer class="footer">
    <div class="footer-container">
      <!-- Grid Layout -->
      <div class="footer-grid">
        <!-- Col 1: Branding & Intro -->
        <div class="footer-brand">
          <a href="#" class="logo">
            <span class="logo-dot"></span>
            <span class="logo-text">SaaS<span>Flow</span></span>
          </a>
          <p class="brand-desc">
            Build beautiful interfaces and automate your workflow with our next-generation cloud services. Designed for modern tech teams.
          </p>
          <div class="social-links">
            <a href="#" aria-label="Twitter" class="social-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
            </a>
            <a href="#" aria-label="GitHub" class="social-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/></svg>
            </a>
            <a href="#" aria-label="LinkedIn" class="social-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
          </div>
        </div>

        <!-- Col 2: Product Links -->
        <div class="footer-links-col">
          <h3 class="footer-title">Product</h3>
          <ul class="footer-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#pricing">Pricing</a></li>
            <li><a href="#integrations">Integrations</a></li>
            <li><a href="#changelog">Changelog</a></li>
          </ul>
        </div>

        <!-- Col 3: Company / Resource Links -->
        <div class="footer-links-col">
          <h3 class="footer-title">Resources</h3>
          <ul class="footer-links">
            <li><a href="#blog">Blog</a></li>
            <li><a href="#docs">Documentation</a></li>
            <li><a href="#support">Help Center</a></li>
            <li><a href="#privacy">Privacy & Terms</a></li>
          </ul>
        </div>

        <!-- Col 4: Newsletter -->
        <div class="footer-newsletter">
          <h3 class="footer-title">Stay Updated</h3>
          <p class="newsletter-desc">Subscribe to our monthly newsletter for engineering updates, new features, and strategies.</p>
          <form class="newsletter-form" @submit.prevent="handleSubscribe">
            <div class="input-group">
              <input 
                type="email" 
                v-model="email" 
                placeholder="Enter your email" 
                required
                aria-label="Email address"
              />
              <button type="submit" class="btn btn-primary btn-newsletter">
                Subscribe
              </button>
            </div>
            <transition name="fade">
              <span v-if="isSubscribed" class="success-message">
                🎉 Thanks for subscribing!
              </span>
            </transition>
          </form>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="footer-bottom">
        <p>&copy; {{ new Date().getFullYear() }} SaaSFlow Inc. All rights reserved.</p>
        <div class="footer-bottom-links">
          <a href="#privacy">Privacy Policy</a>
          <a href="#terms">Terms of Service</a>
          <a href="#cookies">Cookie Settings</a>
        </div>
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
  grid-template-columns: 2fr 1fr 1fr 2fr;
  gap: 4rem;
  margin-bottom: 4rem;
}

@media (max-width: 968px) {
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
  background: linear-gradient(135deg, #10b981, #059669);
  display: inline-block;
}

.logo-text span {
  color: #10b981;
}

.brand-desc {
  font-size: 0.95rem;
  line-height: 1.6;
  color: var(--color-text);
  opacity: 0.8;
}

.social-links {
  display: flex;
  gap: 1rem;
}

.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background-color: var(--color-background-mute);
  color: var(--color-text);
  transition: all 0.25s ease;
  border: 1px solid var(--color-border);
}

.social-icon:hover {
  background-color: #10b981;
  color: white;
  border-color: #10b981;
  transform: translateY(-2px);
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
  color: #10b981;
  padding-left: 4px;
}

/* Newsletter Input & Form styling */
.footer-newsletter {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.newsletter-desc {
  font-size: 0.95rem;
  line-height: 1.6;
  color: var(--color-text);
  opacity: 0.8;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input-group {
  display: flex;
  gap: 0.5rem;
}

.input-group input {
  flex: 1;
  padding: 0.75rem 1rem;
  border-radius: 9999px;
  border: 1px solid var(--color-border);
  background-color: var(--color-background);
  color: var(--color-text);
  font-size: 0.9rem;
  outline: none;
  transition: all 0.2s ease;
}

.input-group input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15);
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
}

.btn-primary {
  background-color: #10b981;
  color: white;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
}

.btn-primary:hover {
  background-color: #059669;
}

.btn-newsletter {
  padding: 0 1.5rem;
  white-space: nowrap;
}

.success-message {
  font-size: 0.85rem;
  color: #10b981;
  font-weight: 500;
  margin-top: 0.25rem;
}

/* Bottom Bar Copyrights styling */
.footer-bottom {
  border-top: 1px solid var(--color-border);
  padding-top: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
  color: var(--color-text);
  opacity: 0.7;
  flex-wrap: wrap;
  gap: 1rem;
}

@media (max-width: 768px) {
  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }
}

.footer-bottom-links {
  display: flex;
  gap: 1.5rem;
}

.footer-bottom-links a {
  color: var(--color-text);
  transition: color 0.2s;
}

.footer-bottom-links a:hover {
  color: #10b981;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
