<template>
    <Head title="History" />

    <AuthenticatedLayout>

        <!-- season picker -->
        <div class="flex items-center flex-col gap-y-2 md:flex-row no-scrollbar min-w-0">
            <div class="flex-1 w-full overflow-hidden min-w-0">
                <SeasonPicker class="w-full" :seasons="seasons" :season_id="season_id" slug="history" />
            </div>
            <div class="w-full md:w-64">
                <form @submit.prevent="submitSearch">
                    <input class="w-full h-9 !ring-primary light:!ring-secondary !border-zinc-700 light:!border-neutral-200 bg-zinc-800 light:bg-neutral-100 text-white/80 light:text-neutral-600" type="text" placeholder="Search..." v-model="searchForm.query">
                </form>
            </div>
        </div>
        

        <!-- episodes -->
        <div class="mt-6">
            <div v-for="episode of episodes">
                <div class="bg-zinc-800 light:bg-neutral-100 text-white/80 light:text-neutral-600 py-4 px-4 flex items-center gap-2 mt-4">
                    <div class="font-condensed text-4xl font-normal text-white/60 light:text-neutral-400 pr-2">
                        S{{ episode.season.number.toString().padStart(2,'0') }}:<span class="text-white light:text-neutral-600">E{{ episode.number.toString().padStart(2,'0') }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-white/60 light:text-neutral-400">Season {{ episode.season.number }}</div>
                        <h3 class="text-sm">Episode {{ episode.number }}</h3>
                    </div>
                    <div>
                        <CalendarIcon class="w-4 h-4" />
                    </div>
                    <div>
                        {{ DateTime.fromISO(episode.created_at).toFormat("LLL dd, yyyy") }}
                    </div>
                </div>
                <div v-if="episode.theme" class="px-4 pb-3 bg-zinc-800 light:bg-neutral-100">
                    <div class="card-label">THEME</div>
                    <p>{{ episode.theme }}</p>
                </div>
                <div class=" bg-gradient-to-b from-zinc-800 to-zinc-900 light:from-neutral-100 light:to-neutral-50 px-4">
                    <template v-for="track of episode.submissions">
                    <Track historical :track="track" :vote_count="track.votes?.length ?? 0"
                        :vote_max="vote_max_for(episode)" :vote_threshold="episode.vote_min"
                        :voted="(track.votes ?? []).filter(u => u.id == user.id).length > 0"
                        :user="user_for_spotify(track.added_by)" :voters="track.votes" />
                </template>
                </div>
                
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import Track from '@/Components/Track.vue';
import { DateTime } from 'luxon'
import { CalendarIcon, ClockIcon } from '@heroicons/vue/20/solid';
import SeasonPicker from '../Components/SeasonPicker.vue'
import { reactive, ref } from 'vue';

const user = usePage().props.auth.user;

const props = defineProps({
    seasons: Array,
    season_id: String,
    episodes: Array,
    group: Object,
    group_users: Array,
    query: String
})

const searchForm = useForm({
  query: props.query,
})

function submitSearch() {
    // searchForm.post('/dashboard', {
    //     preserveState: false
    // })
    // router.post('/dashboard',searchForm)
    router.visit(props.season_id ? `/history/${props.season_id}` : `/history`, {
        data: {
            q: searchForm.query
        }
    })
}

function vote_max_for(episode) {

    // lets just use group size for now
    return props.group_users.length - 1;

    // Count the number of unique users present here
    let ids = []
    for (const track of episode.submissions) {
        if (!ids.includes(track.added_by)) ids.push(track.added_by)
    }
    // the most votes a track can have is one less than the number of people voting
    // (can't vote for yourself)
    return ids.length - 1;
}

function user_for_spotify(spotify_id) {
    for (const u of props.group_users) {
        if (u.spotify_id == spotify_id) return u
    }
    return null;
}


</script>