<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import { store } from '@/routes/two-factor/login';
import type { TwoFactorConfigContent } from '@/types';

const showRecoveryInput = ref<boolean>(false);
const code = ref<string>('');

const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            buttonText: 'login using an authentication code',
        };
    }

    return {
        title: 'Authentication code',
        description:
            'Enter the authentication code provided by your authenticator application.',
        buttonText: 'login using a recovery code',
    };
});

watchEffect(() => {
    setLayoutProps({
        title: authConfigContent.value.title,
        description: authConfigContent.value.description,
    });
});

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
};
</script>

<template>
    <Head title="Two-factor authentication" />

    <template v-if="!showRecoveryInput">
        <Form
            v-bind="store.form()"
            reset-on-error
            @error="code = ''"
            #default="{ errors, processing, clearErrors }"
        >
            <input type="hidden" name="code" :value="code" />
            <div class="d-flex flex-column align-center ga-4">
                <v-otp-input
                    id="otp"
                    v-model="code"
                    :length="6"
                    :disabled="processing"
                    :error-messages="errors.code"
                    autofocus
                />
                <v-btn
                    type="submit"
                    color="primary"
                    block
                    size="large"
                    :loading="processing"
                    :disabled="processing || code.length < 6"
                >
                    Continue
                </v-btn>
                <p class="text-center text-body-2 text-medium-emphasis">
                    <span>or you can </span>
                    <v-btn
                        type="button"
                        variant="text"
                        density="comfortable"
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.buttonText }}
                    </v-btn>
                </p>
            </div>
        </Form>
    </template>

    <template v-else>
        <Form
            v-bind="store.form()"
            reset-on-error
            #default="{ errors, processing, clearErrors }"
        >
            <div class="d-flex flex-column ga-4">
                <v-text-field
                    name="recovery_code"
                    label="Recovery code"
                    placeholder="Enter recovery code"
                    :autofocus="showRecoveryInput"
                    required
                    :error-messages="errors.recovery_code"
                />
                <v-btn
                    type="submit"
                    color="primary"
                    block
                    size="large"
                    :loading="processing"
                    :disabled="processing"
                >
                    Continue
                </v-btn>
                <p class="text-center text-body-2 text-medium-emphasis">
                    <span>or you can </span>
                    <v-btn
                        type="button"
                        variant="text"
                        density="comfortable"
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.buttonText }}
                    </v-btn>
                </p>
            </div>
        </Form>
    </template>
</template>
