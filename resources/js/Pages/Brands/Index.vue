<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    brands: Object,
});

const deleteBrand = (id) => {
    if (confirm('Are you sure you want to delete this brand?')) {
        router.delete(route('brands.destroy', id));
    }
};
</script>

<template>
    <Head title="Brands" />

    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Brands</h1>
            <Link
                :href="route('brands.create')"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Brand
            </Link>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Logo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                From Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                From Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Opt-in Method
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Created By
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="brand in brands.data" :key="brand.id" class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <img 
                                    v-if="brand.brand_logo" 
                                    :src="`/storage/${brand.brand_logo}`" 
                                    alt="Brand logo" 
                                    class="h-10 w-10 object-contain rounded"
                                />
                                <div v-else class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                <Link 
                                    :href="route('brands.dashboard', brand.id)" 
                                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                                >
                                    {{ brand.name }}
                                </Link>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ brand.from_name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ brand.from_email }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                <span :class="[
                                    'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                    brand.default_optin_method === 'double' 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-blue-100 text-blue-800'
                                ]">
                                    {{ brand.default_optin_method === 'double' ? 'Double Opt-in' : 'Single Opt-in' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ brand.user?.name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-3">
                                <Link
                                    :href="route('brands.users.index', brand.id)"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Users
                                </Link>
                                <Link
                                    :href="route('brands.edit', brand.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteBrand(brand.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="brands.data.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No brands found. Create your first brand to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="brands.links.length > 3" class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link
                        v-if="brands.prev_page_url"
                        :href="brands.prev_page_url"
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="brands.next_page_url"
                        :href="brands.next_page_url"
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Next
                    </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ brands.from }}</span>
                            to
                            <span class="font-medium">{{ brands.to }}</span>
                            of
                            <span class="font-medium">{{ brands.total }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, index) in brands.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                                    link.active
                                        ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                        : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                    index === 0 ? 'rounded-l-md' : '',
                                    index === brands.links.length - 1 ? 'rounded-r-md' : '',
                                ]"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
