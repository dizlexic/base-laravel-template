<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="d-flex flex-column ga-4">
            <v-text-field
                id="name"
                name="name"
                label="Name"
                placeholder="Full name"
                autocomplete="name"
                required
                autofocus
                :tabindex="1"
                :error-messages="errors.name"
            />

            <v-text-field
                id="email"
                type="email"
                name="email"
                label="Email address"
                placeholder="email@example.com"
                autocomplete="email"
                required
                :tabindex="2"
                :error-messages="errors.email"
            />

            <PasswordInput
                id="password"
                name="password"
                label="Password"
                autocomplete="new-password"
                required
                :tabindex="3"
                :error-messages="errors.password"
            />

            <PasswordInput
                id="password_confirmation"
                name="password_confirmation"
                label="Confirm password"
                autocomplete="new-password"
                required
                :tabindex="4"
                :error-messages="errors.password_confirmation"
            />

            <v-btn
                type="submit"
                color="primary"
                block
                size="large"
                class="mt-2"
                tabindex="5"
                :loading="processing"
                :disabled="processing"
                data-test="register-user-button"
            >
                Create account
            </v-btn>
        </div>

        <p class="text-center text-body-2 text-medium-emphasis mt-6">
            Already have an account?
            <TextLink :href="login()" :tabindex="6">Log in</TextLink>
        </p>
    </Form>
</template>
