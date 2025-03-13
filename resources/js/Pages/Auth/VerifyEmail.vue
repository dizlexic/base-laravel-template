<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, router, useForm} from '@inertiajs/vue3';
import {route} from "ziggy-js";

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

</script>

<template>
    <GuestLayout>
        <Head title="Email Verification"/>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            v-if="$page.props.status === 'verification-link-sent'"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <v-form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <v-btn
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    type="submit"
                >
                    Resend Verification Email
                </v-btn>
                <v-btn
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    method="post"
                    @click="router.post(route('logout'))"
                >Log Out
                </v-btn
                >
            </div>
        </v-form>
    </GuestLayout>
</template>
