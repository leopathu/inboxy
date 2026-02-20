<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    brand: Object,
});

const expandedSections = ref({
    smtp: false,
    recaptcha: false,
    gdpr: false,
    privacy: false,
    other: false,
});

const toggleSection = (section) => {
    expandedSections.value[section] = !expandedSections.value[section];
};

const form = useForm({
    name: props.brand.name,
    from_name: props.brand.from_name,
    from_email: props.brand.from_email,
    reply_to_email: props.brand.reply_to_email,
    brand_logo: props.brand.brand_logo || '',
    smtp_host: props.brand.smtp_host || '',
    smtp_port: props.brand.smtp_port || 587,
    smtp_username: props.brand.smtp_username || '',
    smtp_password: props.brand.smtp_password || '',
    smtp_encryption: props.brand.smtp_encryption || 'tls',
    smtp_from_address: props.brand.smtp_from_address || '',
    smtp_from_name: props.brand.smtp_from_name || '',
    recaptcha_site_key: props.brand.recaptcha_site_key || '',
    recaptcha_secret_key: props.brand.recaptcha_secret_key || '',
    show_gdpr_options: props.brand.show_gdpr_options,
    only_campaigns_with_gdpr: props.brand.only_campaigns_with_gdpr,
    only_autoresponders_with_gdpr: props.brand.only_autoresponders_with_gdpr,
    track_opens: props.brand.track_opens,
    track_clicks: props.brand.track_clicks,
    default_url_query_string: props.brand.default_url_query_string || '',
    test_email_subject_prefix: props.brand.test_email_subject_prefix || '[TEST]',
    allowed_attachment_types: props.brand.allowed_attachment_types || [],
    default_optin_method: props.brand.default_optin_method,
});

const submit = () => {
    form.put(route('brands.update', props.brand.id));
};

const attachmentTypes = [
    { value: 'pdf', label: 'PDF' },
    { value: 'doc', label: 'DOC' },
    { value: 'docx', label: 'DOCX' },
    { value: 'xls', label: 'XLS' },
    { value: 'xlsx', label: 'XLSX' },
    { value: 'jpg', label: 'JPG' },
    { value: 'jpeg', label: 'JPEG' },
    { value: 'png', label: 'PNG' },
    { value: 'gif', label: 'GIF' },
    { value: 'zip', label: 'ZIP' },
    { value: 'csv', label: 'CSV' },
];

const toggleAttachmentType = (type) => {
    const index = form.allowed_attachment_types.indexOf(type);
    if (index > -1) {
        form.allowed_attachment_types.splice(index, 1);
    } else {
        form.allowed_attachment_types.push(type);
    }
};
</script>

