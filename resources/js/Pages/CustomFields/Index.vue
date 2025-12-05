<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
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
    sort_order: number;
    help_text: string | null;
    default_value: string | null;
}

interface Props {
    brand: Brand;
    list: EmailList;
    customFields: CustomField[];
    fieldTypes: Record<string, string>;
}

const props = defineProps<Props>();

const getTypeLabel = (type: string) => {
    return props.fieldTypes[type] || type;
};

const getTypeBadgeColor = (type: string) => {
    const colors: Record<string, string> = {
        text: 'bg-blue-100 text-blue-800',
        number: 'bg-green-100 text-green-800',
        date: 'bg-purple-100 text-purple-800',
        dropdown: 'bg-yellow-100 text-yellow-800',
        checkbox: 'bg-pink-100 text-pink-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const deleteField = (field: CustomField) => {
    if (confirm(`Are you sure you want to delete "${field.name}"? This will remove the field data from all subscribers.`)) {
        router.delete(route('brands.lists.custom-fields.destroy', [props.brand.id, props.list.id, field.id]));
    }
};

const copyTag = (tag: string) => {
    navigator.clipboard.writeText(`{{${tag}}}`);
};
</script>

<template>
    <Head :title="`Custom Fields - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Custom Fields</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ list.name }}
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('brands.lists.show', [brand.id, list.id])"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        >
                            Back to List
                        </Link>
                        <Link
                            :href="route('brands.lists.custom-fields.create', [brand.id, list.id])"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        >
                            Add Custom Field
                        </Link>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Personalization Tags</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Use these tags in your campaigns to personalize content. Available built-in tags: 
                                <code class="bg-blue-100 px-1 rounded">&#123;&#123;name&#125;&#125;</code>,
                                <code class="bg-blue-100 px-1 rounded">&#123;&#123;email&#125;&#125;</code>,
                                <code class="bg-blue-100 px-1 rounded">&#123;&#123;first_name&#125;&#125;</code>,
                                <code class="bg-blue-100 px-1 rounded">&#123;&#123;last_name&#125;&#125;</code>. 
                                Create custom fields below for additional personalization tags.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Custom Fields Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="customFields.length > 0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name / Tag
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="field in customFields" :key="field.id">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ field.name }}
                                                <span v-if="field.is_required" class="text-red-600 ml-1">*</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">
                                                    &#123;&#123;{{ field.tag }}&#125;&#125;
                                                </code>
                                                <button
                                                    @click="copyTag(field.tag)"
                                                    class="text-xs text-blue-600 hover:text-blue-800"
                                                    title="Copy tag"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div v-if="field.help_text" class="text-xs text-gray-500 mt-1">
                                                {{ field.help_text }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    getTypeBadgeColor(field.type)
                                                ]"
                                            >
                                                {{ getTypeLabel(field.type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    field.is_active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                ]"
                                            >
                                                {{ field.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ field.sort_order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('brands.lists.custom-fields.edit', [brand.id, list.id, field.id])"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteField(field)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No custom fields</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by creating your first custom field for personalization.
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('brands.lists.custom-fields.create', [brand.id, list.id])"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Add Custom Field
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
