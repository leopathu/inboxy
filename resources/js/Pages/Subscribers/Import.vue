<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface EmailList {
    id: number;
    name: string;
}

interface CustomField {
    id: number;
    name: string;
    tag: string;
}

interface Props {
    brand: Brand;
    list: EmailList;
    customFields: CustomField[];
}

const props = defineProps<Props>();

const { brand, list, customFields } = props;

const form = useForm({
    file: null as File | null,
    has_header: true,
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file = target.files[0];
    }
};

const submit = () => {
    form.post(route('brands.lists.subscribers.process-import', [brand.id, list.id]), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head :title="`Import Subscribers - ${list.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Import Subscribers</h2>
                        <p class="mb-6 text-sm text-gray-600">
                            Importing to list: <strong>{{ list.name }}</strong>
                        </p>

                        <!-- Instructions -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                            <h3 class="text-sm font-medium text-blue-800 mb-2">CSV Format Instructions</h3>
                            <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                                <li>First column must be the email address (required)</li>
                                <li>Second column: First Name (optional)</li>
                                <li>Third column: Last Name (optional)</li>
                                <li>Include a header row with column names</li>
                                <li>Maximum file size: 10MB</li>
                                <li>Duplicate emails will be skipped automatically</li>
                            </ul>
                        </div>

                        <!-- CSV Example -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Example CSV Format:</h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3 font-mono text-xs">
                                Email,First Name,Last Name<br>
                                john@example.com,John,Doe<br>
                                jane@example.com,Jane,Smith
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- File Upload -->
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700">
                                    CSV File <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="file"
                                    type="file"
                                    accept=".csv,.txt"
                                    @change="handleFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100"
                                    required
                                />
                                <p v-if="form.errors.file" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.file }}
                                </p>
                            </div>

                            <!-- Has Header -->
                            <div>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.has_header"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">
                                        First row contains column headers
                                    </span>
                                </label>
                                <p class="mt-1 ml-6 text-sm text-gray-500">
                                    Check this if your CSV has a header row (recommended)
                                </p>
                            </div>

                            <!-- Custom Fields Info -->
                            <div v-if="customFields.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <h3 class="text-sm font-medium text-yellow-800 mb-2">Custom Fields Available</h3>
                                <p class="text-sm text-yellow-700 mb-2">
                                    This list has {{ customFields.length }} custom field(s). To import custom field values, add columns with these headers:
                                </p>
                                <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                    <li v-for="field in customFields" :key="field.id">
                                        <strong>{{ field.name }}</strong> (use header: "{{ field.name }}")
                                    </li>
                                </ul>
                            </div>

                            <!-- Progress Info -->
                            <div v-if="form.processing" class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-center">
                                    <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-blue-700">Processing import... Please wait.</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                                <a
                                    :href="route('brands.lists.show', [brand.id, list.id])"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.file"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Importing...' : 'Import Subscribers' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
