<template>
	<div class="flex w-full gap-3 py-2 border-b border-dashed border-zinc-700 light:border-neutral-300 last:border-none">
		<div class="flex-0 w-10 h-10 rounded-full bg-zinc-800 light:bg-neutral-100 overflow-hidden">
			<img v-if="user.spotify_avatar" :src="user.spotify_avatar" alt="">
			<div v-else
				class="h-full w-full light:bg-neutral-200 bg-zinc-700 light:text-neutral-400 text-white/60 overflow-hidden flex items-center justify-center uppercase text-sm font-mono leading-none font-bold">
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
