<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, router } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const brandDropdownOpen = ref(false);
const brandDropdownRef = ref(null);

const selectBrand = (brandId) => {
    router.post(route('select-brand', brandId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            brandDropdownOpen.value = false;
        }
    });
};

const handleClickOutside = (event) => {
    if (brandDropdownRef.value && !brandDropdownRef.value.contains(event.target)) {
        brandDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar for desktop -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Logo -->
            <div class="flex h-16 items-center justify-center border-b border-gray-700">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-9 w-auto fill-current text-white" />
                </Link>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-3">
                <Link
                    :href="route('dashboard')"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('dashboard')
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
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('users.index')"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('users.*')
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
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('brands.index')"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('brands.*')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Brands
                </Link>

                <Link
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('settings.index')"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('settings.*')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </Link>

                <Link
                    :href="route('profile.edit')"
                    :class="[
                        'flex items-center px-4 py-3 mb-2 rounded-lg text-sm font-medium transition-colors',
                        route().current('profile.edit')
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </Link>
            </nav>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 w-full border-t border-gray-700">
                <div class="px-4 py-3">
                    <div class="text-sm font-medium text-white">{{ $page.props.auth.user.name }}</div>
                    <div class="text-xs text-gray-400">{{ $page.props.auth.user.email }}</div>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center w-full px-7 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </Link>
            </div>
        </aside>

        <!-- Mobile sidebar backdrop -->
        <div
            v-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
        ></div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
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

                <!-- Brand Selector -->
                <div v-if="$page.props.brands && $page.props.brands.length > 0" ref="brandDropdownRef" class="relative ml-auto">
                    <button
                        type="button"
                        @click="brandDropdownOpen = !brandDropdownOpen"
                        class="flex items-center gap-x-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        <img 
                            v-if="$page.props.selectedBrand?.logo" 
                            :src="`/storage/${$page.props.selectedBrand.logo}`" 
                            alt="Brand logo" 
                            class="h-8 w-8 object-contain rounded"
                        />
                        <div v-else class="h-8 w-8 bg-gray-200 rounded flex items-center justify-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <span class="hidden sm:block">{{ $page.props.selectedBrand?.name || 'Select Brand' }}</span>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div
                        v-show="brandDropdownOpen"
                        class="absolute right-0 z-10 mt-2 w-72 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                        <button
                            v-for="brand in $page.props.brands"
                            :key="brand.id"
                            @click="selectBrand(brand.id)"
                            :class="[
                                'flex items-center w-full gap-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors',
                                $page.props.selectedBrand?.id === brand.id ? 'bg-gray-50' : ''
                            ]"
                        >
                            <img 
                                v-if="brand.logo" 
                                :src="`/storage/${brand.logo}`" 
                                alt="Brand logo" 
                                class="h-10 w-10 object-contain rounded border border-gray-200"
                            />
                            <div v-else class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center border border-gray-200">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900">{{ brand.name }}</div>
                                <div v-if="$page.props.selectedBrand?.id === brand.id" class="text-xs text-indigo-600 mt-0.5">
                                    <svg class="inline h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Selected
                                </div>
                            </div>
                        </button>
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
