<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

interface Brand {
  id: number;
  name: string;
  company: string;
  email: string;
  monthly_send_limit: number;
  emails_sent_this_month: number;
  is_active: boolean;
  created_at: string;
}

interface PaginatedBrands {
  data: Brand[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

interface Props {
  brands: PaginatedBrands;
}

const props = defineProps<Props>();
const brandsList = computed(() => props.brands.data);

const confirmingDeletion = ref<number | null>(null);

const deleteForm = useForm({});

const deleteBrand = (brandId: number) => {
  deleteForm.delete(route('brands.destroy', brandId), {
    preserveScroll: true,
    onSuccess: () => {
      confirmingDeletion.value = null;
    },
  });
};

const getRemainingPercentage = (brand: Brand): number => {
  if (brand.monthly_send_limit === 0) return 0;
  return (brand.emails_sent_this_month / brand.monthly_send_limit) * 100;
};

const getStatusColor = (brand: Brand): string => {
  if (!brand.is_active) return 'bg-gray-100 text-gray-800';
  const percentage = getRemainingPercentage(brand);
  if (percentage >= 90) return 'bg-red-100 text-red-800';
  if (percentage >= 70) return 'bg-yellow-100 text-yellow-800';
  return 'bg-green-100 text-green-800';
};
</script>

<template>
  <Head title="Brands" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Brands
        </h2>
        <Link
          :href="route('brands.create')"
          class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
        >
          Create Brand
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div v-if="brandsList.length === 0" class="text-center py-12">
              <p class="text-gray-500 text-sm">No brands found.</p>
              <Link
                :href="route('brands.create')"
                class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500"
              >
                Create your first brand
              </Link>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                      Brand
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                      Contact
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                      Usage
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="brand in brandsList" :key="brand.id">
                    <td class="whitespace-nowrap px-6 py-4">
                      <div class="flex items-center">
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            {{ brand.name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ brand.company }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                      <div class="text-sm text-gray-900">{{ brand.email }}</div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                      <div class="text-sm text-gray-900">
                        {{ brand.emails_sent_this_month.toLocaleString() }}
                        <span v-if="brand.monthly_send_limit > 0">
                          / {{ brand.monthly_send_limit.toLocaleString() }}
                        </span>
                        <span v-else class="text-gray-500">(Unlimited)</span>
                      </div>
                      <div v-if="brand.monthly_send_limit > 0" class="mt-1">
                        <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                          <div
                            class="h-full transition-all"
                            :class="{
                              'bg-green-500': getRemainingPercentage(brand) < 70,
                              'bg-yellow-500': getRemainingPercentage(brand) >= 70 && getRemainingPercentage(brand) < 90,
                              'bg-red-500': getRemainingPercentage(brand) >= 90,
                            }"
                            :style="{ width: `${Math.min(getRemainingPercentage(brand), 100)}%` }"
                          />
                        </div>
                      </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                      <span
                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                        :class="getStatusColor(brand)"
                      >
                        {{ brand.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                      <Link
                        :href="route('brands.dashboard', brand.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('brands.edit', brand.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </Link>
                      <button
                        type="button"
                        @click="confirmingDeletion = brand.id"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="confirmingDeletion !== null"
      class="fixed inset-0 z-50 overflow-y-auto"
      @click.self="confirmingDeletion = null"
    >
      <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" />

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Delete Brand
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to delete this brand? This action cannot be undone and will delete all associated lists, campaigns, and subscribers.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="deleteBrand(confirmingDeletion)"
              :disabled="deleteForm.processing"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              Delete
            </button>
            <button
              type="button"
              @click="confirmingDeletion = null"
              :disabled="deleteForm.processing"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
