<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {route} from 'ziggy-js';

const form = useForm({
    email: '',
});
</script>

<template>
    <guest-layout>
        <Head title="Forgot Password"/>
        <v-container class="d-flex justify-center" style="max-width: 500px;">
            <v-card class="pa-4">
                <v-card-title class="bg-primary text-white">
                    <h2 class="text-md font-medium">Reset Password</h2>
                </v-card-title>
                <v-card-item>
                    <p class="pb-4">
                        Forgot your password? No problem. Just let us know your email
                        address and we will email you a password reset link that will allow
                        you to choose a new one.
                    </p>
                    <v-form @submit.prevent="form.post(route('password.email'))">
                        <v-text-field
                            id="email"
                            v-model="form.email"
                            :error-messages="form.errors.email"
                            autofocus
                            density="compact"
                            required
                            type="email"
                            variant="outlined"
                        />
                    </v-form>
                </v-card-item>
                <v-card-actions>
                    <v-btn
                        :loading="form.processing"
                        variant="elevated"
                        @click="form.post(route('password.email'));"
                    >
                        Email Password Reset Link
                    </v-btn>
                    <v-btn
                        :href="route('login')"
                        color="primary"
                        variant="text"
                    >
                        Back to login
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </guest-layout>
</template>
