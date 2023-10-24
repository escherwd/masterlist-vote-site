<template>
    <Head title="History" />

    <AuthenticatedLayout>

        <SeasonPicker slug="stats" :seasons="props.seasons" :season_id="props.season_id" />
        <EpisodePicker v-if="season_id" slug="stats" :episodes="props.episodes" :episode_id="props.episode_id"
            :season_id="props.season_id" class="pt-2 border-t border-zinc-700" />

        <!-- Main Grid -->
        <div class="grid items-end grid-cols-1 sm:grid-cols-2 gap-2 mt-8">
            <div class="pb-4 pt-20">
                <h1 class="text-5xl">Votes</h1>
            </div>
            <div class="pb-4">
                <table class="w-full datatable">
                    <tbody>
                        <tr>
                            <td>
                                <span>Total Tracks Submitted</span>
                            </td>
                            <td>
                                <span>{{ props.tracks.length }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Total Vote Count</span>
                            </td>
                            <td>
                                <span>{{ votesTotal }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Avg. Votes Per Track</span>
                            </td>
                            <td>
                                <span>{{ (votesTotal / props.tracks.length).toFixed(1) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tile">
                <h2>Total Votes Recieved</h2>
                <Bar :data="barFor(votesForUsers, '# of votes')" />
            </div>
            <div class="tile">
                <h2>Total Votes Cast</h2>
                <Bar :data="barFor(castsForUsers, '# of votes')" />
            </div>
            <div class="tile">
                <h2>Share of Votes Recieved</h2>
                <Doughnut :options="donutOptions" :data="pieFor(votesForUsers, '# of votes')" />
            </div>
            <div class="tile">
                <h2>Share of Votes Cast</h2>
                <Doughnut :options="donutOptions" :data="pieFor(castsForUsers, '# of votes')" />
            </div>
            <div class="pb-4 pt-36">
                <h1 class="text-5xl">Playlists</h1>
            </div>
            <div class="pb-4">
                <table class="w-full datatable">
                    <tbody>
                        <tr>
                            <td>
                                <span>Accepted into Masterlist</span>
                            </td>
                            <td>
                                <span>
                                    {{ masterListTotal }}
                                    <span class="text-sm text-white/50">
                                        ({{ ((masterListTotal / props.tracks.length) * 100).toFixed(0) }}%)
                                    </span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Certified Bangers</span>
                            </td>
                            <td>
                                <span>
                                    {{ bangersTotal }}
                                    <span class="text-sm text-white/50">
                                        ({{ ((bangersTotal / props.tracks.length) * 100).toFixed(0) }}%)
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tile">
                <h2>Songs in Masterlist</h2>
                <Bar :data="barFor(listForUsers, '# of songs')" />
            </div>
            <div class="tile">
                <h2>Certified Bangers</h2>
                <Bar :data="barFor(bangersForUsers, '# of songs')" />
            </div>
            <div class="tile">
                <h2>Share of Masterlist</h2>
                <Doughnut :options="donutOptions" :data="pieFor(listForUsers, '# of songs')" />
            </div>
            <div class="tile">
                <h2>Share of Certified Bangers</h2>
                <Doughnut :options="donutOptions" :data="pieFor(bangersForUsers, '# of songs')" />
            </div>
            <div class="pb-4 pt-36 sm:col-span-2">
                <h1 class="text-5xl">Trends</h1>
            </div>
            <div class="sm:col-span-2 tile">
                <h2>Submissions by Decade</h2>
                <Bubble :options="decadeTrendOptions" :data="barFor(collect(fixedDecades), '# of songs')" />
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style lang="scss" scoped>
.tile {
    @apply p-3 bg-zinc-800;

    h2 {
        @apply text-lg font-medium pb-3 mb-3 border-b border-zinc-700;
    }
}

.datatable {

    // max-width: 10em;
    padding: 0;
    overflow: hidden;
    position: relative;

    list-style: none;

    td>span {
        @apply bg-zinc-900;
        position: relative;
        z-index: 1;
    }

    td:first-of-type>span {
        @apply pr-2 text-white/70;
    }

    td:last-of-type {
        @apply text-right;

        >span {
            @apply pl-2 font-mono;
        }
    }

    tr:before {
        @apply text-white/40;
        content:
            ". . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . ";
        white-space: nowrap;
        overflow: hidden;
        text-overflow: clip;
        position: absolute;
        float: left;
        padding: 0;
    }
}
</style>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import SeasonPicker from '@/Components/SeasonPicker.vue';
import EpisodePicker from '@/Components/EpisodePicker.vue';
import { Bar, Doughnut, Bubble } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, defaults, ArcElement, PointElement } from 'chart.js'
import _ from 'lodash'
import { collect } from 'collect.js'

import colors from 'tailwindcss/colors'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, PointElement)
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
    group: Object,
})

