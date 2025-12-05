<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
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
    public_url: string;
    submissions_count: number;
    confirmed_count: number;
    pending_count: number;
    created_at: string;
}

interface Submission {
    id: number;
    email: string;
    status: string;
    created_at: string;
    confirmed_at: string | null;
    subscriber: {
        id: number;
        name: string;
        email: string;
    } | null;
}

interface Props {
    brand: Brand;
    list: EmailList;
    form: SubscriptionForm;
    recentSubmissions: Submission[];
}

const props = defineProps<Props>();

const activeTab = ref('overview');
const copiedMessage = ref('');

const copyToClipboard = (text: string, label: string) => {
    navigator.clipboard.writeText(text);
    copiedMessage.value = `${label} copied!`;
    setTimeout(() => {
        copiedMessage.value = '';
    }, 2000);
};

const embedHtmlCode = `<div id="inboxy-form-${props.form.identifier}"></div>
<script src="${window.location.origin}/forms/${props.form.identifier}/embed.js"><\/script>`;

const embedJsCode = `(function() {
    var script = document.createElement('script');
    script.src = '${window.location.origin}/forms/${props.form.identifier}/embed.js';
    script.async = true;
    document.body.appendChild(script);
})();`;

const conversionRate = props.form.submissions_count > 0 
    ? ((props.form.confirmed_count / props.form.submissions_count) * 100).toFixed(1) 
    : '0.0';

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        confirmed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
        spam: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`${form.name} - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ form.name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">{{ list.name }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a
                            :href="form.public_url"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        >
                            View Public Form
                        </a>
                        <Link
                            :href="route('brands.lists.subscription-forms.edit', [brand.id, list.id, form.id])"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        >
                            Edit Form
                        </Link>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600">Total Submissions</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ form.submissions_count }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600">Confirmed</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">{{ form.confirmed_count }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600">Pending</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">{{ form.pending_count }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-600">Conversion Rate</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ conversionRate }}%</div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <button
                                @click="activeTab = 'overview'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'overview'
                                        ? 'border-blue-500 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Overview
                            </button>
                            <button
                                @click="activeTab = 'embed'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'embed'
                                        ? 'border-blue-500 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Embed Codes
                            </button>
                            <button
                                @click="activeTab = 'submissions'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'submissions'
                                        ? 'border-blue-500 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Recent Submissions
                            </button>
                        </nav>
                    </div>

                    <!-- Overview Tab -->
                    <div v-show="activeTab === 'overview'" class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Form Details</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            form.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="mt-1">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            form.enable_double_optin ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ form.enable_double_optin ? 'Double Opt-in' : 'Single Opt-in' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Identifier</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ form.identifier }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ new Date(form.created_at).toLocaleString() }}</dd>
                                </div>
                                <div class="sm:col-span-2" v-if="form.description">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ form.description }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Public URL</h3>
                            <div class="flex items-center space-x-2">
                                <input
                                    type="text"
                                    :value="form.public_url"
                                    readonly
                                    class="flex-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-sm"
                                />
                                <button
                                    @click="copyToClipboard(form.public_url, 'URL')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    Copy
                                </button>
                            </div>
                            <p v-if="copiedMessage" class="mt-2 text-sm text-green-600">{{ copiedMessage }}</p>
                        </div>
                    </div>

                    <!-- Embed Codes Tab -->
                    <div v-show="activeTab === 'embed'" class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">HTML Embed Code</h3>
                            <p class="text-sm text-gray-600 mb-4">Add this code to your website where you want the form to appear.</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{{ embedHtmlCode }}</code></pre>
                                <button
                                    @click="copyToClipboard(embedHtmlCode, 'HTML code')"
                                    class="absolute top-2 right-2 inline-flex items-center px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-xs rounded"
                                >
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">JavaScript Embed Code</h3>
                            <p class="text-sm text-gray-600 mb-4">Alternative async loading method for better performance.</p>
                            <div class="relative">
                                <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{{ embedJsCode }}</code></pre>
                                <button
                                    @click="copyToClipboard(embedJsCode, 'JavaScript code')"
                                    class="absolute top-2 right-2 inline-flex items-center px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white text-xs rounded"
                                >
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900">WordPress Integration</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                For WordPress sites, you can add the HTML embed code to any post or page using the Custom HTML block, or add it to your theme templates using PHP.
                            </p>
                        </div>
                    </div>

                    <!-- Submissions Tab -->
                    <div v-show="activeTab === 'submissions'" class="p-6">
                        <div v-if="recentSubmissions.length === 0" class="text-center py-12">
                            <p class="text-gray-500">No submissions yet</p>
                        </div>
                        <div v-else>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Submissions</h3>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmed</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="submission in recentSubmissions" :key="submission.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ submission.subscriber?.name || 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ submission.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                getStatusColor(submission.status)
                                            ]">
                                                {{ submission.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(submission.created_at).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ submission.confirmed_at ? new Date(submission.confirmed_at).toLocaleString() : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
