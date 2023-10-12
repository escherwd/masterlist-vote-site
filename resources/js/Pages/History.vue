<template>
    <Head title="History" />

    <AuthenticatedLayout>

        <!-- season picker -->
        <div class="flex gap-3 my-2">
            <Link href="/history" class="button" :class="!props.season_id ? 'selected' : 'unselected'">All</Link>
            <Link :href="`/history/${season.id}`" v-for="season of seasons" :key="season.id" class="button unselected"
                :class="props.season_id == season.id ? 'selected' : 'unselected'">Season {{ season.number }}</Link>
        </div>

        <!-- episodes -->
        <div class="mt-6">
            <div v-for="episode of episodes">
                <div class="bg-zinc-800 text-white/80 py-4 px-4 flex items-center gap-4 mt-4">
                    <div class="font-condensed text-4xl font-normal text-white/60">
                        S{{ episode.season.number.toString().padStart(2,'0') }}:<span class="text-white">E{{ episode.number.toString().padStart(2,'0') }}</span>
                    </div>
                    <div>
                        <div class="text-xs text-white/60">Season {{ episode.season.number }}</div>
                        <h3 class="text-sm">Episode {{ episode.number }}</h3>
                    </div>
                </div>
                <div v-if="episode.theme" class="px-4 pb-3 bg-zinc-800">
                    <div class="card-label">THEME</div>
                    <p>{{ episode.theme }}</p>
                </div>
                <div class=" bg-gradient-to-b from-zinc-800 to-zinc-900 px-4">
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
import { Head, Link, usePage } from '@inertiajs/vue3';
import Track from '@/Components/Track.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    seasons: Array,
    season_id: String,
    episodes: Array,
    group: Object,
    group_users: Array
})

function vote_max_for(episode) {
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