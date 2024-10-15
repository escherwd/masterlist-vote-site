
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <FinishEpisodeModal v-if="showFinishEpisodeModal" :episode="props.episode"
            :counts="[accepted_songs_count, bangers_count, submissions.length]" @close="showFinishEpisodeModal = false"
            :finish_season="isFinishingSeason" />

        <div class="block w-full md:hidden mb-4">
            <DashboardInfoPanels :group="group" :season="season" :episode="episode" :themeSubmit="themeSubmit" :editEpisode="editEpisode" :themeNeedsSave="themeNeedsSave" />
            <div class="border-b flex items-end w-full pt-8 pb-2 text-xl font-medium border-zinc-700 light:border-neutral-200 border-solid">
                <span class="flex-1">Tracks</span>
                <div class="text-sm font-normal text-white/50 light:text-neutral-400">
                    12
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-start gap-x-4 w-full relative gap-y-12">
            <div class="flex-1 min-w-0 max-w-full w-full">
                <template v-for="track in submissions">
                    <Track :track="track" :vote_count="votes[track.id]?.length ?? 0" :vote_max="vote_max"
                        :vote_threshold="episode.vote_min" :disable_vote="track.added_by == user.spotify_id"
                        :voted="(votes[track.id] ?? []).includes(user.id)" :user="user_for_spotify(track.added_by)"
                        :voters="votes[track.id].map(v => user_for_id(v))" @vote="vote" />
                </template>
                <div v-if="submissions.length == 0" class="text-center py-8 text-white/40 uppercase text-sm tracking-wider">
                    <FaceFrownIcon :class="{ 'animate-pulse': isRefreshing }" class="h-6 w-6 mx-auto" />
                    <span class="block mt-2">No submissions yet.</span>
                    <button @click="refreshTracks"
                        class="block mt-2 uppercase text-xs tracking-wide font-medium hover:text-white mx-auto">Refresh
                        Tracks</button>
                </div>
            </div>
            <div class="flex-grow-0 min-w-0 flex-shrink-0 w-full md:w-64 ">

                <DashboardInfoPanels class="hidden md:grid" :group="group" :season="season" :episode="episode" :themeSubmit="themeSubmit" :editEpisode="editEpisode" :themeNeedsSave="themeNeedsSave" />

                <div class="hidden md:block divider my-4"></div>

                <div class="grid grid-cols-2 gap-2 light:text-neutral-600">
                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4">
                        <div class="card-label">APPROVED</div>
                        <div class="font-mono text-2xl">
                            <span>{{ accepted_songs_count }}</span>
                            <span class="text-base text-white/60 light:text-neutral-400">/{{ submissions.length }}</span>
                        </div>
                    </div>
                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4">
                        <div class="card-label">BANGERS</div>
                        <div class="font-mono text-2xl">{{ bangers_count }}</div>
                    </div>
                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4 col-span-2">
                        <div class="card-label">TOP TRACKS</div>
                        <div class="mt-3">
                            <div v-for="track, idx in leaderboard" :key="track.id"
                                class="flex py-1 gap-2 border-b items-center border-zinc-700 light:border-neutral-200 last:border-none">
                                <div class="w-6 h-6 bg-slate-700 light:bg-neutral-200">
                                    <img :src="track.album_cover" alt="">
                                </div>
                                <div class="flex-1 text-sm font-medium min-w-0 ellipsis">
                                    {{ track.title }}
                                </div>
                                <div class="font-mono text-xs font-semibold text-primary light:text-secondary">
                                    {{ track.votes }} VOTES
                                </div>
                            </div>
                            <div v-if="leaderboard.length == 0" class="text-white/30 italic">
                                No Votes Yet
                            </div>
                        </div>
                    </div>
                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4 col-span-2">
                        <div class="card-label">TOP VOTERS</div>
                        <div class="mt-3 light:text-neutral-600">
                            <div v-for="voter, idx in user_leaderboard" :key="voter.id"
                                class="flex py-1 gap-2 border-b items-center border-zinc-700 light:border-neutral-200 last:border-none">
                                <div class="w-6 h-6 bg-slate-700 light:bg-neutral-200 rounded-full overflow-hidden">
                                    <img v-if="voter.spotify_avatar" :src="voter.spotify_avatar" alt="">
                                </div>
                                <div class="flex-1 text-sm font-medium min-w-0 ellipsis">
                                    {{ voter.name }}
                                </div>
                                <div class="font-mono text-xs font-semibold text-primary light:text-secondary">
                                    {{ voter.votes }} VOTES
                                </div>
                            </div>
                        </div>
                        <div v-if="leaderboard.length == 0" class="text-white/30 italic">
                            No Votes Yet
                        </div>
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div class="grid grid-cols-2 gap-2">
                    <button @click="refreshTracks"
                        class="col-span-2 button w-full bg-primary hover:bg-primary/80 text-black/90 light:bg-secondary light:hover:bg-secondary/80 light:text-neutral-50/90">
                        <ArrowPathIcon :class="{ 'animate-spin': isRefreshing }" />
                        <span>Refresh Tracks</span>
                    </button>

                    <button @click="isFinishingSeason = false; showFinishEpisodeModal = true"
                        class="col-span-2 button w-full bg-green-600 hover:bg-green-600/80 light:bg-green-500 light:hover:bg-green-500/80 light:text-neutral-50/90">
                        <CheckIcon />
                        <span>Finish Episode</span>
                    </button>

                    <div @click="isFinishingSeason = true; showFinishEpisodeModal = true"
                        class="col-span-2 p-2 text-center text-sm text-white/60">
                        <button class="hover:underline">
                            Finish Season
                        </button>
                    </div>


                </div>


            </div>
        </div>

    </AuthenticatedLayout>
