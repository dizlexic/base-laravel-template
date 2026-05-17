<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Reset password',
        description: 'Please enter your new password below',
    },
});

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Reset password" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="d-flex flex-column ga-4">
            <v-text-field
                id="email"
                v-model="inputEmail"
                type="email"
                name="email"
                label="Email"
                autocomplete="email"
                readonly
                :error-messages="errors.email"
            />

            <PasswordInput
                id="password"
                name="password"
                label="Password"
                autocomplete="new-password"
                autofocus
                :error-messages="errors.password"
            />

            <PasswordInput
                id="password_confirmation"
                name="password_confirmation"
                label="Confirm password"
                autocomplete="new-password"
                :error-messages="errors.password_confirmation"
            />

            <v-btn
                type="submit"
                color="primary"
                block
                size="large"
                class="mt-2"
                :loading="processing"
                :disabled="processing"
                data-test="reset-password-button"
            >
                Reset password
            </v-btn>
        </div>
    </Form>
</template>
