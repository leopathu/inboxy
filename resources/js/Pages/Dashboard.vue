<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Brand {
    id: number;
    name: string;
    slug: string;
}

interface Statistics {
    total_subscribers: number;
    total_campaigns: number;
    total_lists: number;
    total_forms: number;
    recent_campaigns?: Array<{
        id: number;
        name: string;
        subject: string;
        status: string;
        created_at: string;
    }>;
}

interface Props {
    brand: Brand;
    statistics: Statistics;
}

const props = defineProps<Props>();
</script>

<template>
    <Head :title="`${brand.name} - Dashboard`" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ brand.name }} Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics Grid -->
                <div class="mb-6 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Subscribers -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                Total Subscribers
                            </div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ statistics.total_subscribers.toLocaleString() }}
                            </div>
                        </div>
                    </div>

                    <!-- Total Campaigns -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                Total Campaigns
                            </div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ statistics.total_campaigns }}
                            </div>
                        </div>
                    </div>

                    <!-- Total Lists -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                Total Lists
                            </div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ statistics.total_lists }}
                            </div>
                        </div>
                    </div>

                    <!-- Total Forms -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                Subscription Forms
                            </div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">
                                {{ statistics.total_forms }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            Quick Actions
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <Link
                                :href="route('brands.campaigns.create', brand.id)"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Create Campaign
                            </Link>
                            <Link
                                :href="route('brands.lists.create', brand.id)"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Create List
                            </Link>
                            <Link
                                :href="route('brands.lists.index', brand.id)"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                View Lists
                            </Link>
                            <Link
                                :href="route('brands.campaigns.index', brand.id)"
                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                View Campaigns
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Campaigns -->
                <div
                    v-if="statistics.recent_campaigns && statistics.recent_campaigns.length > 0"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Recent Campaigns
                            </h3>
                            <Link
                                :href="route('brands.campaigns.index', brand.id)"
                                class="text-sm text-blue-600 hover:text-blue-800"
                            >
                                View All
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Name
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Subject
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Status
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Created
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr
                                        v-for="campaign in statistics.recent_campaigns"
                                        :key="campaign.id"
                                    >
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Link
                                                :href="route('brands.campaigns.show', [brand.id, campaign.id])"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800"
                                            >
                                                {{ campaign.name }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ campaign.subject }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                                :class="{
                                                    'bg-gray-100 text-gray-800': campaign.status === 'draft',
                                                    'bg-blue-100 text-blue-800': campaign.status === 'scheduled',
                                                    'bg-yellow-100 text-yellow-800': campaign.status === 'sending',
                                                    'bg-green-100 text-green-800': campaign.status === 'sent',
                                                    'bg-orange-100 text-orange-800': campaign.status === 'paused',
                                                    'bg-red-100 text-red-800': campaign.status === 'cancelled'
                                                }"
                                            >
                                                {{ campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ new Date(campaign.created_at).toLocaleDateString() }}
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
