<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Brand {
  id: number;
  name: string;
}

interface EmailList {
  id: number;
  name: string;
  subscriber_count: number;
}

interface EmailTemplate {
  id: number;
  name: string;
  thumbnail: string | null;
}

interface Props {
  brand: Brand;
  lists: EmailList[];
  templates: EmailTemplate[];
  types: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  name: '',
  subject: '',
  from_name: '',
  from_email: '',
  reply_to_email: '',
  type: 'regular',
  list_id: '',
  template_id: '',
  html_content: '',
  plain_text_content: '',
  send_to: 'subscribed',
  segment: null,
  track_opens: true,
  track_clicks: true,
  google_analytics: '',
  scheduled_at: '',
  throttle_rate: '',
  delay_value: '',
  delay_unit: 'days',
  trigger_event: '',
  attachments: null,
});

const submit = () => {
  form.post(route('brands.campaigns.store', props.brand.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Create Campaign" />

  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-900">Create Campaign</h2>
            <Link
              :href="route('brands.campaigns.index', brand.id)"
              class="text-sm text-gray-600 hover:text-gray-900"
            >
              ← Back to Campaigns
            </Link>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Basic Information -->
              <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 gap-6">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Campaign Name *
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                      {{ form.errors.name }}
                    </div>
                  </div>

                  <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                      Campaign Type *
                    </label>
                    <select
                      id="type"
                      v-model="form.type"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                      <option v-for="(label, value) in types" :key="value" :value="value">
                        {{ label }}
                      </option>
                    </select>
                    <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                      {{ form.errors.type }}
                    </div>
                  </div>

                  <div>
                    <label for="list_id" class="block text-sm font-medium text-gray-700">
                      Email List *
                    </label>
                    <select
                      id="list_id"
                      v-model="form.list_id"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    >
                      <option value="">Select a list</option>
                      <option v-for="list in lists" :key="list.id" :value="list.id">
                        {{ list.name }} ({{ list.subscriber_count }} subscribers)
                      </option>
                    </select>
                    <div v-if="form.errors.list_id" class="mt-1 text-sm text-red-600">
                      {{ form.errors.list_id }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Email Details -->
              <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Details</h3>
                
                <div class="grid grid-cols-1 gap-6">
                  <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">
                      Subject Line *
                    </label>
                    <input
                      id="subject"
                      v-model="form.subject"
                      type="text"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      required
                    />
                    <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                      {{ form.errors.subject }}
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label for="from_name" class="block text-sm font-medium text-gray-700">
                        From Name *
                      </label>
                      <input
                        id="from_name"
                        v-model="form.from_name"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                      />
                      <div v-if="form.errors.from_name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.from_name }}
                      </div>
                    </div>

                    <div>
                      <label for="from_email" class="block text-sm font-medium text-gray-700">
                        From Email *
                      </label>
                      <input
                        id="from_email"
                        v-model="form.from_email"
                        type="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                      />
                      <div v-if="form.errors.from_email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.from_email }}
                      </div>
                    </div>
                  </div>

                  <div>
                    <label for="reply_to_email" class="block text-sm font-medium text-gray-700">
                      Reply-To Email
                    </label>
                    <input
                      id="reply_to_email"
                      v-model="form.reply_to_email"
                      type="email"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <div v-if="form.errors.reply_to_email" class="mt-1 text-sm text-red-600">
                      {{ form.errors.reply_to_email }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Email Content -->
              <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>
                
                <div class="space-y-6">
                  <div>
                    <label for="html_content" class="block text-sm font-medium text-gray-700">
                      HTML Content *
                    </label>
                    <textarea
                      id="html_content"
                      v-model="form.html_content"
                      rows="10"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"
                      required
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                      Full WYSIWYG editor coming soon. For now, enter HTML directly.
                    </p>
                    <div v-if="form.errors.html_content" class="mt-1 text-sm text-red-600">
                      {{ form.errors.html_content }}
                    </div>
                  </div>

                  <div>
                    <label for="plain_text_content" class="block text-sm font-medium text-gray-700">
                      Plain Text Content
                    </label>
                    <textarea
                      id="plain_text_content"
                      v-model="form.plain_text_content"
                      rows="6"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                      Optional plain text version for email clients that don't support HTML.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Tracking Options -->
              <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tracking & Analytics</h3>
                
                <div class="space-y-4">
                  <div class="flex items-start">
                    <div class="flex h-5 items-center">
                      <input
                        id="track_opens"
                        v-model="form.track_opens"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="track_opens" class="font-medium text-gray-700">Track Opens</label>
                      <p class="text-gray-500">Track when recipients open this email</p>
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex h-5 items-center">
                      <input
                        id="track_clicks"
                        v-model="form.track_clicks"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="track_clicks" class="font-medium text-gray-700">Track Clicks</label>
                      <p class="text-gray-500">Track when recipients click links in this email</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-end space-x-3 pt-4">
                <Link
                  :href="route('brands.campaigns.index', brand.id)"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-50"
                >
                  {{ form.processing ? 'Creating...' : 'Create Campaign' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
