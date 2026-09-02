<template>
    <section class="pricing" id="pricing">
        <PricingStarfield />
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Pricing</span>
                <h2 class="section-title">Two ways to launch your site</h2>
                <p class="section-desc">
                    Pick a static site if your content rarely changes, or a managed backend
                    if you want to update things yourself. Every quote is scoped to what
                    you actually need - these are starting points, not the ceiling.
                </p>
            </div>

            <div class="pricing__switch" role="tablist">
                <button role="tab"
                        :aria-selected="track === 'static'"
                        :class="['pricing__switchBtn', { 'is-active': track === 'static' }]"
                        @click="track = 'static'">
                    Static sites
                </button>
                <button role="tab"
                        :aria-selected="track === 'backend'"
                        :class="['pricing__switchBtn', { 'is-active': track === 'backend' }]"
                        @click="track = 'backend'">
                    Backend &amp; CMS
                </button>
            </div>

            <p class="pricing__trackNote">
                {{
 track === 'static'
          ? 'Landing pages and brochure sites. You send us content, we handle the build.'
          : 'Sites with an admin panel, so you can add or edit content without calling us.'
                }}
            </p>

            <div class="pricing__grid">
                <article v-for="plan in currentPlans"
                         :key="plan.name"
                         :class="['plan', { 'plan--featured': plan.featured }]">
                    <p class="plan__scope">{{ plan.scope }}</p>
                    <h3 class="plan__name">{{ plan.name }}</h3>
                    <p class="plan__price">
                        <span class="plan__from">from</span> RM{{ plan.from }}
                    </p>
                    <p class="plan__desc">{{ plan.description }}</p>
                    <ul class="plan__list">
                        <li v-for="item in plan.includes" :key="item">{{ item }}</li>
                    </ul>
                    <router-link :to="{ path: '/contact', query: { plan: plan.name, price: plan.from, scope: plan.scope, track: track } }"
                                 class="plan__cta">Get a quote</router-link>
                </article>
            </div>

            <p class="pricing__footnote">
                Prices depend on scope - page count, content you provide, and any custom
                design work. We'll confirm an exact number before anything starts.
            </p>

            <div class="pricing__hostNote">
                <p class="pricing__hostNoteTitle">Domain and hosting aren't included</p>
                <p class="pricing__hostNoteBody">
                    These prices cover design, build, and deployment only. You'll need
                    your own domain (from ~RM30/year) and hosting (from ~RM30/month) -
                    or we can help you set these up if you don't have them yet.
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import PricingStarfield from '../components/PricingStarfield.vue'

    const track = ref('static')

    const staticPlans = [
        {
            name: 'Starter',
            scope: '1 page',
            from: 500,
            description: 'A single scrolling page - everything your visitors need in one place.',
            includes: [
                'Hero, about, and contact sections',
                'Mobile-friendly layout',
                'Contact form',
                '1 round of revisions',
            ],
        },
        {
            name: 'Standard',
            scope: '3-5 pages',
            from: 1000,
            description: 'Separate pages for a small business that wants a proper site.',
            includes: [
                'Up to 5 pages, own navigation',
                'Photo gallery',
                'Contact form with validation',
                '2 rounds of revisions',
            ],
            featured: true,
        },
        {
            name: 'Business',
            scope: '6-8 pages',
            from: 1800,
            description: 'A fuller site with more polish, for businesses ready to stand out.',
            includes: [
                'Up to 8 pages',
                'Custom animations and interactions',
                'Basic SEO setup',
                '2 rounds of revisions',
            ],
        },
    ]

    const backendPlans = [
        {
            name: 'Basic CMS',
            scope: '1 editable section',
            from: 1500,
            description: 'Update one part of your site yourself - no code, no waiting on us.',
            includes: [
                'Admin login',
                'Add, edit, and delete entries',
                'Image uploads',
                'Everything else stays as a static site',
            ],
        },
        {
            name: 'Standard Backend',
            scope: '2-3 editable sections',
            from: 2500,
            description: 'A proper dashboard for a site with more moving parts.',
            includes: [
                'Manage multiple content types',
                'Contact form saved to a database',
                'Search and filter on listings',
            ],
            featured: true,
        },
        {
            name: 'Advanced',
            scope: 'Orders, bookings, or accounts',
            from: 4000,
            description: 'For stores, bookings, or anything customers log into.',
            includes: [
                'Customer accounts and login',
                'Payment gateway integration',
                'Order or booking management',
                'Email notifications',
            ],
        },
    ]

    const currentPlans = computed(() =>
        track.value === 'static' ? staticPlans : backendPlans
    )
