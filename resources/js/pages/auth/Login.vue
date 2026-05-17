<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Log in" />
    <v-alert
        v-if="status"
        type="success"
        variant="tonal"
        density="comfortable"
        class="mb-4"
    >
        {{ status }}
    </v-alert>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
    >
        <div class="d-flex flex-column ga-4">
            <v-text-field
                id="email"
                type="email"
                name="email"
                label="Email address"
                placeholder="email@example.com"
                autocomplete="email"
                required
                autofocus
                :tabindex="1"
                :error-messages="errors.email"
            />

            <PasswordInput
                id="password"
                name="password"
                label="Password"
                autocomplete="current-password"
                required
                :tabindex="2"
                :error-messages="errors.password"
            />

            <div class="d-flex align-center justify-space-between">
                <v-checkbox
                    id="remember"
                    name="remember"
                    label="Remember me"
                    :tabindex="3"
                />
                <TextLink
                    v-if="canResetPassword"
                    :href="request()"
                    :tabindex="5"
                >
                    Forgot password?
                </TextLink>
            </div>

            <v-btn
                type="submit"
                color="primary"
                block
                size="large"
                class="mt-2"
                :tabindex="4"
                :loading="processing"
                :disabled="processing"
                data-test="login-button"
            >
                Log in
            </v-btn>
        </div>

        <p
            v-if="canRegister"
            class="text-center text-body-2 text-medium-emphasis mt-6"
        >
            Don't have an account?
            <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
        </p>
    </Form>
</template>
