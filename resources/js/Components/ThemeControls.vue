<script setup>
// defineProps<{ 
// 	cycleColorTheme: any;
// 	cycleTheme: any;
// }>()
// const theme = defineModel<any>('theme', { required: true })

import { SunIcon, MoonIcon, ComputerDesktopIcon } from "@heroicons/vue/20/solid"
import { ref } from 'vue';
import colors from 'tailwindcss/colors';
import ThemeControls from '../Components/ThemeControls.vue'

const theme = ref(localStorage.getItem('theme') ?? 'auto')
const colorTheme = ref(localStorage.getItem('colorTheme') ?? 'default')

const colorThemes = {
	"default": [colors.yellow[400], colors.orange[500]],
	"pink": [colors.pink[400], colors.pink[600]],
	"blue": [colors.blue[300], colors.blue[600]],
	"green": [colors.green[400], colors.green[600]],
	"purple": [colors.purple[400], colors.purple[600]],
	"teal": [colors.teal[400], colors.teal[600]],
	"red": [colors.red[400], colors.red[500]],
	"fuchsia": [colors.fuchsia[300], colors.fuchsia[600]],
	"indigo": [colors.indigo[300], colors.indigo[600]],
	"bw": [colors.white, colors.black],
}

const setColorTheme = () => {
	// Set the values
	if (colorThemes[colorTheme.value] == undefined)
		colorTheme.value = 'default'
	document.body.style.setProperty('--primary', colorThemes[colorTheme.value][0])
	document.body.style.setProperty('--secondary', colorThemes[colorTheme.value][1])
}

const cycleColorTheme = () => {
	// Choose the next theme
	let keys = Object.keys(colorThemes)
	let currentThemeIndex = keys.indexOf(colorTheme.value)
	colorTheme.value = (currentThemeIndex == keys.length - 1)
		? keys[0] : keys[currentThemeIndex + 1]
	// Set the theme in local storage
	localStorage.setItem('colorTheme', colorTheme.value)
	// Set the values
	document.body.style.setProperty('--primary', colorThemes[colorTheme.value][0])
	document.body.style.setProperty('--secondary', colorThemes[colorTheme.value][1])
	// Set
	setColorTheme()
}

const systemDark = () => window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches

// Check for theme on page load
if (theme.value === 'light' || (theme.value === 'auto' && !systemDark())) {
	document.body.classList.add('light-mode');
}
// Set the colortheme too
setColorTheme()

const setAutoTheme = () => {
	if (systemDark())
		document.body.classList.remove('light-mode')
	else
		document.body.classList.add('light-mode')
}

const cycleTheme = () => {

	switch (theme.value) {
		case 'dark': // go from dark to auto
			setAutoTheme()
			theme.value = 'auto'
			localStorage.setItem('theme', 'auto')
			break;
		case 'auto': // go from auto to light
			document.body.classList.add('light-mode')
			theme.value = 'light'
			localStorage.setItem('theme', 'light')
			break;
		case 'light': // go from light to dark
			document.body.classList.remove('light-mode')
			theme.value = 'dark'
			localStorage.setItem('theme', 'dark')
			break;
		default:
			break;
	}
}

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
	if (theme.value === 'auto') {
		setAutoTheme()
	}
})

</script>

<template>
	<span>
		<a href="#" @click="cycleColorTheme"
			class="inline-block h-[14px] w-[24px] mr-2 -mb-[3px] rounded-full bg-primary light:bg-secondary border-zinc-600 light:border-neutral-200"></a>
		<a href="#" @click="cycleTheme" class="text-primary light:text-secondary hover:underline">
			<SunIcon v-if="theme == 'light'" class="h-4 w-4 inline-block" />
			<ComputerDesktopIcon v-else-if="theme == 'auto'" class="h-4 w-4 inline-block" />
			<MoonIcon v-else class="h-4 w-4 inline-block" />
		</a>
	</span>
</template>