</script>

<style scoped>
    .pricing {
        position: relative;
        overflow: hidden;
        background-color: var(--color-background-soft);
        color: var(--td-white);
        padding: 6rem 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

        .pricing .container {
            position: relative;
            z-index: 1;
        }

    .container {
        text-align: center;
    }

    .pricing__intro {
        max-width: 600px;
        margin: 0 auto 2.5rem auto;
        text-align: center;
    }

    /* Toggle */
    .pricing__switch {
        display: inline-flex;
        gap: 0.25rem;
        background: var(--td-card-bg);
        border: 1px solid var(--td-card-border);
        border-radius: 999px;
        padding: 0.3rem;
        margin: 0.5rem 0 0.75rem;
    }

    .pricing__switchBtn {
        border: none;
        background: transparent;
        color: var(--td-body-text);
        font-size: 0.92rem;
        font-weight: 600;
        padding: 0.5rem 1.1rem;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

        .pricing__switchBtn.is-active {
            background: var(--td-primary);
            color: white;
        }

    .pricing__trackNote {
        color: var(--td-body-text);
        font-size: 0.92rem;
        margin: 0 0 2.25rem;
    }

    /* Grid */
    .pricing__grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        align-items: stretch;
    }

    @media (max-width: 900px) {
        .pricing__grid {
            grid-template-columns: 1fr;
        }
    }

    .plan {
        border: 1px solid var(--td-card-border);
        border-radius: 18px;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        background: linear-gradient(180deg, rgba(124, 58, 237, 0.06), transparent 60%);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: left;
    }

        .plan:hover {
            transform: translateY(-4px);
            border-color: var(--td-star-gold);
            background: linear-gradient(180deg, rgba(255, 211, 77, 0.08), transparent 60%);
            box-shadow: 0 12px 30px rgba(255, 211, 77, 0.2);
        }

    .plan__scope {
        color: var(--td-accent);
        font-size: 0.85rem;
        font-weight: 600;
        margin: 0 0 0.4rem;
    }

    .plan__name {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 0.6rem;
        color: var(--td-white);
    }

    .plan__price {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.75rem;
        color: var(--td-white);
    }

    .plan__from {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--td-body-text);
        margin-right: 0.25rem;
    }

    .plan__desc {
        color: var(--td-body-text);
        font-size: 0.93rem;
        line-height: 1.5;
        margin: 0 0 1.25rem;
    }

    .plan__list {
        list-style: none;
        margin: 0 0 1.75rem;
        padding: 0;
        flex-grow: 1;
    }

        .plan__list li {
            padding: 0.4rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.9rem;
            color: var(--td-white);
        }

            .plan__list li:first-child {
                border-top: none;
            }

    .plan__cta {
        display: inline-block;
        text-align: center;
        padding: 0.7rem 1.25rem;
        border-radius: 10px;
        background: var(--td-primary);
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.2s ease;
    }

        .plan__cta:hover {
            background: var(--td-accent);
            transform: translateY(-2px);
        }

    .pricing__footnote {
        color: var(--td-body-text);
        font-size: 0.85rem;
        margin: 2.5rem 0 0;
        max-width: 60ch;
        text-align: left;
    }

    .pricing__hostNote {
        margin: 1.25rem 0 0;
        padding: 1rem 1.25rem;
        max-width: 60ch;
        border: 1px solid rgba(255, 211, 77, 0.3);
        border-radius: 12px;
        background: rgba(255, 211, 77, 0.06);
        text-align: left;
    }

    .pricing__hostNoteTitle {
        color: var(--td-star-gold);
        font-size: 0.92rem;
        font-weight: 700;
        margin: 0 0 0.35rem;
    }

    .pricing__hostNoteBody {
        color: var(--td-body-text);
        font-size: 0.87rem;
        line-height: 1.55;
        margin: 0;
    }
</style>
