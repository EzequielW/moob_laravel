<script setup lang="ts">
import BarChart from '@/Components/Charts/BarChart.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ChartStats } from '@/types';
import { Head } from '@inertiajs/vue3';
import { PropType } from 'vue';

const props = defineProps({
  stats: {
    type: Object as PropType<ChartStats>,
    required: true
  }
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <BarChart title="Messages per Platform" :stats="props.stats" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
