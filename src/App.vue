<script setup>
import NavBar from './components/NavBar.vue'
import FooterBar from './components/FooterBar.vue'
</script>

<template>
  <div class="app-layout">
    <!-- Navigation Bar -->
    <NavBar />

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Hero Section -->
      <section id="hero" class="hero-section">
        <div class="container">
          <div class="hero-content">
            <span class="badge">Introducing SaaSFlow 2.0</span>
            <h1 class="hero-title">
              Work smarter. <br />
              <span>Flow faster.</span>
            </h1>
            <p class="hero-lead">
              The all-in-one developer automation platform. Design, deploy, and scale your application workflows in record time with zero infrastructure headaches.
            </p>
            <div class="hero-actions">
              <a href="#signup" class="btn btn-primary btn-large">Start Free Trial</a>
              <a href="#features" class="btn btn-secondary btn-large">
                Explore Features
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
            <div class="hero-social-proof">
              <p>Trusted by creators at forward-thinking companies</p>
              <div class="brand-logos">
                <span class="brand-logo">Vercel</span>
                <span class="brand-logo">Stripe</span>
                <span class="brand-logo">Figma</span>
                <span class="brand-logo">Airbnb</span>
              </div>
            </div>
          </div>
          
          <!-- Mock Interactive UI Dashboard Showcase -->
          <div class="hero-showcase">
            <div class="mock-browser">
              <div class="browser-header">
                <span class="dot red"></span>
                <span class="dot yellow"></span>
                <span class="dot green"></span>
                <span class="browser-address">app.saasflow.io/dashboard</span>
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
        <div class="container">
          <div class="section-header">
            <span class="section-subtitle">Features</span>
            <h2 class="section-title">Everything you need to scale</h2>
            <p class="section-desc">Stop building backend plumbing. SaaSFlow gives you everything you need to power production grade pipelines right out of the box.</p>
          </div>
          
          <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              </div>
              <h3 class="feature-name">Lightning Fast Pipelines</h3>
              <p class="feature-desc">Execute thousands of background workflows concurrently with sub-millisecond response and routing speeds.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <h3 class="feature-name">Bank-Grade Security</h3>
              <p class="feature-desc">All user transactions and database schemas are end-to-end encrypted under AES-256 standard protocols.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card">
              <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
              </div>
              <h3 class="feature-name">Realtime Diagnostics</h3>
              <p class="feature-desc">Monitor pipeline health metrics, latency logs, and memory performance dashboards dynamically in real-time.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Benefits / Call to Action Banner -->
      <section id="benefits" class="cta-banner-section">
        <div class="container">
          <div class="cta-banner">
            <h2 class="cta-banner-title">Ready to optimize your deployment pipeline?</h2>
            <p class="cta-banner-text">Join thousands of software engineers building faster products today. No credit card required.</p>
            <div class="cta-banner-actions">
              <a href="#signup" class="btn btn-light">Get Started Free</a>
              <a href="#docs" class="btn btn-outline-light">Read Documentation</a>
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
  padding-top: 75px; /* Navbar height compensation */
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
h1, h2, h3 {
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
  background-color: #10b981;
  color: white;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
}

.btn-primary:hover {
  background-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
}

.btn-secondary {
  background-color: var(--color-background-soft);
  color: var(--color-heading);
  border: 1px solid var(--color-border);
}

.btn-secondary:hover {
  background-color: var(--color-background-mute);
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
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-outline-light:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-color: white;
  transform: translateY(-2px);
}

/* Badge styling */
.badge {
  display: inline-block;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
  background-color: rgba(16, 185, 129, 0.1);
  color: #10b981;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

/* Hero Section styles */
.hero-section {
  padding: 5rem 0 6rem 0;
  overflow: hidden;
  background: radial-gradient(circle at 100% 0%, rgba(16, 185, 129, 0.05) 0%, transparent 40%);
}

.hero-section .container {
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
  color: #10b981;
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

.dot.red { background-color: #ef4444; }
.dot.yellow { background-color: #f59e0b; }
.dot.green { background-color: #10b981; }

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
  background-color: #10b981;
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

.mock-bar.bar-1 { height: 40%; background-color: #10b981; }
.mock-bar.bar-2 { height: 80%; }
.mock-bar.bar-3 { height: 60%; }
.mock-bar.bar-4 { height: 95%; background-color: #059669; }

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

.mock-line.line-1 { width: 100%; }
.mock-line.line-2 { width: 75%; }
.mock-line.line-3 { width: 90%; }

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
  color: #10b981;
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
  background-color: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 2.25rem;
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.03);
  border-color: rgba(16, 185, 129, 0.3);
}

.feature-icon {
  width: 48px;
  height: 48px;
  background-color: rgba(16, 185, 129, 0.08);
  color: #10b981;
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
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 20px;
  padding: 4.5rem 2rem;
  text-align: center;
  color: white;
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
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
