<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';


</script>

<template>
        <Head title="Log in" />

        <a :href="route('auth.redirect')" class=" bg-green-500 p-3 inline-block">
            Sign in with Spotify
        </a>
</template>
