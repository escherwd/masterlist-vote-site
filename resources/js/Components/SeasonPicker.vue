<template>
	<div class="flex gap-3 my-2">
	            <Link :href="`/${slug}`" class="button" :class="!props.season_id ? 'selected' : 'unselected'">All</Link>
	            <Link :href="`/${slug}/${season.id}`" v-for="season of seasons" :key="season.id" class="button unselected"
	                :class="props.season_id == season.id ? 'selected' : 'unselected'">Season {{ season.number }}</Link>
	        </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
	seasons: Array,
	season_id: String,
	slug: String
})
</script>

