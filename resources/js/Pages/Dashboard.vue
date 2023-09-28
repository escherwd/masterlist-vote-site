
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <FinishEpisodeModal v-if="showFinishEpisodeModal"
            :counts="[accepted_songs_count, bangers_count, submissions.length]" @close="showFinishEpisodeModal = false" />

        <div class="flex flex-col md:flex-row items-start gap-x-4 w-full relative gap-y-12">
            <div class="flex-1 min-w-0 max-w-full w-full">
                <template v-for="track in submissions">
                    <Track :track="track" :vote_count="votes[track.id]?.length ?? 0" :vote_max="vote_max"
                        :vote_threshold="episode.vote_min" :disable_vote="track.added_by == user.spotify_id"
                        :voted="(votes[track.id] ?? []).includes(user.id)" :user="user_for(track.added_by)" @vote="vote" />
                </template>
                <div v-if="submissions.length == 0" class="text-center py-8 text-white/40 uppercase text-sm tracking-wider">
                    <FaceFrownIcon :class="{ 'animate-pulse': isRefreshing }" class="h-6 w-6 mx-auto" />
                    <span class="block mt-2">No submissions yet.</span>
                    <button @click="refreshTracks" class="block mt-2 uppercase text-xs tracking-wide font-medium hover:text-white mx-auto">Refresh Tracks</button>
                </div>
            </div>
            <div class="flex-grow-0 min-w-0 flex-shrink-0 w-full md:w-64 ">
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-zinc-800 py-3 px-4 col-span-2">
                        <div class="card-label">JURY</div>
                        <div class=" font-medium">{{ group.name }}</div>
                    </div>
                    <div class="bg-zinc-800 py-3 px-4">
                        <div class="card-label">SEASON</div>
                        <div class="font-mono text-2xl">{{ `${season.number}`.padStart(2, '0') }}</div>
                    </div>
                    <div class="bg-zinc-800 py-3 px-4">
                        <div class="card-label">EPISODE</div>
                        <div class="font-mono text-2xl">{{ `${episode.number}`.padStart(2, '0') }}</div>
                    </div>
                    <div class="bg-zinc-800 py-4 px-4 col-span-2">
                        <div class="card-label">THEME</div>
                        <div class="leading-snug text-white/60">{{ episode.theme }}</div>
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-zinc-800 py-3 px-4">
                        <div class="card-label">APPROVED</div>
                        <div class="font-mono text-2xl">
                            <span>{{ accepted_songs_count }}</span>
                            <span class="text-base text-white/60">/{{ submissions.length }}</span>
                        </div>
                    </div>
                    <div class="bg-zinc-800 py-3 px-4">
                        <div class="card-label">BANGERS</div>
                        <div class="font-mono text-2xl">{{ bangers_count }}</div>
                    </div>
                    <div class="bg-zinc-800 py-3 px-4 col-span-2">
                        <div class="card-label">TOP TRACKS</div>
                        <div class="mt-3">
                            <div v-for="track, idx in leaderboard" :key="track.id"
                                class="flex py-1 gap-2 border-b items-center border-zinc-700 last:border-none">
                                <div class="w-6 h-6 bg-slate-700">
                                    <img :src="track.album_cover" alt="">
                                </div>
                                <div class="flex-1 text-sm font-medium min-w-0 ellipsis">
                                    {{ track.title }}
                                </div>
                                <div class="font-mono text-xs font-semibold text-primary">
                                    {{ track.votes }} VOTES
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-zinc-800 py-3 px-4 col-span-2">
                        <div class="card-label">TOP VOTERS</div>
                        <div class="mt-3">
                            <div v-for="voter, idx in user_leaderboard" :key="voter.id"
                                class="flex py-1 gap-2 border-b items-center border-zinc-700 last:border-none">
                                <div class="w-6 h-6 bg-slate-700 rounded-full overflow-hidden">
                                    <img v-if="voter.spotify_avatar" :src="voter.spotify_avatar" alt="">
                                </div>
                                <div class="flex-1 text-sm font-medium min-w-0 ellipsis">
                                    {{ voter.name }}
                                </div>
                                <div class="font-mono text-xs font-semibold text-primary">
                                    {{ voter.votes }} VOTES
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider my-4"></div>

                <div class="grid grid-cols-2 gap-2">
                    <button @click="refreshTracks"
                        class="col-span-2 button w-full bg-primary hover:bg-primary/80 text-black/90">
                        <ArrowPathIcon :class="{ 'animate-spin': isRefreshing }" />
                        <span>Refresh Tracks</span>
                    </button>

                    <button @click="showFinishEpisodeModal = true"
                        class="col-span-2 button w-full bg-green-600 hover:bg-green-600/80">
                        <CheckIcon />
                        <span>Finish Episode</span>
                    </button>

                    <div class="col-span-2 p-2 text-center text-sm text-white/60">
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
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import Track from '@/Components/Track.vue';
import { computed, onBeforeMount, ref } from 'vue';
import { ArrowPathIcon, CheckIcon } from "@heroicons/vue/20/solid"
import { FaceFrownIcon } from '@heroicons/vue/24/solid'
import FinishEpisodeModal from "@/Components/FinishEpisodeModal.vue";

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

// Grab a user object based on a spotify_id
function user_for(spotify_id) {
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
            location.reload();
        },
    });
}

// Generate a shortlist of the top tracks
const leaderboard = computed(() => {
    let top = Object.keys(votes.value)
        .filter((k) => votes.value[k].length != 0) // remove songs with no votes
        .sort((a, b) => votes.value[b].length - votes.value[a].length)
        .splice(0, 5) // just take the top 5
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
        .splice(0, 3).map(user_id => {
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
    return Object.keys(votes.value).filter(t_id => votes.value[t_id].length >= vote_max).length
})

var vote_max = 0;
onBeforeMount(() => {
    // Count the number of unique users present here
    let ids = []
    for (const track of props.submissions) {
        if (!ids.includes(track.added_by)) ids.push(track.added_by)
    }
    // the most votes a track can have is one less than the number of people voting
    // (can't vote for yourself)
    vote_max = ids.length - 1;
})
</script>

<style scoped lang="scss"></style>