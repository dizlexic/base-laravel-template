<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verify email',
        description:
            'Please verify your email address by clicking on the link we just emailed to you.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Email verification" />

    <v-alert
        v-if="status === 'verification-link-sent'"
        type="success"
        variant="tonal"
        density="comfortable"
        class="mb-4"
    >
        A new verification link has been sent to the email address you provided
        during registration.
    </v-alert>

    <Form v-bind="send.form()" v-slot="{ processing }">
        <div class="d-flex flex-column align-center ga-4">
            <v-btn
                type="submit"
                color="primary"
                size="large"
                :loading="processing"
                :disabled="processing"
            >
                Resend verification email
            </v-btn>
        </div>
    </Form>

    <Form v-bind="logout.form()" class="d-flex justify-center mt-6">
        <v-btn type="submit" variant="text" density="comfortable">
            Log out
        </v-btn>
    </Form>
</template>
