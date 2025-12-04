<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface EmailList {
    id: number;
    name: string;
}

interface Subscriber {
    id: number;
    email: string;
    first_name: string | null;
    last_name: string | null;
    status: string;
    subscribed_at: string | null;
    created_at: string;
}

interface PaginatedSubscribers {
    data: Subscriber[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    brand: Brand;
    list: EmailList;
    subscribers: PaginatedSubscribers;
}

const props = defineProps<Props>();

const { brand, list, subscribers } = props;

const selectedIds = ref<number[]>([]);
const showDeleteConfirm = ref(false);
const searchQuery = ref('');
const statusFilter = ref('all');

const allSelected = computed(() => {
    return subscribers.data.length > 0 && selectedIds.value.length === subscribers.data.length;
});

const someSelected = computed(() => {
    return selectedIds.value.length > 0 && !allSelected.value;
});

const toggleAll = () => {
    if (allSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = subscribers.data.map(s => s.id);
    }
};

const toggleSelection = (id: number) => {
    const index = selectedIds.value.indexOf(id);
    if (index === -1) {
        selectedIds.value.push(id);
    } else {
        selectedIds.value.splice(index, 1);
    }
};

const bulkDeleteForm = useForm({
    subscriber_ids: [] as number[],
});

const confirmBulkDelete = () => {
    if (selectedIds.value.length === 0) return;
    showDeleteConfirm.value = true;
};

const bulkDelete = () => {
    bulkDeleteForm.subscriber_ids = selectedIds.value;
    bulkDeleteForm.post(route('brands.lists.subscribers.bulk-delete', [brand.id, list.id]), {
        preserveScroll: true,
        onSuccess: () => {
            selectedIds.value = [];
            showDeleteConfirm.value = false;
        },
    });
};

const deleteSubscriber = (id: number) => {
    if (confirm('Are you sure you want to delete this subscriber?')) {
        router.delete(route('brands.lists.subscribers.destroy', [brand.id, list.id, id]), {
            preserveScroll: true,
        });
    }
};

const getStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
        subscribed: 'bg-green-100 text-green-800',
        unconfirmed: 'bg-yellow-100 text-yellow-800',
        unsubscribed: 'bg-gray-100 text-gray-800',
        bounced: 'bg-red-100 text-red-800',
        complained: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string): string => {
    return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
};
</script>

<template>
    <Head :title="`Subscribers - ${list.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Subscribers</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            <a :href="route('brands.lists.show', [brand.id, list.id])" class="text-blue-600 hover:text-blue-700">
                                {{ list.name }}
                            </a>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a
                            :href="route('brands.lists.subscribers.import', [brand.id, list.id])"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Import CSV
                        </a>
                        <a
                            :href="route('brands.lists.subscribers.export', [brand.id, list.id])"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Export CSV
                        </a>
                        <a
                            :href="route('brands.lists.subscribers.create', [brand.id, list.id])"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        >
                            Add Subscriber
                        </a>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div v-if="selectedIds.length > 0" class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-4 flex items-center justify-between">
                    <div class="text-sm text-blue-800">
                        {{ selectedIds.length }} subscriber(s) selected
                    </div>
                    <button
                        @click="confirmBulkDelete"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                    >
                        Delete Selected
                    </button>
                </div>

                <!-- Subscribers Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="subscribers.data.length === 0" class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No subscribers</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Get started by adding your first subscriber.
                        </p>
                        <div class="mt-6">
                            <a
                                :href="route('brands.lists.subscribers.create', [brand.id, list.id])"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                            >
                                Add Subscriber
                            </a>
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            :checked="allSelected"
                                            :indeterminate="someSelected"
                                            @change="toggleAll"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subscribed
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="subscriber in subscribers.data" :key="subscriber.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            :checked="selectedIds.includes(subscriber.id)"
                                            @change="toggleSelection(subscriber.id)"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ subscriber.email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ subscriber.first_name }} {{ subscriber.last_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusColor(subscriber.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ getStatusLabel(subscriber.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ subscriber.subscribed_at ? new Date(subscriber.subscribed_at).toLocaleDateString() : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a
                                            :href="route('brands.lists.subscribers.edit', [brand.id, list.id, subscriber.id])"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            Edit
                                        </a>
                                        <button
                                            @click="deleteSubscriber(subscriber.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="subscribers.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a
                                v-if="subscribers.current_page > 1"
                                :href="route('brands.lists.subscribers.index', { brand: brand.id, list: list.id, page: subscribers.current_page - 1 })"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </a>
                            <a
                                v-if="subscribers.current_page < subscribers.last_page"
                                :href="route('brands.lists.subscribers.index', { brand: brand.id, list: list.id, page: subscribers.current_page + 1 })"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ (subscribers.current_page - 1) * subscribers.per_page + 1 }}</span>
                                    to
                                    <span class="font-medium">{{ Math.min(subscribers.current_page * subscribers.per_page, subscribers.total) }}</span>
                                    of
                                    <span class="font-medium">{{ subscribers.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <a
                                        v-if="subscribers.current_page > 1"
                                        :href="route('brands.lists.subscribers.index', { brand: brand.id, list: list.id, page: subscribers.current_page - 1 })"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                    >
                                        Previous
                                    </a>
                                    <a
                                        v-if="subscribers.current_page < subscribers.last_page"
                                        :href="route('brands.lists.subscribers.index', { brand: brand.id, list: list.id, page: subscribers.current_page + 1 })"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                    >
                                        Next
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div v-if="showDeleteConfirm" class="fixed z-10 inset-0 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteConfirm = false"></div>

                        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Delete subscribers
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to delete {{ selectedIds.length }} subscriber(s)? This action cannot be undone.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button
                                    @click="bulkDelete"
                                    :disabled="bulkDeleteForm.processing"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                                >
                                    {{ bulkDeleteForm.processing ? 'Deleting...' : 'Delete' }}
                                </button>
                                <button
                                    @click="showDeleteConfirm = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
