<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
  name: '',
  company: '',
  email: '',
  website: '',
  phone: '',
  from_name: '',
  from_email: '',
  reply_to_email: '',
  monthly_send_limit: '0',
  primary_color: '#3B82F6',
  can_create_lists: true,
  can_create_campaigns: true,
  can_import_subscribers: true,
  can_export_data: true,
  can_view_reports: true,
  is_active: true,
});

const submit = () => {
  form.post(route('brands.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Create Brand" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Create Brand
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <InputLabel for="name" value="Brand Name *" />
                  <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                  <InputLabel for="company" value="Company Name *" />
                  <TextInput
                    id="company"
                    v-model="form.company"
                    type="text"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError class="mt-2" :message="form.errors.company" />
                </div>

                <div>
                  <InputLabel for="email" value="Email *" />
                  <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                  <InputLabel for="phone" value="Phone" />
                  <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full"
                  />
                  <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div class="sm:col-span-2">
                  <InputLabel for="website" value="Website" />
                  <TextInput
                    id="website"
                    v-model="form.website"
                    type="url"
                    class="mt-1 block w-full"
                    placeholder="https://example.com"
                  />
                  <InputError class="mt-2" :message="form.errors.website" />
                </div>
              </div>
            </div>

            <!-- Email Settings -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Default Email Settings</h3>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <InputLabel for="from_name" value="From Name" />
                  <TextInput
                    id="from_name"
                    v-model="form.from_name"
                    type="text"
                    class="mt-1 block w-full"
                  />
                  <InputError class="mt-2" :message="form.errors.from_name" />
                </div>

                <div>
                  <InputLabel for="from_email" value="From Email" />
                  <TextInput
                    id="from_email"
                    v-model="form.from_email"
                    type="email"
                    class="mt-1 block w-full"
                  />
                  <InputError class="mt-2" :message="form.errors.from_email" />
                </div>

                <div class="sm:col-span-2">
                  <InputLabel for="reply_to_email" value="Reply-To Email" />
                  <TextInput
                    id="reply_to_email"
                    v-model="form.reply_to_email"
                    type="email"
                    class="mt-1 block w-full"
                  />
                  <InputError class="mt-2" :message="form.errors.reply_to_email" />
                </div>
              </div>
            </div>

            <!-- Sending Limits -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Sending Limits</h3>
              <div>
                <InputLabel for="monthly_send_limit" value="Monthly Send Limit" />
                <TextInput
                  id="monthly_send_limit"
                  v-model="form.monthly_send_limit"
                  type="number"
                  class="mt-1 block w-full"
                  min="0"
                />
                <p class="mt-1 text-sm text-gray-500">
                  Set to 0 for unlimited sends
                </p>
                <InputError class="mt-2" :message="form.errors.monthly_send_limit" />
              </div>
            </div>

            <!-- Branding -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Branding</h3>
              <div>
                <InputLabel for="primary_color" value="Primary Color" />
                <div class="flex items-center mt-1 space-x-3">
                  <input
                    id="primary_color"
                    v-model="form.primary_color"
                    type="color"
                    class="h-10 w-20 rounded border-gray-300 shadow-sm"
                  />
                  <TextInput
                    v-model="form.primary_color"
                    type="text"
                    class="block w-32"
                    pattern="^#[0-9A-Fa-f]{6}$"
                  />
                </div>
                <InputError class="mt-2" :message="form.errors.primary_color" />
              </div>
            </div>

            <!-- Permissions -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions</h3>
              <div class="space-y-4">
                <div class="flex items-center">
                  <input
                    id="can_create_lists"
                    v-model="form.can_create_lists"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="can_create_lists" class="ml-3 text-sm text-gray-700">
                    Can create lists
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="can_create_campaigns"
                    v-model="form.can_create_campaigns"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="can_create_campaigns" class="ml-3 text-sm text-gray-700">
                    Can create campaigns
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="can_import_subscribers"
                    v-model="form.can_import_subscribers"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="can_import_subscribers" class="ml-3 text-sm text-gray-700">
                    Can import subscribers
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="can_export_data"
                    v-model="form.can_export_data"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="can_export_data" class="ml-3 text-sm text-gray-700">
                    Can export data
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="can_view_reports"
                    v-model="form.can_view_reports"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="can_view_reports" class="ml-3 text-sm text-gray-700">
                    Can view reports
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="is_active" class="ml-3 text-sm text-gray-700">
                    Active
                  </label>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end border-t border-gray-200 pt-6">
              <PrimaryButton :disabled="form.processing">
                Create Brand
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
