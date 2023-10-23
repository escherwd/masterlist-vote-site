<template>
    <Head title="History" />

    <AuthenticatedLayout>

        <SeasonPicker slug="stats" :seasons="props.seasons" :season_id="props.season_id" />
        <EpisodePicker v-if="season_id" slug="stats" :episodes="props.episodes" :episode_id="props.episode_id"
            :season_id="props.season_id" class="pt-2 border-t border-zinc-700" />

        <!-- Main Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-8">
            <div class="p-3 bg-zinc-800">
                <Bar id="my-chart-id" :options="barOpts" :data="barUserVotes" />
            </div>
            <div class="p-4 bg-zinc-800">d</div>
            <div class="p-4 bg-zinc-800">d</div>
            <div class="p-4 bg-zinc-800">d</div>
            <div class="p-4 bg-zinc-800">d</div>
            <div class="p-4 bg-zinc-800">d</div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import SeasonPicker from '@/Components/SeasonPicker.vue';
import EpisodePicker from '@/Components/EpisodePicker.vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, defaults } from 'chart.js'
import _ from 'lodash'
import { collect } from 'collect.js'

import colors from 'tailwindcss/colors'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
defaults.color = colors.neutral[400]
defaults.backgroundColor = colors.yellow[400]
defaults.responsive = true

const user = usePage().props.auth.user;

const props = defineProps({
    seasons: Array,
    season_id: String,
    episodes: Array,
    episode_id: String,
    tracks: Array,
    group_users: Array,
})

let votesForUsers = collect(props.group_users).mapWithKeys(u => [u.spotify_id, 0])

props.tracks.forEach(track => {
    votesForUsers[track.added_by] += track.votes.length
})

const barUserVotes = {
    labels: props.group_users.map(u => u.name),
    datasets: [{ label: "Votes Recieved", data: [40, 20, 12, 44, 65, 33, 44, 11] }]
}

const barOpts = {
    responsive: true,
    color: colors.neutral[100],
}

</script>