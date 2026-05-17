<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import PasswordInput from '@/components/PasswordInput.vue';

const dialogOpen = ref(false);
const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <section class="d-flex flex-column ga-6">
        <Heading
            variant="small"
            title="Delete account"
            description="Delete your account and all of its resources"
        />
        <v-alert
            type="error"
            variant="tonal"
            density="comfortable"
            icon="mdi-alert-outline"
            title="Warning"
            text="Please proceed with caution, this cannot be undone."
        />
        <div>
            <v-btn
                color="error"
                data-test="delete-user-button"
                @click="dialogOpen = true"
            >
                Delete account
            </v-btn>
        </div>

        <v-dialog v-model="dialogOpen" max-width="520" persistent>
            <Form
                v-bind="ProfileController.destroy.form()"
                reset-on-success
                v-slot="{ errors, processing, reset, clearErrors }"
                :options="{ preserveScroll: true }"
                @error="() => passwordInput?.focus()"
                @success="() => (dialogOpen = false)"
            >
                <v-card>
                    <v-card-title>
                        Are you sure you want to delete your account?
                    </v-card-title>
                    <v-card-text>
                        <p class="text-body-2 text-medium-emphasis mb-4">
                            Once your account is deleted, all of its resources
                            and data will also be permanently deleted. Please
                            enter your password to confirm you would like to
                            permanently delete your account.
                        </p>
                        <PasswordInput
                            id="password"
                            ref="passwordInput"
                            name="password"
                            label="Password"
                            :error-messages="errors.password"
                        />
                    </v-card-text>
                    <v-card-actions class="px-6 pb-4">
                        <v-spacer />
                        <v-btn
                            variant="text"
                            @click="
                                () => {
                                    clearErrors();
                                    reset();
                                    dialogOpen = false;
                                }
                            "
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            type="submit"
                            color="error"
                            :loading="processing"
                            :disabled="processing"
                            data-test="confirm-delete-user-button"
                        >
                            Delete account
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </Form>
        </v-dialog>
    </section>
</template>
