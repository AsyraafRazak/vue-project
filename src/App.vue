<template>
    <div class="app-layout">
        <SparkleCursor />
        <NavBar />
        <main class="main-content">
            <router-view />
        </main>
        <FooterBar />
    </div>
</template>

<script setup>
    import NavBar from './components/NavBar.vue'
    import FooterBar from './components/FooterBar.vue'
    import SparkleCursor from './components/SparkleCursor.vue'
</script>