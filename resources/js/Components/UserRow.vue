<template>
	<div class="flex w-full gap-3 py-2 border-b border-dashed border-zinc-700 last:border-none">
		<div class="flex-0 w-10 h-10 rounded-full bg-zinc-800 overflow-hidden">
			<img v-if="user.spotify_avatar" :src="user.spotify_avatar" alt="">
			<div v-else
				class="h-full w-full bg-zinc-700 text-white/60 overflow-hidden flex items-center justify-center uppercase text-sm font-mono leading-none font-bold">
				{{ user.name.substring(0, 1) }}
			</div>
		</div>
		<div class="flex-1 flex items-center font-medium">
			{{ user.name }}
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	user: Object
})
</script>
