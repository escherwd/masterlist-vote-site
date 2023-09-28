<script setup>

import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="min-h-screen px-4 max-w-3xl w-full min-w-0 relative z-0 mx-auto text-white/80">
        <!-- Header -->
        <div class="pt-6 pb-5">

            <div class="flex gap-4 items-center mb-4 text-xs font-medium tracking-wider">
                <!-- <div class="h-px w-4 border-b border-dashed border-zinc-600"></div> -->
                <div class="text-white/50">
                    <span class="text-white/90">👉&nbsp;&nbsp;VOTE</span>.ESCHER.WTF
                </div>
                <div class="h-px flex-grow border-b border-dashed border-zinc-600"></div>
                <Link class=" text-primary hover:underline" method="post" :href="route('logout')">
                LOG OUT
                </Link>
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
        <div class="pt-6 mt-8 mb-12 border-t border-zinc-600 border-dashed">
            <div class="mb-3 text-white/50 text-xs uppercase tracking-widest">Sponsored By</div>
            <div class="flex gap-4">
                <img src="/images/eschertalks_logo.png" class="h-8" alt="escherTalks">
                <img src="/images/bruhcraft_logo.png" class="h-8" alt="escherTalks">
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.menu-link {
    @apply text-lg;

    &.active {
        @apply text-primary text-4xl border-b-2 border-primary;
    }

    &:hover {
        @apply opacity-50;
    }
}
</style>