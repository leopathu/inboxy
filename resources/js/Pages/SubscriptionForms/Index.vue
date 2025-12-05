<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

interface Brand {
    id: number;
    name: string;
}

interface EmailList {
    id: number;
    name: string;
}

interface SubscriptionForm {
    id: number;
    name: string;
    identifier: string;
    description: string | null;
    is_active: boolean;
    enable_double_optin: boolean;
    submissions_count: number;
    created_at: string;
    public_url: string;
}

interface Props {
    brand: Brand;
    list: EmailList;
    forms: {
        data: SubscriptionForm[];
        links: any[];
        meta: any;
    };
}

const props = defineProps<Props>();

const deleteForm = ref<SubscriptionForm | null>(null);
const showDeleteModal = ref(false);

const confirmDelete = (form: SubscriptionForm) => {
    deleteForm.value = form;
    showDeleteModal.value = true;
};

const handleDelete = () => {
    if (deleteForm.value) {
        router.delete(
            route('brands.lists.subscription-forms.destroy', [props.brand.id, props.list.id, deleteForm.value.id]),
            {
                onSuccess: () => {
                    showDeleteModal.value = false;
                    deleteForm.value = null;
                },
            }
        );
    }
};

const copyEmbedCode = (identifier: string) => {
    const code = `<div id="inboxy-form-${identifier}"></div>\n<script src="${window.location.origin}/forms/${identifier}/embed.js"><\/script>`;
    navigator.clipboard.writeText(code);
};

const toggleActive = (form: SubscriptionForm) => {
    router.put(
        route('brands.lists.subscription-forms.update', [props.brand.id, props.list.id, form.id]),
        { is_active: !form.is_active },
        { preserveScroll: true }
    );
};
</script>

<template>
    <Head :title="`Subscription Forms - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Subscription Forms</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ list.name }}
                        </p>
                    </div>
                    <Link
                        :href="route('brands.lists.subscription-forms.create', [brand.id, list.id])"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        Create Form
                    </Link>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-900">About Subscription Forms</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Create customizable forms to collect subscribers. Forms can be embedded on external websites or hosted on dedicated pages. Support double opt-in, custom fields, and redirect URLs.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Forms Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="forms.data.length === 0" class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No forms yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first subscription form.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('brands.lists.subscription-forms.create', [brand.id, list.id])"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                            >
                                Create First Form
                            </Link>
                        </div>
                    </div>

                    <div v-else>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="form in forms.data" :key="form.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ form.name }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    {{ form.identifier }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="toggleActive(form)"
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer',
                                                form.is_active
                                                    ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                                            ]"
                                        >
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            form.enable_double_optin ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ form.enable_double_optin ? 'Double Opt-in' : 'Single Opt-in' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ form.submissions_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ new Date(form.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('brands.lists.subscription-forms.show', [brand.id, list.id, form.id])"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="View Details"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="route('brands.lists.subscription-forms.edit', [brand.id, list.id, form.id])"
                                                class="text-gray-600 hover:text-gray-900"
                                                title="Edit"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="copyEmbedCode(form.identifier)"
                                                class="text-green-600 hover:text-green-900"
                                                title="Copy Embed Code"
                                            >
                                                Copy
                                            </button>
                                            <button
                                                @click="confirmDelete(form)"
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
                        <div v-if="forms.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link
                                        v-if="forms.meta.prev_page_url"
                                        :href="forms.meta.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="forms.meta.next_page_url"
                                        :href="forms.meta.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{ forms.meta.from }}</span>
                                            to
                                            <span class="font-medium">{{ forms.meta.to }}</span>
                                            of
                                            <span class="font-medium">{{ forms.meta.total }}</span>
                                            forms
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <Link
                                                v-for="(link, index) in forms.links"
                                                :key="index"
                                                :href="link.url"
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                    link.active
                                                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === forms.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                            />
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Form</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete "{{ deleteForm?.name }}"? This will also delete all submission records. This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            @click="handleDelete"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Delete
                        </button>
                        <button
                            type="button"
                            @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
