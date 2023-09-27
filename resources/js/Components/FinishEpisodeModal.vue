<template>
    <div class="fixed top-0 left-0 right-0 bottom-0 w-screen h-screen bg-black/30 z-20 px-4 py-8 backdrop-blur">
        <div class="border border-zinc-800 bg-zinc-900 mx-auto max-w-xl shadow-lg">
            <div class="text-xl px-4 py-3 border-b border-zinc-800">
                Are You Sure?
            </div>
            <div class="p-4 text-white/70 border-b border-zinc-800">
                <span class="text-primary font-mono">{{ counts[0] }}</span> song(s) will be added to the master list, 
                <span class="text-primary font-mono">{{ counts[1] }}</span> song(s) will be added to the certified bangers playlist, and
                <span class="text-primary font-mono">{{ counts[2] }}</span> song(s) will be deleted from the submissions playlist. 

                This action cannot be undone.

                <div class="mt-6">
                    <label for="consent" class="mr-2">I Understand</label>
                <input type="checkbox" v-model="consent" name="consent" id="consent">
                </div>
            </div>
            <div class="px-4 py-3 flex justify-between">
                <button @click="emit('close')" class="button">Cancel</button>
                <button :disabled="!consent" class="button bg-primary text-black/90 disabled:bg-primary/50">Finish Episode</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    counts: Array // [masterlist, bangers, total]
})
const emit = defineEmits(['close'])

const consent = ref(false);


</script>