<script setup lang="ts">
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

interface Brand {
  id: number;
  name: string;
  company: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  current_brand_id: number | null;
}

const page = usePage();
const user = computed(() => page.props.auth?.user as User);
const brands = computed(() => page.props.brands as Brand[] || []);
const currentBrand = computed(() => {
  if (!user.value.current_brand_id) return null;
  return brands.value.find(b => b.id === user.value.current_brand_id);
});

const switchBrand = (brandId: number) => {
  const brand = brands.value.find(b => b.id === brandId);
  if (brand) {
    router.post(route('brands.switch', brand.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        // Reload to refresh brand context
        router.reload();
      },
    });
  }
};
</script>

<template>
  <div v-if="brands.length > 0" class="relative">
    <Dropdown align="right" width="48">
      <template #trigger>
        <span class="inline-flex rounded-md">
          <button
            type="button"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            <svg
              class="mr-2 h-4 w-4 text-gray-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
              />
            </svg>
            <span v-if="currentBrand">{{ currentBrand.name }}</span>
            <span v-else class="text-gray-500">Select Brand</span>
            <svg
              class="ml-2 -mr-0.5 h-4 w-4"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </span>
      </template>

      <template #content>
        <div class="px-4 py-2 border-b border-gray-100">
          <p class="text-xs text-gray-500 uppercase tracking-wider">Switch Brand</p>
        </div>
        <button
          v-for="brand in brands"
          :key="brand.id"
          type="button"
          @click="switchBrand(brand.id)"
          class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none transition duration-150 ease-in-out"
          :class="{
            'bg-gray-50 font-medium': currentBrand?.id === brand.id,
          }"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium">{{ brand.name }}</div>
              <div class="text-xs text-gray-500">{{ brand.company }}</div>
            </div>
            <svg
              v-if="currentBrand?.id === brand.id"
              class="h-4 w-4 text-indigo-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </button>
      </template>
    </Dropdown>
  </div>
</template>
