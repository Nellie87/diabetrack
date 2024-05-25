<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const { url, component, props } = usePage();
</script>

<template>
    <nav>
        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
        </NavLink>
        <NavLink :href="route('doctor')" :active="route().current('doctor')">
            Doctor
        </NavLink>
        <!-- Add other navigation links here -->
    </nav>
</template>