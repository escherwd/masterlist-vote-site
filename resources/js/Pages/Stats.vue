<template>
    <Head title="History" />

    <AuthenticatedLayout>

        <SeasonPicker slug="stats" :seasons="props.seasons" :season_id="props.season_id" />
        <EpisodePicker v-if="season_id" slug="stats" :episodes="props.episodes" :episode_id="props.episode_id"
            :season_id="props.season_id" class="pt-2 border-t border-zinc-700" />

        <!-- Main Grid -->
        <div class="grid items-stretch grid-cols-1 sm:grid-cols-2 gap-2 mt-8">
            <div class="pb-4 pt-20">
                <h1 class="text-5xl">Votes</h1>
            </div>
            <div class="pb-4 flex items-end">
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
                <Bar :options="barOptions" :data="barFor(votesForUsers, '# of votes')" />
            </div>
            <div class="tile">
                <h2>Total Votes Cast</h2>
                <Bar :options="barOptions" :data="barFor(castsForUsers, '# of votes')" />
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
            <div class="pb-4 flex items-end">
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
                <Bar :options="barOptions"  :data="barFor(listForUsers, '# of songs')" />
            </div>
            <div class="tile">
                <h2>Certified Bangers</h2>
                <Bar :options="barOptions"  :data="barFor(bangersForUsers, '# of songs')" />
            </div>
            <div class="tile">
                <h2>Share of Masterlist</h2>
                <Doughnut :options="donutOptions" :data="pieFor(listForUsers, '# of songs')" />
            </div>
            <div class="tile">
                <h2>Share of Certified Bangers</h2>
                <Doughnut :options="donutOptions" :data="pieFor(bangersForUsers, '# of songs')" />
            </div>
            <div class="pb-4 pt-36">
                <h1 class="text-5xl">Trends</h1>
            </div>
            <div class="pb-4 flex items-end">
                <table class="w-full datatable">
                    <tbody>
                        <tr>
                            <td>
                                <span>Most Popular Year</span>
                            </td>
                            <td>
                                <span>
                                    {{ most_popular_year }}
                                    <span class="text-sm text-white/50">
                                        ({{ ((most_popular_year_percent) * 100).toFixed(0) }}%)
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="sm:col-span-2 tile">
                <h2>Submissions by Decade</h2>

                <div class="relative">
                    <div class="absolute inset-0 flex justify-evenly">
                        <div v-for="(tmpl_decade) in decades"
                            class="flex-1 border-r border-dashed border-zinc-700 last:border-none">
                        </div>
                    </div>
                    <div class="my-6 flex justify-evenly">
                        <div v-for="(tmpl_decade) in decades" class="flex-1 text-center text-xs text-white/60">
                            <span class="hidden sm:inline">{{ tmpl_decade }}s</span>
                            <span class="inline sm:hidden">'{{ (tmpl_decade % 100).toString().padStart(2, '0') }}s</span>
                        </div>
                    </div>
                    <div v-for="(user_decades, user_name) in decadesForUsers.items" class="mb-5 z-10 relative">
                        <div class="flex w-full justify-evenly">
                            <div v-for="(tmpl_decade) in decades"
                                class="flex-1 flex justify-evenly border-b border-zinc-700">
                                <div v-if="user_decades[tmpl_decade]" v-for="tmpl_period in periods"
                                    class="flex-1 h-12 relative">
                                    <div v-if="user_decades[tmpl_decade][tmpl_period]"
                                        class="absolute bottom-0 left-0 right-0 bg-white hover:border border-white"
                                        @mouseenter="setYearToolTip(user_name, tmpl_decade, tmpl_period, user_decades[tmpl_decade][tmpl_period] ?? 0)"
                                        @mouseleave="setYearToolTip(null)"
                                        :style="{ height: `${100 * ((user_decades[tmpl_decade][tmpl_period] ?? 0) / period_max_z)}%`, background: colorFromName[user_name] }">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="year_tooltip_user == user_name"
                            class="w-full mt-2 absolute text-xs uppercase tracking-wide text-white/60 text-center">
                            {{ year_tooltip_message }}
                        </div>
                        <div class="flex w-full justify-evenly mt-2 h-3">
                            <div v-for="(tmpl_decade) in decades"
                                class="flex-1 text-center font-mono text-xs text-white/60">
                                <span v-if="user_decades[tmpl_decade] && year_tooltip_user != user_name">{{
                                    user_decades[tmpl_decade]['total'] }}</span>
                            </div>
                        </div>
                        <div class="text-center w-full mt-2 ">
                            <span class="w-3 h-3 inline-block mr-2"
                                :style="{ background: colorFromName[user_name] }"></span>
                            {{ user_name }}
                        </div>
                    </div>
                </div>

                <!-- <Bubble :options="decadeTrendOptions" :data="barFor(collect(fixedDecades), '# of songs')" /> -->
            </div>
            <div class="tile">
                <h2>Top Artists</h2>
                <table class="w-full datatable">
                    <tbody>
                        <tr v-for="row,i in props.top_artists" :class="{
                            'text-3xl': i == 0,
                            'text-2xl': i == 1,
                            'text-xl': i == 2,
                            'text-lg': i == 3,
                        }">
                            <td>
                                <span>{{ row.artist }}</span>
                            </td>
                            <td>
                                <span>
                                    {{ row['COUNT(*)'] }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tile">
                <h2>Top Albums</h2>
                <div class="flex w-full flex-wrap">
                    <div v-for="row, i in props.top_albums" class="p-1 relative" :class="{
                        'w-1/2': i <= 1,
                        'w-1/3': i >= 2 && i <= 4,
                        'w-1/4': i >= 5 
                    }">
                        <img class="aspect-square bg-zinc-700" :src="row.album_cover" alt="">
                        <div class="absolute inset-0 p-2 text-[7pt] opacity-0 transition-opacity hover:opacity-100 bg-zinc-800/80 text-white overflow-hidden flex justify-end flex-col">
                            <div class="line-clamp-1 text-primary tracking-wider uppercase text-[6pt]">
                                {{ row['COUNT(*)'] }} Tracks
                            </div>
                            <div class="font-bold line-clamp-2">
                                {{ row.album }}
                            </div>
                            <div class="line-clamp-2">
                                {{ row.artist }}
                            </div>
                        </div>
                    </div>
                </div>
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

