<template>
    <div class="border-b border-zinc-600 py-2 border-dashed last:border-b-0">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 flex-none bg-zinc-700 relative">
                <img :src="track.album_cover" class=" w-full h-full object-contain" alt="album cover">
                <!-- <div
                    class="w-5 h-5 rounded-full overflow-hidden bg-zinc-600 absolute top-3 -right-2 outline outline-4 outline-zinc-900">
                    <img src="https://i.scdn.co/image/ab67757000003b82b70def4d6b304192db69e89d">
                </div> -->
            </div>
            <div class="flex-1 pl-1 min-w-0">
                <div class="font-medium ellipsis">{{ track.title }}</div>
                <div class="ellipsis text-sm text-white/60">{{ track.artist }}</div>

            </div>
            <button disabled v-if="disable_vote" class="vote-disabled button w-24">
                <span>VOTE</span>
                <NoSymbolIcon />
            </button>
            <button v-else-if="voted" @click="$emit('vote', track.id, false)" class="voted button w-24">
                <span>VOTED</span>
                <CheckIcon />
            </button>
            <button v-else @click="$emit('vote', track.id, true)" class="vote button w-24">
                <span>VOTE</span>
                <PlusIcon />
            </button>
        </div>
        <div class="pt-2 flex gap-2 text-sm text-white/60 tracking-wide">
            <div class="flex-1 items-center gap-2 flex min-w-0">
                <div class="h-5 w-5 shrink-0 bg-zinc-700 rounded-full overflow-hidden">
                    <img v-if="user?.spotify_avatar" :src="user?.spotify_avatar" alt="">
                </div>
                <div class="text-xs uppercase tracking-wider min-w-0 ellipsis">
                    {{ user?.name ?? track.added_by }}
                </div>
            </div>
            <div>
                <div class="w-5 h-5 rounded-full overflow-hidden inline-block border-2 border-zinc-900 -ml-2" v-for="voter in voters">
                    <img v-if="voter.spotify_avatar" :src="voter.spotify_avatar" alt="">
                    <div v-else class="h-full w-full bg-zinc-700 text-white/60 overflow-hidden flex items-center justify-center uppercase text-xs font-mono leading-none font-bold">
                        {{ voter.name.substring(0,1) }}
                    </div>
                </div>
            </div>
            <div class="h-5 w-24 shrink-0 bg-zinc-800 relative flex items-center gap-x-2">
                <div class="flex-grow-0 text-xs h-full font-mono leading-none border-r border-zinc-600 px-2 flex items-center font-bold">
                    {{ vote_count }}
                </div>
                <div class="py-2 flex-1 pr-2">
                    <div class="relative w-full h-1 bg-zinc-700 overflow-hidden">
                        <div class="left-px border-r-2 border-white/30 h-full absolute z-0"
                            :style="{ width: `${(vote_threshold / vote_max) * 100}%` }"></div>
                        <div :class="{
                            'bg-primary': vote_count == vote_max,
                            'bg-green-600': vote_count >= vote_threshold && vote_count < vote_max,
                            'bg-white': vote_count < vote_threshold
                        }" class="absolute h-full transition-all" :style="{ width: `${(vote_count / vote_max) * 100}%` }">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>

<script setup>
import { PlusIcon, CheckIcon, NoSymbolIcon, ChevronRightIcon } from "@heroicons/vue/20/solid"

const props = defineProps({
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
.vote {
    @apply hover:text-white text-white/50 bg-zinc-800;
}

.voted {
    @apply text-black/90 bg-primary hover:bg-primary/80;
    animation: vote_animation 120ms ease 0s 1;
}

.vote-disabled {
    @apply bg-transparent text-white/20;
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