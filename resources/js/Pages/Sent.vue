<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { Message, Pagination } from '@/types';
import Table from '@/Components/Table.vue';
import { capitalizeFirstLetter } from '@/Utils/string';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps<{
    messages: Pagination<Message>
}>()

function changePage(page: number) {
    router.get(route('messages.sent'), { page }, { preserveScroll: true })
}
</script>

<template>

    <Head title="Sent" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Sent
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="space-y-6 bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <Table :columns="[
                        { key: 'created_at', label: 'Date' },
                        { key: 'platform', label: 'Platform' },
                        { key: 'recipient', label: 'Recipient' },
                        { key: 'content', label: 'Message' },
                        { key: 'attachment_url', label: 'Attachment' }
                    ]" :rows="messages.data">
                        <template #cell-created_at="{ value }">
                            {{ new Date(value).toLocaleString() }}
                        </template>

                        <template #cell-platform="{ value }">
                            {{ capitalizeFirstLetter(value) }}
                        </template>

                        <template #cell-attachment_url="{ value }">
                            <a v-if="value" :href="value" target="_blank"
                                class="text-blue-600 hover:text-blue-800 underline">View</a>
                        </template>
                    </Table>

                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <PrimaryButton v-if="messages.prev_page_url" @click="changePage(messages.current_page - 1)">Previous</PrimaryButton>
                            <PrimaryButton v-if="messages.next_page_url" @click="changePage(messages.current_page + 1)">Next</PrimaryButton>
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Page {{ messages.current_page }} of {{ messages.last_page }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