.tile .datatable td>span {
        @apply bg-zinc-800;
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

    tr {
        position: relative;
        vertical-align: bottom;
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
import { ref } from 'vue';
import { RectangleStackIcon } from '@heroicons/vue/20/solid';

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
    top_artists: Array,
    top_albums: Array,
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

let decade_lower_bound = 9999;
let decade_upper_bound = 0;
let period_max_z = 0;
let all_track_years = [];

let period_length = 1;
const year_tooltip_user = ref(null);
const year_tooltip_message = ref(null);

function setYearToolTip(user, decade, year, amount) {
    year_tooltip_user.value = user;
    year_tooltip_message.value = `${amount} tracks in ${decade + year}`;
}

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
    const decade = track.year - (track.year % 10)
    const period = track.year - (track.year % period_length) - decade
    if (item[decade] == null) {
        item[decade] = { [period]: 1, total: 1 }
    } else if (item[decade][period] == null) {
        item[decade][period] = 1
        item[decade]['total'] += 1
    } else {
        item[decade][period] += 1
        item[decade]['total'] += 1
        period_max_z = Math.max(period_max_z, item[decade][period])
    }
    decade_lower_bound = Math.min(decade_lower_bound, decade)
    decade_upper_bound = Math.max(decade_upper_bound, decade)

    all_track_years.push(track.year);
}

// Find most popular year
const most_popular_year = collect(all_track_years).mode()[0];
const most_popular_year_percent = collect(all_track_years).countBy().all()[most_popular_year] / all_track_years.length;

// Compile decades for chart
var decades = [];
for (let index = decade_lower_bound; index <= decade_upper_bound; index += 10) {
    decades.push(index);
}
var periods = [];
for (let index = 0; index < 10; index += period_length) {
    periods.push(index);
}

// Change into [{x: name, y: votes}, ...] format
votesForUsers = votesForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
castsForUsers = castsForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
listForUsers = listForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
bangersForUsers = bangersForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')
// decadesForUsers = decadesForUsers.map((v, i) => ({ x: i, y: v, backgroundColor: colorFromName[i] })).values().sortByDesc('y')

// var fixedDecades = []
// var uCount = 0
// for (const u in decadesForUsers.items) {
//     for (const decade in decadesForUsers.items[u]) {
//         fixedDecades.push({
//             x: uCount,
//             y: decade, 
//             r: decadesForUsers.items[u][decade] * 3,
//             name: u,
//             backgroundColor: colorFromName[u]
//         })
//     }
//     uCount++
// }

console.log(decadesForUsers.items)

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

const barOptions = {
    scaleShowValues: true,
    scales: {
        x: {
            ticks: {
                autoSkip: false
            }
        }
    }
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



</script>