let nameFromId = collect(props.group_users).mapWithKeys(u => [u.spotify_id, u.name]).items
let colorFromName = collect(props.group_users).map((u, i) => ({
    ...u, color: [
        colors.red[500],
        colors.orange[500],
        colors.yellow[400],
        colors.green[500],
        colors.cyan[400],
        colors.blue[500],
        colors.violet[600],
        colors.pink[500],
    ][i]
})).mapWithKeys(u => [u.name, u.color]).items
let votesForUsers = collect(props.group_users).mapWithKeys(u => [u.name, 0])
let castsForUsers = collect(props.group_users).mapWithKeys(u => [u.name, 0])
let listForUsers = collect(props.group_users).mapWithKeys(u => [u.name, 0])
let bangersForUsers = collect(props.group_users).mapWithKeys(u => [u.name, 0])
let decadesForUsers = collect(props.group_users).mapWithKeys(u => [u.name, {}])

let votesTotal = 0;
let masterListTotal = 0;
let bangersTotal = 0;

for (const track of props.tracks) {
    votesForUsers.items[nameFromId[track.added_by]] += track.votes?.length
    for (const voter of track.votes) {
        votesTotal += 1
        castsForUsers.items[voter.name] += 1
    }
    // check if listed
    if (track.votes?.length >= props.group.vote_min) {
        masterListTotal += 1
        listForUsers.items[nameFromId[track.added_by]] += 1
    }
    if (track.votes?.length >= props.group_users.length - 1) {
        bangersTotal += 1
        bangersForUsers.items[nameFromId[track.added_by]] += 1
    }
    // add decade
    var item = decadesForUsers.items[nameFromId[track.added_by]]
    const decade = track.year - (track.year % 5)
    if (item[decade] == null) {
        item[decade] = 1
    } else {
        item[decade] += 1
    }
}


console.log(decadesForUsers.items)


// Change into [{x: name, y: votes}, ...] format
votesForUsers = votesForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
castsForUsers = castsForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
listForUsers = listForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
bangersForUsers = bangersForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
// decadesForUsers = decadesForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')

var fixedDecades = []
var uCount = 0
for (const u in decadesForUsers.items) {
    for (const decade in decadesForUsers.items[u]) {
        fixedDecades.push({
            x: uCount,
            y: decade, 
            r: decadesForUsers.items[u][decade] * 3,
            name: u,
            backgroundColor: colorFromName[u]
        })
    }
    uCount++
}

console.log(fixedDecades)

function barFor(items, label) {
    return {
        datasets: [{
            label: label,
            data: items.items,
            backgroundColor: items.pluck('backgroundColor').items,
        }]
    }
}

const donutOptions = {
    borderWidth: 3,
    borderColor: colors.zinc[800]
}

function pieFor(items, label) {
    return {
        labels: items.pluck('x').items,
        datasets: [{
            label: label,
            data: items.pluck('y').items,
            backgroundColor: items.pluck('backgroundColor').items,
        }]
    }
}

const decadeTrendOptions = {
  scales: {
    x: {
      ticks: {
        callback: function(value, index, ticks) {
          return props.group_users[index].name
        }
      }
    },
    y: {
      ticks: {
        callback: function(value, index, ticks) {
          return value
        },
        padding: 16,
      },
    }
  }
}



</script>