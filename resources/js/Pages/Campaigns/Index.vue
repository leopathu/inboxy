<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Brand {
  id: number;
  name: string;
}

interface Campaign {
  id: number;
  name: string;
  subject: string;
  type: string;
  status: string;
  list: { id: number; name: string };
  total_recipients: number;
  total_sent: number;
  total_opens: number;
  total_clicks: number;
  created_at: string;
  scheduled_at: string | null;
  sent_at: string | null;
}

interface Props {
  brand: Brand;
  campaigns: {
    data: Campaign[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

const props = defineProps<Props>();

const confirmingDeletion = ref<number | null>(null);

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

const getTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    regular: 'Regular',
    autoresponder: 'Autoresponder',
    trigger: 'Trigger',
  };
  return labels[type] || type;
};

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatPercentage = (numerator: number, denominator: number): string => {
  if (denominator === 0) return '0%';
  return ((numerator / denominator) * 100).toFixed(1) + '%';
};

const deleteCampaign = (id: number) => {
  router.delete(route('brands.campaigns.destroy', [props.brand.id, id]), {
    onSuccess: () => {
      confirmingDeletion.value = null;
    },
  });
};

const duplicateCampaign = (id: number) => {
  router.post(route('brands.campaigns.duplicate', [props.brand.id, id]));
};

const pauseCampaign = (id: number) => {
  router.post(route('brands.campaigns.pause', [props.brand.id, id]));
};

const resumeCampaign = (id: number) => {
  router.post(route('brands.campaigns.resume', [props.brand.id, id]));
};

const cancelCampaign = (id: number) => {
  router.post(route('brands.campaigns.cancel', [props.brand.id, id]));
};
</script>

<template>
  <Head title="Campaigns" />

  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-900">Campaigns</h2>
          <Link
            :href="route('brands.campaigns.create', brand.id)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
          >
            Create Campaign
          </Link>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div v-if="campaigns.data.length === 0" class="p-6 text-center text-gray-500">
            <p class="mb-4">No campaigns yet. Create your first campaign to get started.</p>
            <Link
              :href="route('brands.campaigns.create', brand.id)"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
            >
              Create Campaign
            </Link>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Campaign
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    List
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Performance
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="campaign in campaigns.data" :key="campaign.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <Link :href="route('brands.campaigns.show', [brand.id, campaign.id])" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                      {{ campaign.name }}
                    </Link>
                    <div class="text-xs text-gray-500 mt-1">{{ campaign.subject }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900">{{ getTypeLabel(campaign.type) }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusColor(campaign.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('brands.lists.show', [brand.id, campaign.list.id])" class="text-sm text-indigo-600 hover:text-indigo-900">
                      {{ campaign.list.name }}
                    </Link>
                  </td>
                  <td class="px-6 py-4">
                    <div v-if="campaign.total_sent > 0" class="text-xs">
                      <div class="text-gray-900">
                        Sent: {{ campaign.total_sent.toLocaleString() }}
                      </div>
                      <div class="text-gray-500">
                        Opens: {{ formatPercentage(campaign.total_opens, campaign.total_sent) }} •
                        Clicks: {{ formatPercentage(campaign.total_clicks, campaign.total_sent) }}
                      </div>
                    </div>
                    <div v-else class="text-xs text-gray-400">Not sent yet</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="campaign.sent_at">
                      Sent: {{ formatDate(campaign.sent_at) }}
                    </div>
                    <div v-else-if="campaign.scheduled_at">
                      Scheduled: {{ formatDate(campaign.scheduled_at) }}
                    </div>
                    <div v-else>
                      Created: {{ formatDate(campaign.created_at) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link
                        v-if="campaign.status === 'draft'"
                        :href="route('brands.campaigns.edit', [brand.id, campaign.id])"
                        class="text-indigo-600 hover:text-indigo-900"
                        title="Edit"
                      >
                        Edit
                      </Link>
                      <button
                        v-if="campaign.status === 'sending'"
                        @click="pauseCampaign(campaign.id)"
                        class="text-yellow-600 hover:text-yellow-900"
                        title="Pause"
                      >
                        Pause
                      </button>
                      <button
                        v-if="campaign.status === 'paused'"
                        @click="resumeCampaign(campaign.id)"
                        class="text-green-600 hover:text-green-900"
                        title="Resume"
                      >
                        Resume
                      </button>
                      <button
                        v-if="['scheduled', 'sending', 'paused'].includes(campaign.status)"
                        @click="cancelCampaign(campaign.id)"
                        class="text-red-600 hover:text-red-900"
                        title="Cancel"
                      >
                        Cancel
                      </button>
                      <button
                        @click="duplicateCampaign(campaign.id)"
                        class="text-gray-600 hover:text-gray-900"
                        title="Duplicate"
                      >
                        Duplicate
                      </button>
                      <button
                        v-if="campaign.status === 'draft'"
                        @click="confirmingDeletion = campaign.id"
                        class="text-red-600 hover:text-red-900"
                        title="Delete"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="campaigns.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                  Showing {{ (campaigns.current_page - 1) * campaigns.per_page + 1 }} to
                  {{ Math.min(campaigns.current_page * campaigns.per_page, campaigns.total) }} of
                  {{ campaigns.total }} results
                </div>
                <div class="flex space-x-1">
                  <Link
                    v-for="page in campaigns.last_page"
                    :key="page"
                    :href="route('brands.campaigns.index', [brand.id, { page }])"
                    :class="[
                      'px-3 py-2 text-sm font-medium rounded-md',
                      page === campaigns.current_page
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300',
                    ]"
                  >
                    {{ page }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="confirmingDeletion"
      class="fixed inset-0 z-50 overflow-y-auto"
      @click.self="confirmingDeletion = null"
    >
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg p-6 max-w-md w-full">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Campaign</h3>
          <p class="text-sm text-gray-500 mb-6">
            Are you sure you want to delete this campaign? This action cannot be undone.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="confirmingDeletion = null"
              class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              @click="deleteCampaign(confirmingDeletion)"
              class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Delete Campaign
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
