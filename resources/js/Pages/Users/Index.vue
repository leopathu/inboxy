<script setup>
import BrandLayout from '@/Layouts/BrandLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
    brand: Object,
});

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to remove ${user.name} from this brand?`)) {
        router.delete(route('brands.users.destroy', { brand: props.brand.id, user: user.id }));
    }
};
</script>

<template>
    <Head :title="`${brand.name} - Users`" />

    <BrandLayout :brand="brand">
        <div>
            <div class="mb-6">
                <div class="flex items-center text-sm text-gray-500 mb-2">
                    <Link :href="route('brands.index')" class="hover:text-gray-700">
                        Brands
                    </Link>
                    <svg class="mx-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-gray-900">{{ brand.name }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        Manage Users - {{ brand.name }}
                    </h2>
                    <Link
                        :href="route('brands.users.create', brand.id)"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                    >
                        Add User
                    </Link>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Brand Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        System Role
                                    </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="user in users" :key="user.id">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ user.name }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                {{ user.email }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                :class="[
                                                    'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                                    user.pivot?.role === 'admin'
                                                        ? 'bg-purple-100 text-purple-800'
                                                        : 'bg-blue-100 text-blue-800'
                                                ]"
                                            >
                                                {{ user.pivot?.role || 'user' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                :class="[
                                                    'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                                    user.role === 'admin'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                ]"
                                            >
                                                {{ user.role }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-3">
                                            <Link
                                                :href="route('brands.users.edit', { brand: brand.id, user: user.id })"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteUser(user)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="users.length === 0" class="py-8 text-center text-gray-500">
                                No users in this brand. Add your first user to get started.
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </BrandLayout>
</template>
