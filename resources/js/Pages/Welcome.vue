<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <div>
        <div class="block">
            Welcome to the app.
        </div>

        <template v-if="user">
            <Link :href="route('dashboard')">
                Dashboard
            </Link>
        </template>

        <template v-else>
            <Link :href="route('login')">
                Login
            </Link>
        </template>

        
    </div>
    
</template>

<style>

</style>
