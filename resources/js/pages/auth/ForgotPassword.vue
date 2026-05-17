<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Forgot password',
        description: 'Enter your email to receive a password reset link',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Forgot password" />

    <v-alert
        v-if="status"
        type="success"
        variant="tonal"
        density="comfortable"
        class="mb-4"
    >
        {{ status }}
    </v-alert>

    <Form v-bind="email.form()" v-slot="{ errors, processing }">
        <div class="d-flex flex-column ga-4">
            <v-text-field
                id="email"
                type="email"
                name="email"
                label="Email address"
                placeholder="email@example.com"
                autocomplete="off"
                autofocus
                :error-messages="errors.email"
            />

            <v-btn
                type="submit"
                color="primary"
                block
                size="large"
                :loading="processing"
                :disabled="processing"
                data-test="email-password-reset-link-button"
            >
                Email password reset link
            </v-btn>
        </div>
    </Form>

    <p class="text-center text-body-2 text-medium-emphasis mt-6">
        Or, return to
        <TextLink :href="login()">log in</TextLink>
    </p>
</template>
