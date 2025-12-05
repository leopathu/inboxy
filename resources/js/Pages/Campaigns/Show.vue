<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Brand {
  id: number;
  name: string;
}

interface User {
  id: number;
  name: string;
}

interface EmailList {
  id: number;
  name: string;
}

interface EmailTemplate {
  id: number;
  name: string;
}

interface Campaign {
  id: number;
  brand_id: number;
  name: string;
  subject: string;
  from_name: string;
  from_email: string;
  reply_to_email: string | null;
  type: string;
  status: string;
  html_content: string;
  plain_text_content: string | null;
  track_opens: boolean;
  track_clicks: boolean;
  scheduled_at: string | null;
  sent_at: string | null;
  created_at: string;
  user: User;
  list: EmailList;
  template: EmailTemplate | null;
}

interface Stats {
  total_sends: number;
  delivered: number;
  failed: number;
  opens: number;
  unique_opens: number;
  clicks: number;
  unique_clicks: number;
  bounces: number;
  complaints: number;
  unsubscribes: number;
}

interface Props {
  brand: Brand;
  campaign: Campaign;
  stats: Stats;
}

const props = defineProps<Props>();

const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    scheduled: 'bg-blue-100 text-blue-800',
    sending: 'bg-yellow-100 text-yellow-800',
    sent: 'bg-green-100 text-green-800',
    paused: 'bg-orange-100 text-orange-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date: string): string => {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatPercentage = (numerator: number, denominator: number): string => {
  if (denominator === 0) return '0%';
  return ((numerator / denominator) * 100).toFixed(1) + '%';
};
</script>

<template>
  <Head :title="campaign.name" />

  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <Link
                :href="route('brands.campaigns.index', brand.id)"
                class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block"
              >
                ← Back to Campaigns
              </Link>
              <h2 class="text-2xl font-semibold text-gray-900">{{ campaign.name }}</h2>
              <p class="text-sm text-gray-500 mt-1">
                Created {{ formatDate(campaign.created_at) }} by {{ campaign.user.name }}
              </p>
            </div>
            <div class="flex items-center space-x-3">
              <span :class="getStatusColor(campaign.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                {{ campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1) }}
              </span>
              <Link
                v-if="campaign.status === 'draft'"
                :href="route('brands.campaigns.edit', [brand.id, campaign.id])"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
              >
                Edit Campaign
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Sent</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">
              {{ stats.total_sends.toLocaleString() }}
            </div>
            <div class="mt-1 text-sm text-gray-600">
              {{ stats.delivered.toLocaleString() }} delivered, {{ stats.failed.toLocaleString() }} failed
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Open Rate</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">
              {{ formatPercentage(stats.unique_opens, stats.delivered) }}
            </div>
            <div class="mt-1 text-sm text-gray-600">
              {{ stats.opens.toLocaleString() }} opens ({{ stats.unique_opens.toLocaleString() }} unique)
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Click Rate</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">
              {{ formatPercentage(stats.unique_clicks, stats.delivered) }}
            </div>
            <div class="mt-1 text-sm text-gray-600">
              {{ stats.clicks.toLocaleString() }} clicks ({{ stats.unique_clicks.toLocaleString() }} unique)
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Issues</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">
              {{ (stats.bounces + stats.complaints + stats.unsubscribes).toLocaleString() }}
            </div>
            <div class="mt-1 text-sm text-gray-600">
              {{ stats.bounces }} bounces, {{ stats.complaints }} complaints, {{ stats.unsubscribes }} unsubscribes
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Campaign Details -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Email Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Details</h3>
                <dl class="space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Subject Line</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ campaign.subject }}</dd>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">From Name</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ campaign.from_name }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">From Email</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ campaign.from_email }}</dd>
                    </div>
                  </div>
                  <div v-if="campaign.reply_to_email">
                    <dt class="text-sm font-medium text-gray-500">Reply-To Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ campaign.reply_to_email }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">List</dt>
                    <dd class="mt-1 text-sm">
                      <Link
                        :href="route('brands.lists.show', [brand.id, campaign.list.id])"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        {{ campaign.list.name }}
                      </Link>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Email Content Preview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 max-h-96 overflow-auto">
                  <div v-html="campaign.html_content"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Campaign Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Campaign Info</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ campaign.type }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                      <span :class="getStatusColor(campaign.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                        {{ campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1) }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="campaign.scheduled_at">
                    <dt class="text-sm font-medium text-gray-500">Scheduled For</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(campaign.scheduled_at) }}</dd>
                  </div>
                  <div v-if="campaign.sent_at">
                    <dt class="text-sm font-medium text-gray-500">Sent At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(campaign.sent_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tracking</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <div class="flex flex-col space-y-1">
                        <span v-if="campaign.track_opens" class="text-green-600">✓ Opens tracked</span>
                        <span v-else class="text-gray-400">✗ Opens not tracked</span>
                        <span v-if="campaign.track_clicks" class="text-green-600">✓ Clicks tracked</span>
                        <span v-else class="text-gray-400">✗ Clicks not tracked</span>
                      </div>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-2">
                  <Link
                    v-if="campaign.status === 'draft'"
                    :href="route('brands.campaigns.edit', [brand.id, campaign.id])"
                    class="block w-full text-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                  >
                    Edit Campaign
                  </Link>
                  <button
                    class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                  >
                    Send Test Email
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
