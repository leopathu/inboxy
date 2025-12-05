<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

interface Brand {
    id: number;
    name: string;
}

interface EmailList {
    id: number;
    name: string;
    from_name: string;
    from_email: string;
}

interface CustomField {
    id: number;
    name: string;
    tag: string;
    type: string;
    is_required: boolean;
}

interface Props {
    brand: Brand;
    list: EmailList;
    customFields: CustomField[];
}

const props = defineProps<Props>();

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
    name: '',
    description: '',
    enable_double_optin: true,
    send_confirmation_email: true,
    is_active: true,
    visible_fields: [] as number[],
    required_fields: [] as number[],
    success_redirect_url: '',
    failure_redirect_url: '',
    confirmation_redirect_url: '',
    success_message: 'Thank you for subscribing!',
    already_subscribed_message: 'You are already subscribed to this list.',
    confirmation_pending_message: 'Please check your email to confirm your subscription.',
    confirmation_email_subject: 'Please confirm your subscription',
    confirmation_email_body: "Hello!\n\nPlease click the link below to confirm your subscription:\n\n{{confirmation_link}}\n\nIf you didn't request this, please ignore this email.\n\nThank you!",
    welcome_email_subject: 'Welcome! Your subscription is confirmed',
    welcome_email_body: `Hello {{name}}!\n\nThank you for subscribing to ${props.list.name}.\n\nYou'll receive updates from us at {{email}}.\n\nThank you!`,
    submit_button_text: 'Subscribe',
    primary_color: '#3490dc',
    custom_css: '',
    custom_html: '',
    enable_captcha: false,
    captcha_type: '',
    captcha_site_key: '',
    captcha_secret_key: '',
});

const canProceed = computed(() => {
    if (currentStep.value === 1) {
        return form.name.length > 0;
    }
    return true;
});