<template>
    <Head title="Edit Brand" />

    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Brand</h1>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Brand Name *</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label for="from_name" class="block text-sm font-medium text-gray-700">From Name *</label>
                            <input
                                type="text"
                                id="from_name"
                                v-model="form.from_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            />
                            <div v-if="form.errors.from_name" class="mt-1 text-sm text-red-600">{{ form.errors.from_name }}</div>
                        </div>

                        <div>
                            <label for="from_email" class="block text-sm font-medium text-gray-700">From Email *</label>
                            <input
                                type="email"
                                id="from_email"
                                v-model="form.from_email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            />
                            <div v-if="form.errors.from_email" class="mt-1 text-sm text-red-600">{{ form.errors.from_email }}</div>
                        </div>

                        <div>
                            <label for="reply_to_email" class="block text-sm font-medium text-gray-700">Reply to Email *</label>
                            <input
                                type="email"
                                id="reply_to_email"
                                v-model="form.reply_to_email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            />
                            <div v-if="form.errors.reply_to_email" class="mt-1 text-sm text-red-600">{{ form.errors.reply_to_email }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="brand_logo" class="block text-sm font-medium text-gray-700">Brand Logo URL</label>
                            <input
                                type="text"
                                id="brand_logo"
                                v-model="form.brand_logo"
                                placeholder="https://example.com/logo.png"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.brand_logo" class="mt-1 text-sm text-red-600">{{ form.errors.brand_logo }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMTP Settings - Collapsible -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <button
                    type="button"
                    @click="toggleSection('smtp')"
                    class="w-full border-b border-gray-200 bg-white px-6 py-4 text-left focus:outline-none hover:bg-gray-50"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">SMTP Settings</h2>
                        <svg
                            :class="['h-5 w-5 text-gray-500 transition-transform', expandedSections.smtp ? 'rotate-180' : '']"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div v-show="expandedSections.smtp" class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="smtp_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                            <input
                                type="text"
                                id="smtp_host"
                                v-model="form.smtp_host"
                                placeholder="smtp.example.com"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.smtp_host" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_host }}</div>
                        </div>

                        <div>
                            <label for="smtp_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                            <input
                                type="number"
                                id="smtp_port"
                                v-model="form.smtp_port"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.smtp_port" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_port }}</div>
                        </div>

                        <div>
                            <label for="smtp_username" class="block text-sm font-medium text-gray-700">SMTP Username</label>
                            <input
                                type="text"
                                id="smtp_username"
                                v-model="form.smtp_username"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.smtp_username" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_username }}</div>
                        </div>

                        <div>
                            <label for="smtp_password" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                            <input
                                type="password"
                                id="smtp_password"
                                v-model="form.smtp_password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.smtp_password" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_password }}</div>
                        </div>

                        <div>
                            <label for="smtp_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                            <select
                                id="smtp_encryption"
                                v-model="form.smtp_encryption"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="none">None</option>
                            </select>
                            <div v-if="form.errors.smtp_encryption" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_encryption }}</div>
                        </div>

                        <div>
                            <label for="smtp_from_address" class="block text-sm font-medium text-gray-700">SMTP From Address</label>
                            <input
                                type="email"
                                id="smtp_from_address"
                                v-model="form.smtp_from_address"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.smtp_from_address" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_from_address }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="smtp_from_name" class="block text-sm font-medium text-gray-700">SMTP From Name</label>
                            <input
                                type="text"
                                id="smtp_from_name"
                                v-model="form.smtp_from_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.smtp_from_name" class="mt-1 text-sm text-red-600">{{ form.errors.smtp_from_name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google reCAPTCHA - Collapsible -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <button
                    type="button"
                    @click="toggleSection('recaptcha')"
                    class="w-full border-b border-gray-200 bg-white px-6 py-4 text-left focus:outline-none hover:bg-gray-50"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Google reCAPTCHA</h2>
                        <svg
                            :class="['h-5 w-5 text-gray-500 transition-transform', expandedSections.recaptcha ? 'rotate-180' : '']"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div v-show="expandedSections.recaptcha" class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700">Site Key</label>
                            <input
                                type="text"
                                id="recaptcha_site_key"
                                v-model="form.recaptcha_site_key"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.recaptcha_site_key" class="mt-1 text-sm text-red-600">{{ form.errors.recaptcha_site_key }}</div>
                        </div>

                        <div>
                            <label for="recaptcha_secret_key" class="block text-sm font-medium text-gray-700">Secret Key</label>
                            <input
                                type="text"
                                id="recaptcha_secret_key"
                                v-model="form.recaptcha_secret_key"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.recaptcha_secret_key" class="mt-1 text-sm text-red-600">{{ form.errors.recaptcha_secret_key }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GDPR Features - Collapsible -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <button
                    type="button"
                    @click="toggleSection('gdpr')"
                    class="w-full border-b border-gray-200 bg-white px-6 py-4 text-left focus:outline-none hover:bg-gray-50"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">GDPR Features</h2>
                        <svg
                            :class="['h-5 w-5 text-gray-500 transition-transform', expandedSections.gdpr ? 'rotate-180' : '']"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div v-show="expandedSections.gdpr" class="px-6 py-4 space-y-4">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="show_gdpr_options"
                            v-model="form.show_gdpr_options"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label for="show_gdpr_options" class="ml-3 block text-sm font-medium text-gray-700">
                            Show me GDPR options where applicable
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="only_campaigns_with_gdpr"
                            v-model="form.only_campaigns_with_gdpr"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label for="only_campaigns_with_gdpr" class="ml-3 block text-sm font-medium text-gray-700">
                            Only send Campaigns to subscribers with GDPR tag
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="only_autoresponders_with_gdpr"
                            v-model="form.only_autoresponders_with_gdpr"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label for="only_autoresponders_with_gdpr" class="ml-3 block text-sm font-medium text-gray-700">
                            Only send Autoresponders to subscribers with GDPR tag
                        </label>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings - Collapsible -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <button
                    type="button"
                    @click="toggleSection('privacy')"
                    class="w-full border-b border-gray-200 bg-white px-6 py-4 text-left focus:outline-none hover:bg-gray-50"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Privacy Settings</h2>
                        <svg
                            :class="['h-5 w-5 text-gray-500 transition-transform', expandedSections.privacy ? 'rotate-180' : '']"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div v-show="expandedSections.privacy" class="px-6 py-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <label for="track_opens" class="text-sm font-medium text-gray-700">Track Opens</label>
                        <button
                            type="button"
                            @click="form.track_opens = !form.track_opens"
                            :class="[
                                form.track_opens ? 'bg-indigo-600' : 'bg-gray-200',
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                            ]"
                        >
                            <span
                                :class="[
                                    form.track_opens ? 'translate-x-5' : 'translate-x-0',
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                ]"
                            />
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="track_clicks" class="text-sm font-medium text-gray-700">Track Clicks</label>
                        <button
                            type="button"
                            @click="form.track_clicks = !form.track_clicks"
                            :class="[
                                form.track_clicks ? 'bg-indigo-600' : 'bg-gray-200',
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                            ]"
                        >
                            <span
                                :class="[
                                    form.track_clicks ? 'translate-x-5' : 'translate-x-0',
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                ]"
                            />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Other Settings - Collapsible -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <button
                    type="button"
                    @click="toggleSection('other')"
                    class="w-full border-b border-gray-200 bg-white px-6 py-4 text-left focus:outline-none hover:bg-gray-50"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Other Settings</h2>
                        <svg
                            :class="['h-5 w-5 text-gray-500 transition-transform', expandedSections.other ? 'rotate-180' : '']"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div v-show="expandedSections.other" class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="default_url_query_string" class="block text-sm font-medium text-gray-700">Default URL Query String</label>
                            <input
                                type="text"
                                id="default_url_query_string"
                                v-model="form.default_url_query_string"
                                placeholder="utm_source=newsletter&utm_medium=email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.default_url_query_string" class="mt-1 text-sm text-red-600">{{ form.errors.default_url_query_string }}</div>
                        </div>

                        <div>
                            <label for="test_email_subject_prefix" class="block text-sm font-medium text-gray-700">Prefix for Subject Line of Test Emails</label>
                            <input
                                type="text"
                                id="test_email_subject_prefix"
                                v-model="form.test_email_subject_prefix"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <div v-if="form.errors.test_email_subject_prefix" class="mt-1 text-sm text-red-600">{{ form.errors.test_email_subject_prefix }}</div>
                        </div>

                        <div>
                            <label for="default_optin_method" class="block text-sm font-medium text-gray-700">Default Opt-in Method *</label>
                            <select
                                id="default_optin_method"
                                v-model="form.default_optin_method"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            >
                                <option value="single">Single Opt-in</option>
                                <option value="double">Double Opt-in</option>
                            </select>
                            <div v-if="form.errors.default_optin_method" class="mt-1 text-sm text-red-600">{{ form.errors.default_optin_method }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Attachment File Types</label>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            <div v-for="type in attachmentTypes" :key="type.value" class="flex items-center">
                                <input
                                    type="checkbox"
                                    :id="`attachment_${type.value}`"
                                    :value="type.value"
                                    :checked="form.allowed_attachment_types.includes(type.value)"
                                    @change="toggleAttachmentType(type.value)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label :for="`attachment_${type.value}`" class="ml-2 block text-sm text-gray-700">
                                    {{ type.label }}
                                </label>
                            </div>
                        </div>
                        <div v-if="form.errors.allowed_attachment_types" class="mt-1 text-sm text-red-600">{{ form.errors.allowed_attachment_types }}</div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4">
                <Link
                    :href="route('brands.index')"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    <span v-if="form.processing">Updating...</span>
                    <span v-else>Update Brand</span>
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
