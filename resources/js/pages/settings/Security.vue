<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { onUnmounted, ref } from 'vue';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { edit } from '@/routes/security';
import { disable, enable } from '@/routes/two-factor';

type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    canManageTwoFactor: false,
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Security settings',
                href: edit(),
            },
        ],
    },
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => clearTwoFactorAuthData());
</script>

<template>
    <Head title="Security settings" />

    <h1 class="sr-only">Security settings</h1>

    <section class="d-flex flex-column ga-6">
        <Heading
            variant="small"
            title="Update password"
            description="Ensure your account is using a long, random password to stay secure"
        />

        <Form
            v-bind="SecurityController.update.form()"
            :options="{ preserveScroll: true }"
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            v-slot="{ errors, processing }"
        >
            <div class="d-flex flex-column ga-4">
                <PasswordInput
                    id="current_password"
                    name="current_password"
                    label="Current password"
                    autocomplete="current-password"
                    :error-messages="errors.current_password"
                />
                <PasswordInput
                    id="password"
                    name="password"
                    label="New password"
                    autocomplete="new-password"
                    :error-messages="errors.password"
                />
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    label="Confirm password"
                    autocomplete="new-password"
                    :error-messages="errors.password_confirmation"
                />
                <div>
                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="processing"
                        :disabled="processing"
                        data-test="update-password-button"
                    >
                        Save password
                    </v-btn>
                </div>
            </div>
        </Form>
    </section>

    <section v-if="canManageTwoFactor" class="d-flex flex-column ga-6">
        <Heading
            variant="small"
            title="Two-factor authentication"
            description="Manage your two-factor authentication settings"
        />

        <template v-if="!twoFactorEnabled">
            <p class="text-body-2 text-medium-emphasis">
                When you enable two-factor authentication, you will be prompted
                for a secure pin during login. This pin can be retrieved from a
                TOTP-supported application on your phone.
            </p>

            <div>
                <v-btn
                    v-if="hasSetupData"
                    color="primary"
                    prepend-icon="mdi-shield-check-outline"
                    @click="showSetupModal = true"
                >
                    Continue setup
                </v-btn>
                <Form
                    v-else
                    v-bind="enable.form()"
                    @success="showSetupModal = true"
                    #default="{ processing }"
                >
                    <v-btn
                        type="submit"
                        color="primary"
                        prepend-icon="mdi-shield-key-outline"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Enable 2FA
                    </v-btn>
                </Form>
            </div>
        </template>

        <template v-else>
            <p class="text-body-2 text-medium-emphasis">
                You will be prompted for a secure, random pin during login,
                which you can retrieve from the TOTP-supported application on
                your phone.
            </p>

            <div>
                <Form v-bind="disable.form()" #default="{ processing }">
                    <v-btn
                        type="submit"
                        color="error"
                        prepend-icon="mdi-shield-off-outline"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Disable 2FA
                    </v-btn>
                </Form>
            </div>

            <TwoFactorRecoveryCodes />
        </template>

        <TwoFactorSetupModal
            v-model:isOpen="showSetupModal"
            :requiresConfirmation="requiresConfirmation"
            :twoFactorEnabled="twoFactorEnabled"
        />
    </section>
</template>
