<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';

const form = useForm({
    password: '',
});
</script>

<template>
    <guest-layout>
        <Head title="Confirm Password"/>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            This is a secure area of the application. Please confirm your
            password before continuing.
        </div>

        <v-form @submit.prevent="form.post(route('password.confirm'))">
            <v-text-field
                v-model="form.password"
                autocomplete="password"
                autofocus
                density="compact"
                label="Email"
                required
                type="password"
                variant="outlined"
            />
            <v-btn
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                class="mt-4"
                type="submit"
            >
                Confirm
            </v-btn>
        </v-form>
    </guest-layout>
</template>
