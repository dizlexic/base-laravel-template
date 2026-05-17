<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import AlertError from '@/components/AlertError.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { confirm } from '@/routes/two-factor';
import type { TwoFactorConfigContent } from '@/types';

type Props = {
    requiresConfirmation: boolean;
    twoFactorEnabled: boolean;
};

const { resolvedAppearance } = useAppearance();

const props = defineProps<Props>();
const isOpen = defineModel<boolean>('isOpen');

const { copy, copied } = useClipboard();
const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } =
    useTwoFactorAuth();

const showVerificationStep = ref(false);
const code = ref<string>('');

const modalConfig = computed<TwoFactorConfigContent>(() => {
    if (props.twoFactorEnabled) {
        return {
            title: 'Two-factor authentication enabled',
            description:
                'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
            buttonText: 'Close',
        };
    }

    if (showVerificationStep.value) {
        return {
            title: 'Verify authentication code',
            description: 'Enter the 6-digit code from your authenticator app',
            buttonText: 'Continue',
        };
    }

    return {
        title: 'Enable two-factor authentication',
        description:
            'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
        buttonText: 'Continue',
    };
});

const handleModalNextStep = () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;

        return;
    }

    clearSetupData();
    isOpen.value = false;
};

const resetModalState = () => {
    if (props.twoFactorEnabled) {
        clearSetupData();
    }

    showVerificationStep.value = false;
    code.value = '';
};

watch(
    () => isOpen.value,
    async (value) => {
        if (!value) {
            resetModalState();

            return;
        }

        if (!qrCodeSvg.value) {
            await fetchSetupData();
        }
    },
);
</script>

<template>
    <!--
        Two-step setup dialog: shows the authenticator QR code + manual setup
        key, then collects the 6-digit verification code via Vuetify's native
        `<v-otp-input>` (which removes our previous dependency on
        vue-input-otp).
    -->
    <v-dialog v-model="isOpen" max-width="480" persistent scrollable>
        <v-card>
            <v-card-item class="text-center">
                <template #prepend>
                    <v-avatar
                        size="56"
                        rounded="circle"
                        color="secondary"
                        class="mx-auto mb-2"
                    >
                        <v-icon icon="mdi-shield-key-outline" size="28" />
                    </v-avatar>
                </template>
                <v-card-title>{{ modalConfig.title }}</v-card-title>
                <v-card-subtitle class="text-pre-wrap">
                    {{ modalConfig.description }}
                </v-card-subtitle>
            </v-card-item>

            <v-card-text>
                <div class="d-flex flex-column align-center ga-5">
                    <template v-if="!showVerificationStep">
                        <AlertError v-if="errors?.length" :errors="errors" />
                        <template v-else>
                            <v-sheet
                                rounded="lg"
                                border
                                class="pa-4 d-flex align-center justify-center"
                                width="240"
                                height="240"
                            >
                                <v-progress-circular
                                    v-if="!qrCodeSvg"
                                    indeterminate
                                />
                                <div
                                    v-else
                                    v-html="qrCodeSvg"
                                    class="qr-code-frame"
                                    :style="{
                                        filter:
                                            resolvedAppearance === 'dark'
                                                ? 'invert(1) brightness(1.5)'
                                                : undefined,
                                    }"
                                />
                            </v-sheet>

                            <v-btn
                                block
                                color="primary"
                                @click="handleModalNextStep"
                            >
                                {{ modalConfig.buttonText }}
                            </v-btn>

                            <v-divider class="w-100">
                                <span class="text-caption text-medium-emphasis">
                                    or, enter the code manually
                                </span>
                            </v-divider>

                            <v-text-field
                                :model-value="manualSetupKey ?? ''"
                                readonly
                                hide-details
                                density="comfortable"
                                placeholder="Loading setup key…"
                                class="w-100"
                            >
                                <template #append-inner>
                                    <v-btn
                                        variant="text"
                                        :icon="
                                            copied
                                                ? 'mdi-check'
                                                : 'mdi-content-copy'
                                        "
                                        :color="copied ? 'success' : undefined"
                                        :disabled="!manualSetupKey"
                                        size="small"
                                        :aria-label="
                                            copied
                                                ? 'Copied to clipboard'
                                                : 'Copy setup key'
                                        "
                                        @click="copy(manualSetupKey || '')"
                                    />
                                </template>
                            </v-text-field>
                        </template>
                    </template>

                    <template v-else>
                        <Form
                            v-bind="confirm.form()"
                            error-bag="confirmTwoFactorAuthentication"
                            reset-on-error
                            @finish="code = ''"
                            @success="isOpen = false"
                            v-slot="{ errors: formErrors, processing }"
                            class="w-100"
                        >
                            <input type="hidden" name="code" :value="code" />
                            <div class="d-flex flex-column align-center ga-3">
                                <v-otp-input
                                    id="otp"
                                    v-model="code"
                                    :length="6"
                                    :disabled="processing"
                                    :error-messages="formErrors?.code"
                                    autofocus
                                />
                                <div class="d-flex w-100 ga-3">
                                    <v-btn
                                        type="button"
                                        variant="outlined"
                                        class="flex-grow-1"
                                        :disabled="processing"
                                        @click="showVerificationStep = false"
                                    >
                                        Back
                                    </v-btn>
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        class="flex-grow-1"
                                        :loading="processing"
                                        :disabled="
                                            processing || code.length < 6
                                        "
                                    >
                                        Confirm
                                    </v-btn>
                                </div>
                            </div>
                        </Form>
                    </template>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<style scoped>
.qr-code-frame :deep(svg) {
    width: 100%;
    height: 100%;
}
</style>
