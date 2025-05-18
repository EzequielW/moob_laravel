<script lang="ts" setup>
import { Column } from '@/types';

const props = defineProps<{
    columns: Column[];
    rows: Array<Record<string, any>>;
}>();
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-gray-300 dark:border-gray-600">
        <table class="w-full text-sm text-left border-collapse separate border-spacing-0">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <tr>
                    <th v-for="(col, index) in columns" :key="col.key" :class="[
                        'px-4 py-2 border-b border-gray-300 dark:border-gray-600 font-medium',
                        index === 0 ? 'rounded-tl-lg' : '',
                        index === columns.length - 1 ? 'rounded-tr-lg' : '',
                    ]">
                        {{ col.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, rowIndex) in rows" :key="rowIndex"
                    class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <td v-for="(col, colIndex) in columns" :key="col.key" :class="[
                        'px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-100',
                        colIndex === 0 ? (rowIndex === rows.length - 1 ? 'rounded-bl-lg' : '') : '',
                        colIndex === columns.length - 1 ? (rowIndex === rows.length - 1 ? 'rounded-br-lg' : '') : '',
                        colIndex !== columns.length - 1 ? 'border-r border-gray-200 dark:border-gray-600' : '',
                    ]">
                        <slot :name="`cell-${String(col.key)}`" :row="row" :value="row[String(col.key)]">
                            {{ row[String(col.key)] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
