<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref, watch, computed } from 'vue'
import TextAreaInput from '@/Components/TextAreaInput.vue'

type MessageForm = {
    platform: string
    recipients: string[]
    content: string
    attachment: File | null
}

const recipientsRaw = ref<string>('')
const successMessage = ref<string>('')

const form = useForm<MessageForm>({
    platform: '',
    recipients: [],
    content: '',
    attachment: null,
})

const formatHint = computed(() => {
    switch (form.platform) {
        case 'telegram':
            return 'Use @username';
        case 'whatsapp':
            return 'Use international phone number (e.g., +1234567890)';
        case 'discord':
            return 'Use username#1234';
        case 'slack':
            return 'Use email@domain.com';
        default:
            return '';
    }
});

watch(recipientsRaw, (val: string) => {
    form.recipients = val
        .split('\n')
        .map((r) => r.trim())
        .filter((r) => r.length > 0)
})

function handleFile(e: Event) {
    const target = e.target as HTMLInputElement
    if (target.files?.length) {
        form.attachment = target.files[0]
    }
}

function submit() {
    form.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('recipients', 'content', 'attachment')
            recipientsRaw.value = ''
            successMessage.value = 'Message is being processed.'
        },
        onError: () => {
            successMessage.value = ''
        }
    })
}
</script>

<template>

    <Head title="Send" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Send
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <section class="max-w-xl">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="platform" value="Platform" />
                                
                                <select v-model="form.platform" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" required>
                                    <option value="">Select platform</option>
                                    <option value="telegram">Telegram</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="discord">Discord</option>
                                    <option value="slack">Slack</option>
                                </select>

                                <InputError :message="form.errors.platform" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="recipients" value="Recipients (one per line)" />

                                <TextAreaInput class="mt-1 block w-full" v-model="recipientsRaw" rows="4" />
                                <p class="text-sm text-gray-500 mt-1">{{ formatHint }}</p>
                                
                                <InputError :message="form.errors.recipients" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="message" value="Message" />
                                <TextAreaInput class="mt-1 block w-full" v-model="form.content" rows="4" required />
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="attachment" value="Attachment (optional)" />
                                <input type="file" @change="handleFile" class="py-3 px-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
                                <InputError :message="form.errors.attachment" class="mt-2" />
                            </div>

                            <div>
                                <PrimaryButton :disabled="form.processing">Send</PrimaryButton>
                            </div>
                        </form>

                        <p v-if="successMessage" class="mt-4 text-green-600 dark:text-green-400">
                            {{ successMessage }}
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
