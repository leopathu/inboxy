<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
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
    errors: Array<{ row: number; email: string; error: string }> | null;
    error_message: string | null;
    progress_percentage: number;
    created_at: string;
    started_at: string | null;
    completed_at: string | null;
    list?: EmailList;
    user?: User;
}

interface Props {
    brand: Brand;
    list: EmailList;
    import: SubscriberImport;
}

const props = defineProps<Props>();

const importData = ref<SubscriberImport>(props.import);
let pollInterval: number | null = null;

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

const isActive = () => {
    return importData.value.status === 'pending' || importData.value.status === 'processing';
};

const pollImportStatus = () => {
    router.reload({
        only: ['import'],
        onSuccess: (page: any) => {
            importData.value = page.props.import;
            
            // Stop polling if no longer active
            if (!isActive() && pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        },
    });
};

const startPolling = () => {
    if (isActive() && !pollInterval) {
        pollInterval = window.setInterval(pollImportStatus, 3000); // Poll every 3 seconds
    }
};

onMounted(() => {
    startPolling();
});

onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});
</script>

<template>
    <Head :title="`Import Details - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Import Details</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ list.name }}
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('brands.lists.imports.index', [brand.id, list.id])"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        >
                            Back to Imports
                        </Link>
                        <Link
                            :href="route('brands.lists.show', [brand.id, list.id])"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        >
                            Back to List
                        </Link>
                    </div>
                </div>

                <!-- Import Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-medium text-gray-900">
                                    {{ importData.filename }}
                                </h3>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <p>Started: {{ new Date(importData.created_at).toLocaleString() }}</p>
                                    <p v-if="importData.started_at">Processing started: {{ new Date(importData.started_at).toLocaleString() }}</p>
                                    <p v-if="importData.completed_at">Completed: {{ new Date(importData.completed_at).toLocaleString() }}</p>
                                    <p v-if="importData.user">Imported by: {{ importData.user.name }}</p>
                                </div>
                            </div>
                            <span
                                :class="[
                                    'px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full',
                                    getStatusColor(importData.status)
                                ]"
                            >
                                {{ getStatusLabel(importData.status) }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div v-if="isActive()" class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Progress</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ (importData.progress_percentage || 0).toFixed(1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div
                                    class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                                    :style="{ width: `${importData.progress_percentage || 0}%` }"
                                ></div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                Processing row {{ importData.processed_rows }} of {{ importData.total_rows }}
                            </p>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ importData.total_rows.toLocaleString() }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">Total Rows</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-3xl font-bold text-blue-900">
                                    {{ importData.processed_rows.toLocaleString() }}
                                </div>
                                <div class="text-sm text-blue-600 mt-1">Processed</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-3xl font-bold text-green-900">
                                    {{ importData.imported_count.toLocaleString() }}
                                </div>
                                <div class="text-sm text-green-600 mt-1">Imported</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="text-3xl font-bold text-yellow-900">
                                    {{ importData.skipped_count.toLocaleString() }}
                                </div>
                                <div class="text-sm text-yellow-600 mt-1">Skipped</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                                <div class="text-3xl font-bold text-red-900">
                                    {{ importData.error_count.toLocaleString() }}
                                </div>
                                <div class="text-sm text-red-600 mt-1">Errors</div>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <div v-if="importData.status === 'completed'" class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">Import completed successfully!</h4>
                                    <p class="text-sm text-green-700 mt-1">
                                        Successfully imported {{ importData.imported_count }} subscribers.
                                        {{ importData.skipped_count > 0 ? `Skipped ${importData.skipped_count} duplicate(s).` : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="importData.status === 'failed'" class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-red-800">Import failed</h4>
                                    <p v-if="importData.error_message" class="text-sm text-red-700 mt-1">
                                        {{ importData.error_message }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Details -->
                <div v-if="importData.errors && importData.errors.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Error Details ({{ importData.errors.length }})
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Row
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Error
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(error, index) in importData.errors" :key="index">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ error.row }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ error.email }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-600">
                                            {{ error.error }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
