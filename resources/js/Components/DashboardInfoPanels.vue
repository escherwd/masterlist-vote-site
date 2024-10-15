<template>
	<div class="grid grid-cols-2 gap-2 light:text-neutral-600">
	                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4 col-span-2">
	                        <div class="card-label">JURY</div>
	                        <div class=" font-medium">{{ group.name }}</div>
	                    </div>
	                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4">
	                        <div class="card-label">SEASON</div>
	                        <div class="font-mono text-2xl">{{ `${season.number}`.padStart(2, '0') }}</div>
	                    </div>
	                    <div class="bg-zinc-800 light:bg-neutral-100 py-3 px-4">
	                        <div class="card-label">EPISODE</div>
	                        <div class="font-mono text-2xl">{{ `${episode.number}`.padStart(2, '0') }}</div>
	                    </div>
	                    <div class="bg-zinc-800 light:bg-neutral-100 py-4 px-4 col-span-2">
	                        <div class="card-label">THEME</div>
	                        <textarea
	                            class="leading-snug text-white/60 light:text-neutral-600 w-full bg-transparent resize-none p-0 border-0 m-0 focus:outline-primary focus:ring-0"
	                            v-model="editEpisode.theme" placeholder="No Theme Yet." name="episode_theme" rows="2"
	                            @keydown.enter.native.prevent="themeSubmit">
	
	                        </textarea>
	                        <div class="text-xs text-white/30 light:text-neutral-400 italic">
	                            <span v-if="themeNeedsSave" class="animate-pulse">hit enter to save</span>
	                            <span v-else>click to edit</span>
	                        </div>
	                    </div>
	                </div>
</template>

<script setup lang="ts">
defineProps<{ 
	group: any;
	season: any;
	episode: any;
	themeSubmit: any;
	editEpisode: any;
	themeNeedsSave: any;
}>()
</script>
