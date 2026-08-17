import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../pages/HomePage.vue'
import ContactPage from '../pages/ContactPage.vue'
import DemoPage from '../pages/DemoPage.vue'

const routes = [
    { path: '/', name: 'home', component: HomePage },
    { path: '/contact', name: 'contact', component: ContactPage },
    { path: '/demo', name: 'demo', component: DemoPage }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router