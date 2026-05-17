<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { nextTick, onMounted, ref, useTemplateRef } from 'vue';
import AlertError from '@/components/AlertError.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { regenerateRecoveryCodes } from '@/routes/two-factor';

const { recoveryCodesList, fetchRecoveryCodes, errors } = useTwoFactorAuth();
const isRecoveryCodesVisible = ref<boolean>(false);
const recoveryCodeSectionRef = useTemplateRef('recoveryCodeSectionRef');

const toggleRecoveryCodesVisibility = async () => {
    if (!isRecoveryCodesVisible.value && !recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }

    isRecoveryCodesVisible.value = !isRecoveryCodesVisible.value;

    if (isRecoveryCodesVisible.value) {
        await nextTick();
        // `recoveryCodeSectionRef` resolves to a Vuetify component instance;
        // its rendered DOM node is exposed via `$el`.
        const el = recoveryCodeSectionRef.value?.$el as HTMLElement | undefined;
        el?.scrollIntoView({ behavior: 'smooth' });
    }
};

onMounted(async () => {
    if (!recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }
});
</script>

<template>
    <v-card>
        <v-card-title class="d-flex align-center ga-2">
            <v-icon icon="mdi-lock-outline" size="20" />
            <span>2FA recovery codes</span>
        </v-card-title>
        <v-card-subtitle>
            Recovery codes let you regain access if you lose your 2FA device.
            Store them in a secure password manager.
        </v-card-subtitle>
        <v-card-text>
            <div
                class="d-flex flex-column flex-sm-row ga-3 justify-space-between align-sm-center"
            >
                <v-btn
                    :prepend-icon="
                        isRecoveryCodesVisible ? 'mdi-eye-off' : 'mdi-eye'
                    "
                    color="primary"
                    @click="toggleRecoveryCodesVisibility"
                >
                    {{ isRecoveryCodesVisible ? 'Hide' : 'View' }} recovery
                    codes
                </v-btn>

                <Form
                    v-if="isRecoveryCodesVisible && recoveryCodesList.length"
                    v-bind="regenerateRecoveryCodes.form()"
                    method="post"
                    :options="{ preserveScroll: true }"
                    @success="fetchRecoveryCodes"
                    #default="{ processing }"
                >
                    <v-btn
                        variant="tonal"
                        prepend-icon="mdi-refresh"
                        type="submit"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Regenerate codes
                    </v-btn>
                </Form>
            </div>
            <v-expand-transition>
                <div v-show="isRecoveryCodesVisible" class="mt-6">
                    <AlertError v-if="errors?.length" :errors="errors" />
                    <template v-else>
                        <v-sheet
                            ref="recoveryCodeSectionRef"
                            color="secondary"
                            rounded="lg"
                            class="pa-4 mb-3"
                        >
                            <div
                                v-if="!recoveryCodesList.length"
                                class="d-flex flex-column ga-2"
                            >
                                <v-skeleton-loader
                                    v-for="n in 8"
                                    :key="n"
                                    type="text"
                                />
                            </div>
                            <div
                                v-else
                                class="recovery-codes-grid font-monospace text-body-2"
                            >
                                <div
                                    v-for="(code, index) in recoveryCodesList"
                                    :key="index"
                                >
                                    {{ code }}
                                </div>
                            </div>
                        </v-sheet>
                        <p
                            class="text-caption text-medium-emphasis user-select-none"
                        >
                            Each recovery code can be used once to access your
                            account and will be removed after use. If you need
                            more, click
                            <strong>Regenerate codes</strong> above.
                        </p>
                    </template>
                </div>
            </v-expand-transition>
        </v-card-text>
    </v-card>
</template>

<style scoped>
.recovery-codes-grid {
    display: grid;
    gap: 4px;
}

.font-monospace {
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}

.user-select-none {
    user-select: none;
}
</style>
