<template>
    <Head title="History" />

    <AuthenticatedLayout>

        <!-- season picker -->
        <div class="flex">
            <button v-for="season of seasons" class="button unselected">Season {{ season.number }}</button>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    seasons: Array,
})


</script>