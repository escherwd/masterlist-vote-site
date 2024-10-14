<script setup>

import { Link } from '@inertiajs/vue3';
import { SunIcon, MoonIcon } from "@heroicons/vue/20/solid"

const isLightTheme = localStorage.getItem('theme') == 'light'

const cycleTheme = () => {
    if (isLightTheme) {
        // Set dark theme
        document.body.classList.remove('light-theme')
        localStorage.setItem('theme','dark')
    } else {
        // Set light theme
        document.body.classList.add('light-theme')
        localStorage.setItem('theme','light')
    }
    location.reload()
}

</script>

<template>
    <div class="min-h-screen px-4 max-w-3xl w-full min-w-0 relative z-0 mx-auto light:text-neutral-600 text-white/80">
        <!-- Header -->
        <div class="pt-6 pb-5">

            <div class="flex gap-4 items-center mb-4 text-xs font-medium tracking-wider">
                <!-- <div class="h-px w-4 border-b border-dashed border-zinc-600"></div> -->
                <div class="text-white/50 light:text-neutral-400">
                    <span class="text-white/90 light:text-black/90">
                        <span class="h-2.5 w-2.5 inline-block mr-1 bg-primary light:bg-secondary"></span>
                        VOTE</span>.ESCHER.WTF
                </div>
                <div class="h-px flex-grow border-b border-dashed border-zinc-600 light:border-neutral-300"></div>
                <div>
                    
                    <a href="#" @click="cycleTheme" class="text-primary light:text-secondary hover:underline">
                        <MoonIcon v-if="isLightTheme" class="h-4 w-4 inline-block" />
                        <SunIcon v-else class="h-4 w-4 inline-block" />
                    </a>
                    <span class="mx-2">/</span>
                    <Link as="button" class=" text-primary light:text-secondary hover:underline" method="post" :href="route('logout')">
                LOG OUT
                </Link>
                </div>
                
            </div>

            <div class="flex gap-3 items-baseline">
                <Link :href="route('dashboard')" class="menu-link" :class="{ 'active': route().current('dashboard') }">
                Vote
                </Link>

                <Link :href="route('history')" class="menu-link" :class="{ 'active': route().current('history') }">
                History
                </Link>

                <Link :href="route('stats')" class="menu-link" :class="{ 'active': route().current('stats') }">
                Stats
                </Link>
            </div>
        </div>

        <!-- Page Content -->
        <div>
            <main>
                <slot />
            </main>
        </div>

        <!-- Footer -->
        <div class="pt-6 mt-8 mb-12 border-t border-zinc-600 light:border-neutral-300 border-dashed">
            <div class="mb-3 text-white/50 light:text-neutral-400 light:font-medium text-xs uppercase tracking-widest">Sponsored By</div>
            <div class="flex gap-4">
                <img src="/images/eschertalks_logo.png" class="h-8 light:invert" alt="escherTalks">
                <img src="/images/bruhcraft_logo.png" class="h-8" alt="escherTalks">
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.menu-link {
    @apply text-lg light:text-neutral-600;

    &.active {
        @apply text-primary light:text-secondary text-4xl border-b-2 border-primary light:border-secondary;
    }

    &:hover {
        @apply opacity-50;
    }
}
</style>