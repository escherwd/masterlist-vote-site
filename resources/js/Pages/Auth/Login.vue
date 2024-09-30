<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    top_albums: Array,
})

var covers = props.top_albums.map(a => a.album_cover)
// Duplicate it and shuffle
covers = [...covers, ...covers]
    .map(value => ({ value, sort: Math.random() }))
    .sort((a, b) => a.sort - b.sort)
    .map(({ value }) => value)

</script>

<template>

    <Head title="Log in" />

    <div class="fixed -z-10 w-screen h-screen">
        <div class="absolute -ml-16 -mr-16 inset-0 flex flex-wrap justify-center content-center">
            <img v-for="cover in covers" class="w-32 h-32" :key="cover" :src="cover" alt="">
        </div>
        <div class="absolute -inset-2 bg-zinc-900/95"></div>
    </div>

    <div class="fixed -top-[0.5px] right-0 left-0 bottom-0 flex justify-center items-center">
        <!-- <div class="h-px flex-grow border-b border-dashed border-zinc-600"></div> -->
        <div class="p-4 flex-none text-center mx-auto text-xs font-medium tracking-wider text-white/50">
            <span class="text-white/90">VOTE</span>.ESCHER.WTF
        </div>
        <!-- <div class="h-px flex-grow border-b border-dashed border-zinc-600"></div> -->
    </div>


    <div class="right-0 left-0 bottom-0 fixed flex sm:items-end p-6 gap-6 flex-col sm:flex-row">
        <div class="flex-1 text-white/60">
            An access code is required to log in for the first time.
        </div>
        <div class="flex-none">
            <a :href="route('auth.redirect')" class=" bg-green-500 text-black hover:bg-green-600 p-3 inline-block">
                Sign in with Spotify
            </a>
        </div>

    </div>


</template>
