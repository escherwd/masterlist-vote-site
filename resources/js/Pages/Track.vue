<template>
    <Head :title="track.title" />

    <AuthenticatedLayout>
        <div class="hidden sm:block absolute w-full h-96 top-8 bg-zinc-700 -z-10">
            <div class="absolute w-full h-full z-10"
                style="background: radial-gradient(circle, rgba(24,24,27,0.6) 15%, rgba(24,24,27,1) 80%);">
            </div>
            <div class="absolute w-full h-full z-10"
                style="background: linear-gradient(180deg, rgba(24,24,27,1) 0%, rgba(24,24,27,0.1993391106442577) 40%, rgba(24,24,27,0.20214023109243695) 60%, rgba(24,24,27,1) 100%);">
            </div>
            <img v-if="extras.banner" class="w-full h-full object-cover z-0 fadein" style="object-position: center 25%;"
                :src="extras.banner" alt="">
        </div>
        <div class="flex flex-col sm:flex-row mt-8 sm:mt-32 gap-6 relative">
            <div class="flex-0">
                <img class="w-full aspect-square sm:h-56 sm:w-56 bg-zinc-800 shadow-lg" :src="track.album_cover" alt="">
            </div>
            <div class="flex-1 flex gap-0.5 items-start justify-center flex-col">
                <div class="text-3xl sm:text-4xl font-semibold tracking-tight text-white/90">
                    {{ track.title }}
                </div>
                <div class="text-lg sm:text-xl">
                    {{ track.album }} <span class="text-white/40">({{ track.year }})</span>
                </div>
                <div>
                    {{ track.artist }}
                </div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 mt-4 w-full sm:h-7">
            <div v-if="scores.length > 0" class="flex flex-1 items-center gap-2">
                <a target="_blank" v-for="(score) in scores" :href="score.url" class="text-[10px] flex rounded fadein">
                    <div
                        class="py-1 px-2 uppercase bg-zinc-800 tracking-wider font-medium text-white/70 border border-zinc-700 border-r-0 rounded-l">
                        {{ score.name }}
                    </div>
                    <div
                        class="py-1 px-2 bg-gradient-to-b from-zinc-600 to-zinc-700 text-white/90 font-mono font-medium border border-zinc-500 rounded-r">
                        {{ Number(score.score).toFixed(1) }}
                    </div>
                </a>
            </div>
            <div v-else class="flex-1"></div>
            <div class="flex pt-4 sm:pt-0 sm:flex-0 sm:gap-3 justify-evenly sm:justify-end">
                <a target="_blank" :href="`https://open.spotify.com/track/${track.track_id}`"
                    class="flex justify-center transition text-white/20 hover:text-green-500 border-r sm:border-none w-full border-zinc-700 h-7 sm:h-6">
                    <IconSpotify />
                </a>
                <a target="_blank"
                    :href="`https://www.deezer.com/search/${encodeURIComponent(`${track.title} - ${track.artist}`)}`"
                    class="flex justify-center transition text-white/20 hover:text-purple-500 border-r sm:border-none w-full border-zinc-700 h-7 sm:h-6">
                    <IconDeezer />
                </a>
                <a target="_blank"
                    :href="`https://music.apple.com/us/search?term=${encodeURIComponent(`${track.title} - ${track.artist}`)}`"
                    class="flex justify-center transition text-white/20 hover:text-white border-r sm:border-none w-full border-zinc-700 h-7 sm:h-6">
                    <IconApple />
                </a>
                <a target="_blank"
                    :href="`https://www.last.fm/music/${track.artist.replace(' ', '+')}/_/${track.title.replace(' ', '+')}`"
                    class="flex justify-center transition text-white/20 hover:text-red-500 w-full h-7 sm:h-6">
                    <IconLastfm />
                </a>
            </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row gap-y-8 gap-4 mt-8 sm:mt-6 items-start">
            <div class="flex-1 w-full">
                <!-- <iframe style="border-radius:12px"
                    :src="`https://open.spotify.com/embed/track/${track.track_id}?theme=0`"
                    width="100%" height="152" frameBorder="0" allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe> -->
                <div class="card-label muted pb-1 border-b border-zinc-700">SUBMITTED BY</div>
                <div class="mb-12">
                    <UserRow :user="adder" />
                </div>

                <div class="card-label muted pb-1 border-b border-zinc-700">VOTED BY</div>
                <div class="">
                    <UserRow v-for="voter in track.votes" :user="voter" />
                </div>

            </div>
            <div class="flex-0 sm:w-64 grid grid-col-2 gap-2 w-full">
                <div class="col-span-2 p-2 bg-zinc-800 text-center font-medium">
                    <span v-if="(track.votes?.length ?? 0) >= (group_users?.length ?? 999) - 1">
                        <StarIcon class="h-4 inline -mt-0.5" /> certified banger
                    </span>
                    <span v-else-if="track.votes.length >= episode.vote_min">
                        <CheckIcon class="h-4 inline -mt-0.5" /> in masterlist
                    </span>
                    <span v-else>
                        <XMarkIcon class="h-4 inline" /> not in masterlist
                    </span>
                </div>
                <div class="bg-zinc-800 py-3 px-4">
                    <div class="card-label">SEASON</div>
                    <div class="font-mono text-2xl">{{ season.number }}</div>
                </div>
                <div class="bg-zinc-800 py-3 px-4">
                    <div class="card-label">EPISODE</div>
                    <div class="font-mono text-2xl">{{ episode.number }}</div>
                </div>
                <div class="bg-zinc-800 col-span-2 py-3 px-4">
                    <div class="card-label">THEME</div>
                    <p>
                        {{ episode.theme }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ArrowUpRightIcon, CheckIcon, StarIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { Head } from '@inertiajs/vue3';
import UserRow from '@/Components/UserRow.vue';
import IconSpotify from "@/Components/IconSpotify.vue";
import IconDeezer from '@/Components/IconDeezer.vue';
import IconApple from '@/Components/IconApple.vue';
import IconLastfm from '@/Components/IconLastfm.vue';
import { ref } from 'vue';
import { onMounted } from 'vue';
import axios from 'axios';

const scores = ref([])
const extras = ref({})

const props = defineProps({
    track: Object,
    adder: Object,
    episode: Object,
    season: Object,
    group: Object,
    group_users: Array,
})

onMounted(async () => {
    // its so cool this works in just one line
    scores.value = (await axios.get(`/score/${props.track.artist}/${props.track.album}`)).data
    extras.value = (await axios.get(`/artist_img/${props.track.track_id}`)).data
})

</script>