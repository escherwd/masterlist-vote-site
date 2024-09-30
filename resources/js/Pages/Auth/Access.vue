<script setup>
import { ArrowRightOnRectangleIcon } from '@heroicons/vue/20/solid';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    error: String,
})

console.log(props);

const form = reactive({
  access_code: null
})

function submit() {
  router.post('/access', form)
}

</script>

<template>

    <Head title="Create Account" />

    <div class="fixed h-screen w-screen flex items-center justify-center">
        <div class="max-w-sm w-full px-8">
            <div class="mb-8"><img class="w-56 mx-auto" :src="error ? '/images/wrong.jpg' : '/images/hmm.jpg'" alt="hmmm" srcset=""></div>
            <span v-if="error" class="card-label float-right !text-red-500 uppercase">{{ error }}</span>
            <div class="card-label">ACCESS CODE</div>
            <input v-model="form.access_code" :class="{ 'border-red-500':error }" class="mt-2 w-full uppercase text-center text-sm bg-zinc-800 text-white/90 p-2 border-zinc-700"
                type="text" autofocus>
            <button :disabled="(form.access_code ?? '') == ''" @click="submit" class="disabled:opacity-10 disabled:select-none disabled:hover:bg-primary mt-4 button w-full bg-primary hover:bg-primary/80 text-black/90">
                <ArrowRightOnRectangleIcon />
                <span>Verify</span>
            </button>
        </div>
    </div>

</template>
