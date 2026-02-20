<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    setting: Object,
    timezones: Array,
    awsRegions: Object,
});

const form = useForm({
    company_name: props.setting.company_name || '',
    timezone: props.setting.timezone || Intl.DateTimeFormat().resolvedOptions().timeZone,
    language: props.setting.language || 'en',
    aws_access_key: props.setting.aws_access_key || '',
    aws_secret_key: props.setting.aws_secret_key || '',
    aws_region: props.setting.aws_region || '',
    aws_sending_rate_limit: props.setting.aws_sending_rate_limit || 14,
    smtp_host: props.setting.smtp_host || '',
    smtp_port: props.setting.smtp_port || '',
    smtp_username: props.setting.smtp_username || '',
    smtp_password: props.setting.smtp_password || '',
    smtp_encryption: props.setting.smtp_encryption || 'tls',
    smtp_from_address: props.setting.smtp_from_address || '',
    smtp_from_name: props.setting.smtp_from_name || '',
});

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <div>
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">
                    Application Settings
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Configure your application settings and credentials.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- General Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="company_name" value="Company Name" />
                                <TextInput
                                    id="company_name"
                                    v-model="form.company_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.company_name" />
                            </div>

                            <div>
                                <InputLabel for="timezone" value="Default Timezone" />
                                <select
                                    id="timezone"
                                    v-model="form.timezone"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option v-for="tz in timezones" :key="tz" :value="tz">
                                        {{ tz }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.timezone" />
                            </div>

                            <div>
                                <InputLabel for="language" value="Default Language" />
                                <select
                                    id="language"
                                    v-model="form.language"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="de">German</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.language" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AWS Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">AWS Credentials</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="aws_access_key" value="Access Key" />
                                <TextInput
                                    id="aws_access_key"
                                    v-model="form.aws_access_key"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.aws_access_key" />
                            </div>

                            <div>
                                <InputLabel for="aws_secret_key" value="Secret Key" />
                                <TextInput
                                    id="aws_secret_key"
                                    v-model="form.aws_secret_key"
                                    type="password"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.aws_secret_key" />
                            </div>

                            <div>
                                <InputLabel for="aws_region" value="Region" />
                                <select
                                    id="aws_region"
                                    v-model="form.aws_region"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Select a region</option>
                                    <option v-for="(label, value) in awsRegions" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.aws_region" />
                            </div>

                            <div>
                                <InputLabel for="aws_sending_rate_limit" value="Sending Rate Limit (per second)" />
                                <TextInput
                                    id="aws_sending_rate_limit"
                                    v-model="form.aws_sending_rate_limit"
                                    type="number"
                                    min="1"
                                    max="50"
                                    class="mt-1 block w-full"
                                />
                                <p class="mt-1 text-sm text-gray-500">Default: 14 emails per second</p>
                                <InputError class="mt-2" :message="form.errors.aws_sending_rate_limit" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMTP Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">SMTP Settings</h3>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="smtp_host" value="SMTP Host" />
                                    <TextInput
                                        id="smtp_host"
                                        v-model="form.smtp_host"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.smtp_host" />
                                </div>

                                <div>
                                    <InputLabel for="smtp_port" value="SMTP Port" />
                                    <TextInput
                                        id="smtp_port"
                                        v-model="form.smtp_port"
                                        type="number"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.smtp_port" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="smtp_username" value="SMTP Username" />
                                    <TextInput
                                        id="smtp_username"
                                        v-model="form.smtp_username"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.smtp_username" />
                                </div>

                                <div>
                                    <InputLabel for="smtp_password" value="SMTP Password" />
                                    <TextInput
                                        id="smtp_password"
                                        v-model="form.smtp_password"
                                        type="password"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.smtp_password" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="smtp_encryption" value="Encryption" />
                                <select
                                    id="smtp_encryption"
                                    v-model="form.smtp_encryption"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.smtp_encryption" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="smtp_from_address" value="From Email Address" />
                                    <TextInput
                                        id="smtp_from_address"
                                        v-model="form.smtp_from_address"
                                        type="email"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.smtp_from_address" />
                                </div>

                                <div>
                                    <InputLabel for="smtp_from_name" value="From Name" />
                                    <TextInput
                                        id="smtp_from_name"
                                        v-model="form.smtp_from_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.smtp_from_name" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">
                        Save Settings
                    </PrimaryButton>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                            Saved.
                        </p>
                    </Transition>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
