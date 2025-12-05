<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    is_active: boolean;
    default_value: string | null;
    help_text: string | null;
}

interface Props {
    brand: Brand;
    list: EmailList;
    customField: CustomField;
    fieldTypes: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.customField.name,
    tag: props.customField.tag,
    type: props.customField.type,
    is_required: props.customField.is_required,
    is_active: props.customField.is_active,
    default_value: props.customField.default_value || '',
    help_text: props.customField.help_text || '',
});

const submit = () => {
    form.put(route('brands.lists.custom-fields.update', [props.brand.id, props.list.id, props.customField.id]));
};
</script>

<template>
    <Head :title="`Edit Custom Field - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Edit Custom Field</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ list.name }}
                    </p>
                </div>

                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Field Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Field Name <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Display name for this field (e.g., "Company Name", "Phone Number")
                            </p>
                        </div>

                        <!-- Field Tag -->
                        <div>
                            <label for="tag" class="block text-sm font-medium text-gray-700">
                                Tag <span class="text-red-600">*</span>
                            </label>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-gray-500">{</span>
                                <span class="text-gray-500">{</span>
                                <input
                                    id="tag"
                                    v-model="form.tag"
                                    type="text"
                                    pattern="[A-Z0-9_]+"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase"
                                    required
                                />
                                <span class="text-gray-500">}</span>
                                <span class="text-gray-500">}</span>
                            </div>
                            <p v-if="form.errors.tag" class="mt-1 text-sm text-red-600">{{ form.errors.tag }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Personalization tag for campaigns. Use uppercase letters, numbers, and underscores only (e.g., COMPANY_NAME)
                            </p>
                        </div>

                        <!-- Field Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Field Type <span class="text-red-600">*</span>
                            </label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required
                            >
                                <option v-for="(label, value) in fieldTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                        </div>

                        <!-- Default Value -->
                        <div>
                            <label for="default_value" class="block text-sm font-medium text-gray-700">
                                Default Value
                            </label>
                            <input
                                id="default_value"
                                v-model="form.default_value"
                                :type="form.type === 'number' ? 'number' : form.type === 'date' ? 'date' : 'text'"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p v-if="form.errors.default_value" class="mt-1 text-sm text-red-600">{{ form.errors.default_value }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Default value if subscriber doesn't have this field
                            </p>
                        </div>

                        <!-- Help Text -->
                        <div>
                            <label for="help_text" class="block text-sm font-medium text-gray-700">
                                Help Text
                            </label>
                            <textarea
                                id="help_text"
                                v-model="form.help_text"
                                rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                            <p v-if="form.errors.help_text" class="mt-1 text-sm text-red-600">{{ form.errors.help_text }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Help text to guide users when filling this field
                            </p>
                        </div>

                        <!-- Checkboxes -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input
                                    id="is_required"
                                    v-model="form.is_required"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="is_required" class="ml-2 block text-sm text-gray-900">
                                    Required field
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active (show in forms)
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            <Link
                                :href="route('brands.lists.custom-fields.index', [brand.id, list.id])"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else">Update Field</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
