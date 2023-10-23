<template>
	<div class="flex gap-3 my-2">
	            <Link :href="`/${slug}/${season_id}`" class="button" :class="!props.episode_id ? 'selected' : 'unselected'">All</Link>
	            <Link :href="`/${slug}/${season_id}/${episode.id}`" v-for="episode of episodes" :key="episode.id" class="button unselected"
	                :class="props.episode_id == episode.id ? 'selected' : 'unselected'">Episode {{ episode.number }}</Link>
	        </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
	episodes: Array,
	episode_id: String,
	season_id: String,
	slug: String
})
</script>

