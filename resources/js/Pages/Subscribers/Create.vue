<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface EmailList {
    id: number;
    name: string;
}

interface CustomField {
    id: number;
    name: string;
    tag: string;
    type: string;
    is_required: boolean;
    help_text: string | null;
    options: string[] | null;
}

interface Props {
    brand: Brand;
    list: EmailList;
    customFields: CustomField[];
}

const props = defineProps<Props>();

const { brand, list, customFields } = props;

const form = useForm({
    email: '',
    first_name: '',
    last_name: '',
    status: 'subscribed',
    custom_fields: {} as Record<string, string>,
});

const submit = () => {
    form.post(route('brands.lists.subscribers.store', [brand.id, list.id]), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Add Subscriber - ${list.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add Subscriber</h2>
                        <p class="mb-6 text-sm text-gray-600">
                            Adding to list: <strong>{{ list.name }}</strong>
                        </p>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                    autofocus
                                />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">
                                    First Name
                                </label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.first_name }}
                                </p>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">
                                    Last Name
                                </label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.last_name }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="subscribed">Subscribed (Active)</option>
                                    <option value="unconfirmed">Unconfirmed (Pending Confirmation)</option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    Choose "Unconfirmed" if subscriber needs to confirm via email
                                </p>
                                <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <!-- Custom Fields -->
                            <div v-if="customFields.length > 0" class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Custom Fields</h3>
                                
                                <div class="space-y-4">
                                    <div v-for="field in customFields" :key="field.id">
                                        <label :for="`custom_${field.tag}`" class="block text-sm font-medium text-gray-700">
                                            {{ field.name }}
                                            <span v-if="field.is_required" class="text-red-500">*</span>
                                        </label>
                                        
                                        <!-- Text/Number/Date Input -->
                                        <input
                                            v-if="['text', 'number', 'date'].includes(field.type)"
                                            :id="`custom_${field.tag}`"
                                            v-model="form.custom_fields[field.tag]"
                                            :type="field.type"
                                            :required="field.is_required"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        
                                        <!-- Dropdown -->
                                        <select
                                            v-else-if="field.type === 'dropdown'"
                                            :id="`custom_${field.tag}`"
                                            v-model="form.custom_fields[field.tag]"
                                            :required="field.is_required"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">Select...</option>
                                            <option v-for="option in field.options" :key="option" :value="option">
                                                {{ option }}
                                            </option>
                                        </select>
                                        
                                        <!-- Checkbox -->
                                        <div v-else-if="field.type === 'checkbox'" class="mt-1">
                                            <label class="inline-flex items-center">
                                                <input
                                                    :id="`custom_${field.tag}`"
                                                    v-model="form.custom_fields[field.tag]"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">{{ field.help_text || 'Yes' }}</span>
                                            </label>
                                        </div>
                                        
                                        <p v-if="field.help_text && field.type !== 'checkbox'" class="mt-1 text-sm text-gray-500">
                                            {{ field.help_text }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                                <a
                                    :href="route('brands.lists.show', [brand.id, list.id])"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Adding...' : 'Add Subscriber' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
