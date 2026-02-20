<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    brand: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    brand_role: props.user.brand_role || 'user',
});

const submit = () => {
    form.patch(route('brands.users.update', { brand: props.brand.id, user: props.user.id }), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="`Edit User - ${user.name}`" />

    <AuthenticatedLayout>
        <div>
            <div class="mb-6">
                <div class="flex items-center text-sm text-gray-500 mb-2">
                    <Link :href="route('brands.index')" class="hover:text-gray-700">
                        Brands
                    </Link>
                    <svg class="mx-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <Link :href="route('brands.users.index', brand.id)" class="hover:text-gray-700">
                        {{ brand.name }} - Users
                    </Link>
                    <svg class="mx-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-gray-900">Edit {{ user.name }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        Edit User - {{ user.name }}
                    </h2>
                    <Link
                        :href="route('brands.users.index', brand.id)"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Back to Users
                    </Link>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="brand_role" value="Brand Role" />
                                <select
                                    id="brand_role"
                                    v-model="form.brand_role"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    Brand admins can manage users within this brand.
                                </p>
                                <InputError class="mt-2" :message="form.errors.brand_role" />
                            </div>

                            <div>
                                <div class="mb-2 bg-gray-50 border border-gray-200 rounded-md p-3">
                                    <p class="text-sm text-gray-700">
                                        <strong>System Role:</strong>
                                        <span
                                            :class="[
                                                'ml-2 inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                                user.role === 'admin'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-800'
                                            ]"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Change Password (Optional)
                                </h3>
                                <div class="space-y-6">
                                    <div>
                                        <InputLabel for="password" value="New Password" />
                                        <TextInput
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="mt-1 block w-full"
                                            autocomplete="new-password"
                                        />
                                        <InputError class="mt-2" :message="form.errors.password" />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Leave blank to keep current password
                                        </p>
                                    </div>

                                    <div>
                                        <InputLabel for="password_confirmation" value="Confirm New Password" />
                                        <TextInput
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="mt-1 block w-full"
                                            autocomplete="new-password"
                                        />
                                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Update User
                                </PrimaryButton>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                                        Updated.
                                    </p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </AuthenticatedLayout>
</template>
