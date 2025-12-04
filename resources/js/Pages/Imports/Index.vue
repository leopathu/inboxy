<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface EmailList {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface SubscriberImport {
    id: number;
    filename: string;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    total_rows: number;
    processed_rows: number;
    imported_count: number;
    skipped_count: number;
    error_count: number;
    progress_percentage: number;
    created_at: string;
    completed_at: string | null;
    user?: User;
}

interface PaginatedImports {
    data: SubscriberImport[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    brand: Brand;
    list: EmailList;
    imports: PaginatedImports;
}

const props = defineProps<Props>();

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        pending: 'Pending',
        processing: 'Processing',
        completed: 'Completed',
        failed: 'Failed',
    };
    return labels[status] || status;
};
</script>

<template>
    <Head :title="`Import History - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Import History</h2>
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
                            :href="route('brands.lists.subscribers.import', [brand.id, list.id])"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                        >
                            New Import
                        </Link>
                    </div>
                </div>

                <!-- Imports Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="imports.data.length > 0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Filename
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Progress
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Results
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="importItem in imports.data" :key="importItem.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ importItem.filename }}
                                            </div>
                                            <div v-if="importItem.user" class="text-xs text-gray-500">
                                                by {{ importItem.user.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    getStatusColor(importItem.status)
                                                ]"
                                            >
                                                {{ getStatusLabel(importItem.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ importItem.processed_rows }} / {{ importItem.total_rows }}
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                <div
                                                    class="bg-blue-600 h-1.5 rounded-full"
                                                    :style="{ width: `${importItem.progress_percentage || 0}%` }"
                                                ></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex gap-3">
                                                <span class="text-green-600">✓ {{ importItem.imported_count }}</span>
                                                <span class="text-yellow-600">⊘ {{ importItem.skipped_count }}</span>
                                                <span class="text-red-600">✗ {{ importItem.error_count }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(importItem.created_at).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('brands.lists.imports.show', [brand.id, list.id, importItem.id])"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div v-if="imports.last_page > 1" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ ((imports.current_page - 1) * imports.per_page) + 1 }}
                                    to {{ Math.min(imports.current_page * imports.per_page, imports.total) }}
                                    of {{ imports.total }} imports
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-if="imports.current_page > 1"
                                        :href="route('brands.lists.imports.index', { brand: brand.id, list: list.id, page: imports.current_page - 1 })"
                                        class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="imports.current_page < imports.last_page"
                                        :href="route('brands.lists.imports.index', { brand: brand.id, list: list.id, page: imports.current_page + 1 })"
                                        class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                            </div>
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No imports yet</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by importing subscribers from a CSV file.
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('brands.lists.subscribers.import', [brand.id, list.id])"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Import Subscribers
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