const nextStep = () => {
    if (canProceed.value && currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const toggleFieldVisibility = (fieldId: number) => {
    const index = form.visible_fields.indexOf(fieldId);
    if (index > -1) {
        form.visible_fields.splice(index, 1);
        // Also remove from required if hiding
        const reqIndex = form.required_fields.indexOf(fieldId);
        if (reqIndex > -1) {
            form.required_fields.splice(reqIndex, 1);
        }
    } else {
        form.visible_fields.push(fieldId);
    }
};

const toggleFieldRequired = (fieldId: number) => {
    if (!form.visible_fields.includes(fieldId)) {
        return; // Can't require a hidden field
    }
    const index = form.required_fields.indexOf(fieldId);
    if (index > -1) {
        form.required_fields.splice(index, 1);
    } else {
        form.required_fields.push(fieldId);
    }
};

const submit = () => {
    form.post(route('brands.lists.subscription-forms.store', [props.brand.id, props.list.id]));
};
</script>

<template>
    <Head :title="`Create Subscription Form - ${list.name} - ${brand.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Create Subscription Form</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ list.name }}</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div v-for="step in totalSteps" :key="step" class="flex-1">
                            <div class="flex items-center">
                                <div :class="[
                                    'w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium border-2',
                                    step === currentStep ? 'bg-blue-600 border-blue-600 text-white' :
                                    step < currentStep ? 'bg-blue-100 border-blue-600 text-blue-600' :
                                    'bg-gray-100 border-gray-300 text-gray-400'
                                ]">
                                    {{ step }}
                                </div>
                                <div v-if="step < totalSteps" :class="[
                                    'flex-1 h-0.5 mx-2',
                                    step < currentStep ? 'bg-blue-600' : 'bg-gray-300'
                                ]" />
                            </div>
                            <div class="mt-2 text-xs text-center">
                                <span v-if="step === 1">Basic Info</span>
                                <span v-if="step === 2">Fields & Settings</span>
                                <span v-if="step === 3">Email Templates</span>
                                <span v-if="step === 4">Design & URLs</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Step 1: Basic Info -->
                        <div v-show="currentStep === 1" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Form Name <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                <p class="mt-1 text-xs text-gray-500">Internal name to identify this form</p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">Optional description for your reference</p>
                            </div>

                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active (form will be publicly accessible)
                                </label>
                            </div>
                        </div>

                        <!-- Step 2: Fields & Settings -->
                        <div v-show="currentStep === 2" class="space-y-6">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Subscription Settings</h3>
                                
                                <div class="flex items-center">
                                    <input
                                        id="enable_double_optin"
                                        v-model="form.enable_double_optin"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label for="enable_double_optin" class="ml-2 block text-sm text-gray-900">
                                        Enable double opt-in (recommended)
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 ml-6">Subscribers must confirm via email before being added to the list</p>

                                <div class="flex items-center">
                                    <input
                                        id="send_confirmation_email"
                                        v-model="form.send_confirmation_email"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label for="send_confirmation_email" class="ml-2 block text-sm text-gray-900">
                                        Send confirmation/welcome email
                                    </label>
                                </div>
                            </div>

                            <div v-if="customFields.length > 0" class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Custom Fields</h3>
                                <p class="text-sm text-gray-600">Select which custom fields to show in the form</p>
                                
                                <div class="border rounded-lg divide-y">
                                    <div v-for="field in customFields" :key="field.id" class="p-4 hover:bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <input
                                                    :id="`field-${field.id}`"
                                                    type="checkbox"
                                                    :checked="form.visible_fields.includes(field.id)"
                                                    @change="toggleFieldVisibility(field.id)"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                />
                                                <label :for="`field-${field.id}`" class="text-sm font-medium text-gray-900">
                                                    {{ field.name }}
                                                </label>
                                                <span class="text-xs text-gray-500">{{ '{' + '{' + field.tag + '}' + '}' }}</span>
                                            </div>
                                            <div v-if="form.visible_fields.includes(field.id)" class="flex items-center">
                                                <input
                                                    :id="`required-${field.id}`"
                                                    type="checkbox"
                                                    :checked="form.required_fields.includes(field.id)"
                                                    @change="toggleFieldRequired(field.id)"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                />
                                                <label :for="`required-${field.id}`" class="ml-2 text-sm text-gray-700">
                                                    Required
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Email Templates -->
                        <div v-show="currentStep === 3" class="space-y-6">
                            <div v-if="form.enable_double_optin">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmation Email</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="confirmation_email_subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                        <input
                                            id="confirmation_email_subject"
                                            v-model="form.confirmation_email_subject"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label for="confirmation_email_body" class="block text-sm font-medium text-gray-700">Body</label>
                                        <textarea
                                            id="confirmation_email_body"
                                            v-model="form.confirmation_email_body"
                                            rows="6"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500">Available tags: &#123;&#123;confirmation_link&#125;&#125;, &#123;&#123;name&#125;&#125;, &#123;&#123;email&#125;&#125;</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Welcome Email</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="welcome_email_subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                        <input
                                            id="welcome_email_subject"
                                            v-model="form.welcome_email_subject"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label for="welcome_email_body" class="block text-sm font-medium text-gray-700">Body</label>
                                        <textarea
                                            id="welcome_email_body"
                                            v-model="form.welcome_email_body"
                                            rows="6"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500">Available tags: &#123;&#123;name&#125;&#125;, &#123;&#123;email&#125;&#125;, &#123;&#123;first_name&#125;&#125;, &#123;&#123;last_name&#125;&#125;</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Design & URLs -->
                        <div v-show="currentStep === 4" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Design</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="submit_button_text" class="block text-sm font-medium text-gray-700">Button Text</label>
                                        <input
                                            id="submit_button_text"
                                            v-model="form.submit_button_text"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Primary Color</label>
                                        <input
                                            id="primary_color"
                                            v-model="form.primary_color"
                                            type="color"
                                            class="mt-1 block w-full h-10 border-gray-300 rounded-md shadow-sm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Messages</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="success_message" class="block text-sm font-medium text-gray-700">Success Message</label>
                                        <input
                                            id="success_message"
                                            v-model="form.success_message"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>

                                    <div v-if="form.enable_double_optin">
                                        <label for="confirmation_pending_message" class="block text-sm font-medium text-gray-700">Confirmation Pending Message</label>
                                        <input
                                            id="confirmation_pending_message"
                                            v-model="form.confirmation_pending_message"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label for="already_subscribed_message" class="block text-sm font-medium text-gray-700">Already Subscribed Message</label>
                                        <input
                                            id="already_subscribed_message"
                                            v-model="form.already_subscribed_message"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Redirect URLs (Optional)</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="success_redirect_url" class="block text-sm font-medium text-gray-700">Success Redirect URL</label>
                                        <input
                                            id="success_redirect_url"
                                            v-model="form.success_redirect_url"
                                            type="url"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="https://example.com/thank-you"
                                        />
                                    </div>

                                    <div v-if="form.enable_double_optin">
                                        <label for="confirmation_redirect_url" class="block text-sm font-medium text-gray-700">Confirmation Redirect URL</label>
                                        <input
                                            id="confirmation_redirect_url"
                                            v-model="form.confirmation_redirect_url"
                                            type="url"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="https://example.com/confirmed"
                                        />
                                    </div>

                                    <div>
                                        <label for="failure_redirect_url" class="block text-sm font-medium text-gray-700">Failure Redirect URL</label>
                                        <input
                                            id="failure_redirect_url"
                                            v-model="form.failure_redirect_url"
                                            type="url"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="https://example.com/error"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex items-center justify-between pt-6 border-t">
                            <div>
                                <Link
                                    :href="route('brands.lists.subscription-forms.index', [brand.id, list.id])"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <button
                                    v-if="currentStep > 1"
                                    type="button"
                                    @click="prevStep"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                                >
                                    Previous
                                </button>
                                
                                <button
                                    v-if="currentStep < totalSteps"
                                    type="button"
                                    @click="nextStep"
                                    :disabled="!canProceed"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
                                >
                                    Next
                                </button>
                                
                                <button
                                    v-if="currentStep === totalSteps"
                                    type="submit"
                                    :disabled="form.processing || !canProceed"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Form</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
