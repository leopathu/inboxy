<script setup>
import BrandLayout from '@/Layouts/BrandLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    list: Object,
    brand: Object,
});

const form = useForm({
    name: props.list.name,
    optin_type: props.list.optin_type,
    thank_you_enabled: props.list.thank_you_enabled || false,
    thank_you_subject: props.list.thank_you_subject || '',
    thank_you_message: props.list.thank_you_message || '',
    confirmation_subject: props.list.confirmation_subject || '',
    confirmation_message: props.list.confirmation_message || '',
    unsubscribe_enabled: props.list.unsubscribe_enabled || false,
    goodbye_subject: props.list.goodbye_subject || '',
    goodbye_message: props.list.goodbye_message || '',
});

const submit = () => {
    form.put(route('brands.lists.update', { brand: props.brand.id, list: props.list.id }));
};
</script>

<template>
    <Head :title="`Edit List - ${list.name}`" />

    <BrandLayout :brand="brand">
        <div>
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">
                            Edit List
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Update list settings and email configurations
                        </p>
                    </div>
                    <Link
                        :href="route('brands.lists.index', brand.id)"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Back to Lists
                    </Link>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Settings</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    List Name
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    v-model="form.name"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label for="optin_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Opt-in Type
                                </label>
                                <select
                                    id="optin_type"
                                    v-model="form.optin_type"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="single">Single Opt-in</option>
                                    <option value="double">Double Opt-in</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thank You Email Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Thank You Email</h3>
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.thank_you_enabled"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Enable</span>
                            </label>
                        </div>

                        <div v-if="form.thank_you_enabled" class="space-y-4">
                            <div>
                                <label for="thank_you_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                    Subject
                                </label>
                                <input
                                    type="text"
                                    id="thank_you_subject"
                                    v-model="form.thank_you_subject"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Thank you for subscribing!"
                                />
                            </div>

                            <div>
                                <label for="thank_you_message" class="block text-sm font-medium text-gray-700 mb-1">
                                    Message
                                </label>
                                <textarea
                                    id="thank_you_message"
                                    v-model="form.thank_you_message"
                                    rows="6"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter your thank you message..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Double Opt-in Confirmation Settings -->
                <div v-if="form.optin_type === 'double'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Double Opt-in Confirmation</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="confirmation_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmation Email Subject
                                </label>
                                <input
                                    type="text"
                                    id="confirmation_subject"
                                    v-model="form.confirmation_subject"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Please confirm your subscription"
                                />
                            </div>

                            <div>
                                <label for="confirmation_message" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmation Email Message
                                </label>
                                <textarea
                                    id="confirmation_message"
                                    v-model="form.confirmation_message"
                                    rows="6"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter your confirmation message..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unsubscriber Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Unsubscriber Settings</h3>
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.unsubscribe_enabled"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Enable</span>
                            </label>
                        </div>

                        <div v-if="form.unsubscribe_enabled" class="space-y-4">
                            <div>
                                <label for="goodbye_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                    Goodbye Email Subject
                                </label>
                                <input
                                    type="text"
                                    id="goodbye_subject"
                                    v-model="form.goodbye_subject"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Sorry to see you go"
                                />
                            </div>

                            <div>
                                <label for="goodbye_message" class="block text-sm font-medium text-gray-700 mb-1">
                                    Goodbye Email Message
                                </label>
                                <textarea
                                    id="goodbye_message"
                                    v-model="form.goodbye_message"
                                    rows="6"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter your goodbye message..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-3">
                    <Link
                        :href="route('brands.lists.index', brand.id)"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </BrandLayout>
</template>
