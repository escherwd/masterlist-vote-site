<template>
    <Link :href="`/track/${track.id}`" class="hide-last-border block -mx-2 px-2 hover:bg-white/5 light:hover:bg-neutral-500/5 rounded-md">
        <div class="py-2 border-b border-zinc-600 light:border-neutral-300 border-dashed">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 flex-none bg-zinc-700 light:bg-neutral-200 relative">
                <img :src="track.album_cover" class=" w-full h-full object-contain" alt="album cover">
                <div v-if="vote_count >= vote_threshold"
                    class="w-5 h-5 rounded-full overflow-hidden bg-zinc-800 light:bg-neutral-100 absolute -top-2 -right-2 outline outline-2 outline-zinc-800 light:outline-neutral-100 flex items-center justify-center">
                    <div class="w-4 h-4 rounded-full flex items-center justify-center text-zinc-800/80 light:text-neutral-50"
                        :class="vote_count == vote_max ? `bg-primary light:bg-secondary` : `bg-green-600`">
                        <StarIcon v-if="vote_count == vote_max" class="w-3 h-3" />
                        <CheckIcon v-else class="w-3 h-3" />
                    </div>
                </div>
            </div>
            <div class="flex-1 pl-1 min-w-0 light:text-neutral-600">
                <div class="font-medium ellipsis">{{ track.title }}</div>
                <div class="ellipsis text-sm text-white/60 light:text-neutral-400">{{ track.artist }}</div>
            </div>
            <div v-if="props.historical">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden inline-block border-4 border-zinc-800 light:border-neutral-100 -ml-4"
                    v-for="voter in voters">
                    <img v-if="voter.spotify_avatar" class="bg-zinc-700 light:bg-neutral-100" :src="voter.spotify_avatar" alt="">
                    <div v-else
                        class="h-full w-full bg-zinc-700 text-white/60 light:bg-neutral-200 light:text-neutral-400 overflow-hidden flex items-center justify-center uppercase text-sm font-mono leading-none font-bold">
                        {{ voter.name.substring(0, 1) }}
                    </div>
                </div>
            </div>
            <template v-else>
                <button disabled v-if="disable_vote" class="vote-disabled button w-24">
                    <span>VOTE</span>
                    <NoSymbolIcon />
                </button>
                <button v-else-if="voted" @click.prevent="$emit('vote', track.id, false)" class="selected button w-24">
                    <span>VOTED</span>
                    <CheckIcon />
                </button>
                <button v-else @click.prevent="$emit('vote', track.id, true)" class="unselected button w-24">
                    <span>VOTE</span>
                    <PlusIcon />
                </button>
            </template>

        </div>
        <div class="pt-2 flex gap-2 text-sm text-white/60 light:text-neutral-400 light:font-medium tracking-wide items-center">
            <div class="flex-1 items-center gap-2 flex min-w-0">
                <div class="h-5 w-5 shrink-0 bg-zinc-700 light:bg-zinc-200 rounded-full overflow-hidden">
                    <img v-if="user?.spotify_avatar" :src="user?.spotify_avatar" alt="">
                </div>
                <div class="text-xs uppercase tracking-wider min-w-0 ellipsis">
                    {{ user?.name ?? track.added_by }}
                </div>
            </div>
            <div class="h-full flex items-center" v-if="!props.historical">
                <div class="w-5 h-5 rounded-full overflow-hidden inline-block border-2 border-zinc-900 light:border-neutral-50 -ml-2"
                    v-for="voter in voters">
                    <img v-if="voter.spotify_avatar" :src="voter.spotify_avatar" alt="">
                    <div v-else
                        class="h-full w-full bg-zinc-700 text-white/60 overflow-hidden flex items-center justify-center uppercase text-xs font-mono leading-none font-bold">
                        {{ voter.name.substring(0, 1) }}
                    </div>
                </div>
            </div>
            <CheckCircleIcon v-else-if="voted" class="h-5 text-zinc-600 light:text-neutral-500" />

            <div class="h-5 w-24 shrink-0 bg-zinc-800 light:bg-neutral-50 relative flex items-center gap-x-2">
                <div
                    class="flex-grow-0 text-xs h-full font-mono leading-none border-r border-zinc-600 light:border-zinc-300 px-2 flex items-center font-bold">
                    {{ vote_count }}
                </div>
                <div class="py-2 flex-1 pr-2">
                    <div class="relative w-full h-1 bg-zinc-700 light:bg-neutral-200 overflow-hidden">
                        <div class="left-px border-r-2 border-white/30 light:border-neutral-400 h-full absolute z-0"
                            :style="{ width: `${(vote_threshold / vote_max) * 100}%` }"></div>
                        <div :class="{
                            'bg-primary light:bg-secondary': vote_count == vote_max,
                            'bg-green-600': vote_count >= vote_threshold && vote_count < vote_max,
                            'bg-zinc-300 light:bg-neutral-800': vote_count < vote_threshold
                        }" class="absolute h-full transition-all"
                            :style="{ width: `${(vote_count / vote_max) * 100}%` }">
                        </div>
                    </div>
                </div>

            </div>

        </div>
        </div>
    </Link>
</template>

<script setup>
import { PlusIcon, CheckIcon, NoSymbolIcon, UserIcon, CheckCircleIcon, StarIcon, FireIcon } from "@heroicons/vue/20/solid"
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    historical: Boolean,
    track: Object,
    disable_vote: Boolean,
    voted: Boolean,
    vote_count: Number,
    vote_threshold: Number,
    vote_max: Number,
    user: Object,
    voters: Array,
})

const emit = defineEmits(['vote'])

</script>

<style scoped lang="scss">

.hide-last-border:last-child > div {
    border-bottom: none;
}

.selected {
    animation: vote_animation 120ms ease 0s 1;
}

.vote-disabled {
    @apply bg-transparent text-white/20 light:text-neutral-300;
}

@keyframes vote_animation {
    0% {
        transform: scale(0.5);
    }

    70% {
        transform: scale(1.2);
    }

    100% {
        transform: scale(1);
    }
}

.voter:last-child>.voter-comma {
    display: none;
}
</style>