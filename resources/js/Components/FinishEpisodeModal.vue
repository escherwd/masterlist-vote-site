<template>
    <div class="fixed top-0 left-0 right-0 bottom-0 w-screen h-screen bg-black/30 z-20 px-4 py-8 backdrop-blur">
        <div class="border light:bg-white border-zinc-800 light:border-neutral-200 bg-zinc-900 mx-auto max-w-xl shadow-lg">
            <div class="text-xl px-4 py-3 border-b border-zinc-800 light:border-neutral-200">
                Are You Sure?
            </div>
            <div class="p-4 light:text-black/80 text-white/70 border-b border-zinc-800 light:border-neutral-200">
                <span class="text-primary light:text-secondary font-mono font-bold">{{ counts[0] }}</span> song(s) will be added to the master list, 
                <span class="text-primary light:text-secondary font-mono font-bold">{{ counts[1] }}</span> song(s) will be added to the certified bangers playlist, and
                <span class="text-primary light:text-secondary font-mono font-bold">{{ counts[2] }}</span> song(s) will be deleted from the submissions playlist. 

                This action cannot be undone.

                <div class="mt-6">
                    <label for="consent" class="mr-2">I Understand</label>
                <input type="checkbox" v-model="consent" name="consent" id="consent">
                </div>
            </div>
            <div class="px-4 py-3 flex justify-between">
                <button @click="emit('close')" class="button hover:bg-zinc-800 light:hover:bg-neutral-100">Cancel</button>
                <button :disabled="!consent" @click="finishEpisode" class="button bg-green-600 text-white/90 hover:bg-green-600/80 disabled:text-white/30 disabled:bg-green-600/50">
                    <ArrowPathIcon v-if="loading" class="animate-spin" />
                    <CheckIcon v-else />
                    Finish {{ props.finish_season ? "Season" : "Episode" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowPathIcon, CheckIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    episode: Object,
    finish_season: Boolean,
    counts: Array // [masterlist, bangers, total]
})
const emit = defineEmits(['close'])

const consent = ref(false);
const loading = ref(false);

function finishEpisode() {
    loading.value = true;
    useForm({
        finish_season: props.finish_season
    }).post(`/api/episode/${props.episode.id}/finish`, {
        onFinish: () => {
            loading.value = false;
        },
        onSuccess: () => {
            emit('close');
        }
    })
}

</script>