<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Brand {
  id: number;
  name: string;
  company: string;
  email: string;
  website: string | null;
  phone: string | null;
  from_name: string | null;
  from_email: string | null;
  reply_to_email: string | null;
  monthly_send_limit: number;
  emails_sent_this_month: number;
  primary_color: string | null;
  is_active: boolean;
  use_own_ses: boolean;
  use_own_smtp: boolean;
  created_at: string;
}

interface Statistics {
  total_lists: number;
  total_campaigns: number;
  total_subscribers: number;
  emails_sent_this_month: number;
  remaining_sends: number | null;
  send_limit_percentage: number | null;
}

interface Props {
  brand: Brand;
  statistics: Statistics;
}

const props = defineProps<Props>();

const getRemainingPercentage = (): number => {
  if (props.brand.monthly_send_limit === 0) return 0;
  return (props.brand.emails_sent_this_month / props.brand.monthly_send_limit) * 100;
};
</script>

<template>
  <Head :title="brand.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          {{ brand.name }}
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('brands.edit', brand.id)"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Edit
          </Link>
          <Link
            :href="route('brands.users.index', brand.id)"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Manage Users
          </Link>
          <Link
            :href="route('brands.api-keys.index', brand.id)"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            API Keys
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 rounded-md bg-indigo-500 p-3">
                  <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Total Lists</dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ statistics.total_lists.toLocaleString() }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 rounded-md bg-green-500 p-3">
                  <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Campaigns</dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ statistics.total_campaigns.toLocaleString() }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 rounded-md bg-yellow-500 p-3">
                  <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Subscribers</dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ statistics.total_subscribers.toLocaleString() }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 rounded-md bg-purple-500 p-3">
                  <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Emails Sent</dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ statistics.emails_sent_this_month.toLocaleString() }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Brand Details -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Brand Information</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500">Company</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ brand.company }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ brand.email }}</dd>
              </div>
              <div v-if="brand.website">
                <dt class="text-sm font-medium text-gray-500">Website</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  <a :href="brand.website" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                    {{ brand.website }}
                  </a>
                </dd>
              </div>
              <div v-if="brand.phone">
                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ brand.phone }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                  <span
                    class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                    :class="brand.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  >
                    {{ brand.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Sending Limits -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sending Limits</h3>
            <div v-if="brand.monthly_send_limit === 0">
              <p class="text-sm text-gray-500">Unlimited sends per month</p>
            </div>
            <div v-else>
              <div class="mb-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-500">Monthly Usage</span>
                  <span class="font-medium text-gray-900">
                    {{ brand.emails_sent_this_month.toLocaleString() }} / {{ brand.monthly_send_limit.toLocaleString() }}
                  </span>
                </div>
              </div>
              <div class="h-4 w-full bg-gray-200 rounded-full overflow-hidden">
                <div
                  class="h-full transition-all"
                  :class="{
                    'bg-green-500': getRemainingPercentage() < 70,
                    'bg-yellow-500': getRemainingPercentage() >= 70 && getRemainingPercentage() < 90,
                    'bg-red-500': getRemainingPercentage() >= 90,
                  }"
                  :style="{ width: `${Math.min(getRemainingPercentage(), 100)}%` }"
                />
              </div>
              <p v-if="statistics.remaining_sends !== null" class="mt-2 text-sm text-gray-500">
                {{ statistics.remaining_sends.toLocaleString() }} sends remaining
              </p>
            </div>
          </div>
        </div>

        <!-- Email Configuration -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Configuration</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div v-if="brand.from_name">
                <dt class="text-sm font-medium text-gray-500">Default From Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ brand.from_name }}</dd>
              </div>
              <div v-if="brand.from_email">
                <dt class="text-sm font-medium text-gray-500">Default From Email</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ brand.from_email }}</dd>
              </div>
              <div v-if="brand.reply_to_email">
                <dt class="text-sm font-medium text-gray-500">Default Reply-To</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ brand.reply_to_email }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">SES Configuration</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  {{ brand.use_own_ses ? 'Using own SES credentials' : 'Using system SES' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">SMTP Configuration</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  {{ brand.use_own_smtp ? 'Using own SMTP server' : 'Using system SMTP' }}
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
