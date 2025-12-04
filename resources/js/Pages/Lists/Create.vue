<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Brand } from '@/types';

interface Props {
    brand: Brand;
}

const props = defineProps<Props>();

const { brand } = props;

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('brands.lists.store', props.brand.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Create List - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Email List</h2>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    List Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                    autofocus
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                                <a
                                    :href="route('brands.lists.index', brand.id)"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create List' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
