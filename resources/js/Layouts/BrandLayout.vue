<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    brand: Object,
});

const sidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar for desktop -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Brand Header -->
            <div class="flex h-16 items-center justify-center border-b border-gray-700 px-4">
                <Link :href="route('brands.dashboard', brand.id)" class="flex items-center gap-3">
                    <img 
                        v-if="brand.brand_logo" 
                        :src="`/storage/${brand.brand_logo}`" 
                        :alt="brand.name" 
                        class="h-10 w-10 object-contain rounded"
                    />
                    <div v-else class="h-10 w-10 bg-gray-600 rounded flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-lg truncate">{{ brand.name }}</span>
                </Link>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-3">
                <Link
                    :href="route('brands.dashboard', brand.id)"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('brands.dashboard', brand.id)
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </Link>

                <Link
                    :href="route('brands.users.index', brand.id)"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('brands.users.*')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </Link>

                <Link
                    :href="route('brands.lists.index', brand.id)"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('brands.lists.*')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lists
                </Link>

                <!-- Back to Brands List -->
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <Link
                        :href="route('brands.index')"
                        class="flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Brands
                    </Link>
                </div>
            </nav>

            <!-- User Menu -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-700 p-4">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center px-4 py-3 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center w-full px-4 py-3 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar with brand info -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center justify-between gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Brand info on the right -->
                <div class="flex items-center gap-3 ml-auto">
                    <img 
                        v-if="brand.brand_logo" 
                        :src="`/storage/${brand.brand_logo}`" 
                        :alt="brand.name" 
                        class="h-10 w-10 object-contain rounded border border-gray-200"
                    />
                    <div v-else class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center border border-gray-200">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-sm font-semibold text-gray-900">{{ brand.name }}</div>
                        <div class="text-xs text-gray-500">{{ brand.from_email }}</div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-6">
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
