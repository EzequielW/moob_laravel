<script setup lang="ts">
import { computed, PropType, ref } from 'vue'
import { ChartStats } from '@/types'
import { capitalizeFirstLetter } from '@/Utils/string';

const props = defineProps({
	title: {
		type: String,
		required: true
	},
	stats: {
		type: Object as PropType<ChartStats>,
		required: true
	}
})

const isDarkMode = ref(window.matchMedia('(prefers-color-scheme: dark)').matches);

const chartOptions = computed(() => {
	return {
		chart: {
			type: 'bar',
			background: 'transparent'
		},
		xaxis: {
			categories: props.stats.labels.map(label => capitalizeFirstLetter(label))
		},
		title: {
			text: props.title
		},
		plotOptions: {
			bar: {
				columnWidth: '45%',
				distributed: true,
				colors: {
					backgroundBarOpacity: 0.5
				}
			}
		},
		dataLabels: {
			enabled: false
		},
		legend: {
			show: false
		},
		theme: {
			mode: isDarkMode.value ? 'dark' : 'light'
		},
		tooltip: {
			theme: isDarkMode.value ? 'dark' : 'light'
		}
	}
})

const series = computed(() => {
	return [
		{
			name: props.title,
			data: props.stats.data
		}
	]
})

</script>

<template>
	<apexcharts type="bar" :options="chartOptions" :series="series" height="500" />
</template>