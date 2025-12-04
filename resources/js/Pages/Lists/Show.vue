<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface CustomField {
    id: number;
    name: string;
    tag: string;
    type: string;
    is_required: boolean;
}

interface Subscriber {
    id: number;
    email: string;
    first_name: string | null;
    last_name: string | null;
    status: string;
    subscribed_at: string;
}

interface PaginatedSubscribers {
    data: Subscriber[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface EmailList {
    id: number;
    name: string;
    description: string | null;
    from_name: string;
    from_email: string;
    reply_to_email: string | null;
    subscriber_count: number;
    active_subscriber_count: number;
    subscribers_count: number;
    active_subscribers_count: number;
    is_active: boolean;
    created_at: string;
    custom_fields: CustomField[];
}

interface Props {
    brand: Brand;
    list: EmailList;
    subscribers: PaginatedSubscribers;
}

const props = defineProps<Props>();

const { brand, list, subscribers } = props;

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        subscribed: 'bg-green-100 text-green-800',
        unsubscribed: 'bg-gray-100 text-gray-800',
        bounced: 'bg-red-100 text-red-800',
        pending_confirmation: 'bg-yellow-100 text-yellow-800',
        complained: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        subscribed: 'Subscribed',
        unsubscribed: 'Unsubscribed',
        bounced: 'Bounced',
        pending_confirmation: 'Pending',
        complained: 'Complained',
    };
    return labels[status] || status;
};

const deleteList = () => {
    if (confirm(`Are you sure you want to delete "${list.name}"? This will also delete all subscribers in this list.`)) {
        router.delete(route('brands.lists.destroy', [brand.id, list.id]));
    }
};
</script>

<template>
    <Head :title="`${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- List Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ list.name }}</h2>
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
                                </div>
                                <p v-if="list.description" class="text-sm text-gray-600 mt-2">
                                    {{ list.description }}
                                </p>
                            </div>
                            <div class="flex gap-3">
                                <Link
                                    :href="route('brands.lists.edit', [brand.id, list.id])"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                                >
                                    Edit List
                                </Link>
                                <button
                                    @click="deleteList"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-blue-600 font-medium">Total Subscribers</div>
                                <div class="text-2xl font-bold text-blue-900 mt-1">
                                    {{ (list.subscriber_count || list.subscribers_count || 0).toLocaleString() }}
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-green-600 font-medium">Active Subscribers</div>
                                <div class="text-2xl font-bold text-green-900 mt-1">
                                    {{ (list.active_subscriber_count || list.active_subscribers_count || 0).toLocaleString() }}
                                </div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-sm text-purple-600 font-medium">Custom Fields</div>
                                <div class="text-2xl font-bold text-purple-900 mt-1">
                                    {{ list.custom_fields?.length || 0 }}
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600 font-medium">Created</div>
                                <div class="text-lg font-bold text-gray-900 mt-1">
                                    {{ new Date(list.created_at).toLocaleDateString() }}
                                </div>
                            </div>
                        </div>

                        <!-- Sender Info -->
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">
                                Sender Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">From Name:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ list.from_name }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">From Email:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ list.from_email }}</span>
                                </div>
                                <div v-if="list.reply_to_email">
                                    <span class="text-sm text-gray-600">Reply-To:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ list.reply_to_email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscribers Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Subscribers</h3>
                            <div class="flex gap-3">
                                <Link
                                    :href="route('brands.lists.index', brand.id)"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                                >
                                    Back to Lists
                                </Link>
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Add Subscriber
                                </button>
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Import CSV
                                </button>
                            </div>
                        </div>

                        <div v-if="subscribers.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subscribed
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="subscriber in subscribers.data" :key="subscriber.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ subscriber.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ subscriber.first_name || subscriber.last_name 
                                                    ? `${subscriber.first_name || ''} ${subscriber.last_name || ''}`.trim()
                                                    : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    getStatusColor(subscriber.status)
                                                ]"
                                            >
                                                {{ getStatusLabel(subscriber.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(subscriber.subscribed_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                                Edit
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div v-if="subscribers.last_page > 1" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ ((subscribers.current_page - 1) * subscribers.per_page) + 1 }}
                                    to {{ Math.min(subscribers.current_page * subscribers.per_page, subscribers.total) }}
                                    of {{ subscribers.total }} subscribers
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-if="subscribers.current_page > 1"
                                        :href="route('brands.lists.show', { brand: brand.id, list: list.id, page: subscribers.current_page - 1 })"
                                        class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="subscribers.current_page < subscribers.last_page"
                                        :href="route('brands.lists.show', { brand: brand.id, list: list.id, page: subscribers.current_page + 1 })"
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
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No subscribers</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by adding subscribers or importing from CSV.
                            </p>
                            <div class="mt-6 flex justify-center gap-3">
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Add Subscriber
                                </button>
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Import CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
