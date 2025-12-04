<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface EmailList {
    id: number;
    name: string;
    description: string | null;
    from_name: string;
    from_email: string;
    subscriber_count: number;
    active_subscriber_count: number;
    is_active: boolean;
    created_at: string;
}

interface PaginatedLists {
    data: EmailList[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    brand: Brand;
    lists: PaginatedLists;
}

const props = defineProps<Props>();

const { brand, lists } = props;

const deleteList = (list: EmailList) => {
    if (confirm(`Are you sure you want to delete "${list.name}"? This will also delete all subscribers in this list.`)) {
        router.delete(route('brands.lists.destroy', [brand.id, list.id]));
    }
};
</script>

<template>
    <Head :title="`Email Lists - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Email Lists</h2>
                                <p class="text-sm text-gray-600 mt-1">
                                    Manage your subscriber lists for {{ brand.name }}
                                </p>
                            </div>
                            <Link
                                :href="route('brands.lists.create', brand.id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Create List
                            </Link>
                        </div>

                        <!-- Lists Table -->
                        <div v-if="lists.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            List Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            From
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subscribers
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="list in lists.data" :key="list.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link
                                                :href="route('brands.lists.show', [brand.id, list.id])"
                                                class="text-blue-600 hover:text-blue-900 font-medium"
                                            >
                                                {{ list.name }}
                                            </Link>
                                            <p v-if="list.description" class="text-sm text-gray-500 mt-1">
                                                {{ list.description }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ list.from_name }}</div>
                                            <div class="text-sm text-gray-500">{{ list.from_email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ list.active_subscriber_count.toLocaleString() }} active
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ list.subscriber_count.toLocaleString() }} total
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    list.is_active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                ]"
                                            >
                                                {{ list.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(list.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('brands.lists.edit', [brand.id, list.id])"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteList(list)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div v-if="lists.last_page > 1" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ ((lists.current_page - 1) * lists.per_page) + 1 }}
                                    to {{ Math.min(lists.current_page * lists.per_page, lists.total) }}
                                    of {{ lists.total }} lists
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-if="lists.current_page > 1"
                                        :href="route('brands.lists.index', { brand: brand.id, page: lists.current_page - 1 })"
                                        class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="lists.current_page < lists.last_page"
                                        :href="route('brands.lists.index', { brand: brand.id, page: lists.current_page + 1 })"
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No lists</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by creating your first email list.
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('brands.lists.create', brand.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Create List
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