</template>


<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Track from '@/Components/Track.vue';
import { computed, ref } from 'vue';
import { ArrowPathIcon, CheckIcon } from "@heroicons/vue/20/solid"
import { FaceFrownIcon } from '@heroicons/vue/24/solid'
import FinishEpisodeModal from "@/Components/FinishEpisodeModal.vue";
import { watch } from 'vue';
import DashboardInfoPanels from '../Components/DashboardInfoPanels.vue'

const user = usePage().props.auth.user;

const props = defineProps({
    group: Object,
    group_users: Array,
    season: Object,
    episode: Object,
    submissions: Array,
    votes_default: Object,
})

/** @type {Ref<number[][]>} */
const votes = ref(props.votes_default);

const isRefreshing = ref(false);
const showFinishEpisodeModal = ref(false);
const isFinishingSeason = ref(false);

// Votes need to be synced back and forth
watch(() => props.votes_default, (new_votes) => {
    votes.value = new_votes;
});

const editEpisode = useForm({
    theme: props.episode.theme
});

const themeNeedsSave = computed(() => props.episode.theme != editEpisode.theme);

function themeSubmit() {
    editEpisode.post(`/api/episode/${props.episode.id}/update`, {
        preserveScroll: true,
        onFinish: () => {
            document.activeElement.blur()
        },
    });
}

// Log a vote (or unvote) for a track_id
function vote(track_id, is_vote) {
    // Toggle user id in vote array
    if (is_vote == false) {
        votes.value[track_id] = (votes.value[track_id] ?? []).filter(u => u != user.id);
    } else if (!votes.value[track_id].includes(user.id)) {
        if (votes.value[track_id])
            votes.value[track_id].push(user.id);
        else
            votes.value[track_id] = [user.id]
    }
    // Send to database
    useForm({
        "submission_id": track_id,
        "is_vote": is_vote
    }).post('/api/vote', { preserveScroll: true });
}

function user_for_id(id) {
    for (const u of props.group_users) {
        if (u.id == id) return u
    }
    return null;
}

// Grab a user object based on a spotify_id
function user_for_spotify(spotify_id) {
    for (const u of props.group_users) {
        if (u.spotify_id == spotify_id) return u
    }
    return null;
}

function refreshTracks() {
    isRefreshing.value = true;
    useForm({}).post(`/api/episode/${props.episode.id}/refresh`, {
        preserveScroll: true,
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
}

// Generate a shortlist of the top tracks
const leaderboard = computed(() => {
    let top = Object.keys(votes.value)
        .filter((k) => votes.value[k].length != 0) // remove songs with no votes
        .sort((a, b) => votes.value[b].length - votes.value[a].length)
        .splice(0, 8) // just take the top 8
        .map((k) => {
            var t = props.submissions.filter(t => t.id == k)[0]
            if (!t) return null;
            t['votes'] = votes.value[k].length
            return t
        }).filter(k => k != null);
    return top;
})

// Generate a list of the 3 users who've cast the most votes
const user_leaderboard = computed(() => {
    let vote_counts = {}
    // Compile votes into a { <user_id>:<n_votes> } dictionary
    for (const track_votes of Object.values(votes.value)) {
        for (const voter of track_votes) {
            if (voter in vote_counts) {
                vote_counts[voter] += 1;
            } else {
                vote_counts[voter] = 1;
            }
        }
    }
    // Attach to actual user objects
    return Object.keys(vote_counts)
        .sort((a, b) => vote_counts[b] - vote_counts[a])
        .splice(0, 5).map(user_id => {
            let voter = props.group_users.filter(u => u.id == user_id)[0]
            voter.votes = vote_counts[user_id];
            return voter;
        })
})

const accepted_songs_count = computed(() => {
    return Object.keys(votes.value).filter(t_id => votes.value[t_id].length >= props.episode.vote_min).length
})

const bangers_count = computed(() => {
    if (vote_max == 0) return 0;
    return Object.keys(votes.value).filter(t_id => votes.value[t_id].length >= vote_max.value).length
})

const vote_max = computed(() => {
    // Count the number of unique users present here
    let ids = []
    for (const track of props.submissions) {
        if (!ids.includes(track.added_by)) ids.push(track.added_by)
    }
    // the most votes a track can have is one less than the number of people voting
    // (can't vote for yourself)
    return ids.length - 1;
})
</script>

<style scoped lang="scss"></style>