<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {onMounted} from "vue";

const form = useForm({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
});

onMounted(() => {
    form.token = usePage().props.token;
    form.email = usePage().props.email;
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password"/>

        <v-card
            class="mx-auto"
            style="max-width: 400px;"
        >
            <v-card-title>
                <h2 class="text-md font-medium">Reset Password</h2>
            </v-card-title>

            <v-card-text>
                <p>
                    Please enter your new password.
                </p>
            </v-card-text>

            <v-card-text>
                <v-form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.email"
                        :error-messages="form.errors.email"
                        autofocus
                        density="compact"
                        label="Email"
                        readonly
                        required
                        type="email"
                        variant="outlined"
                    />

                    <v-text-field
                        v-model="form.password"
                        :error-messages="form.errors.password"
                        density="compact"
                        label="Password"
                        required
                        type="password"
                        variant="outlined"
                    />

                    <v-text-field
                        v-model="form.password_confirmation"
                        :error-messages="form.errors.password_confirmation"
                        density="compact"
                        label="Confirm Password"
                        required
                        type="password"
                        variant="outlined"
                    />
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-btn
                    :loading="form.processing"
                    type="submit"
                    variant="elevated"
                    @click="submit"
                >
                    Reset Password
                </v-btn>
            </v-card-actions>
        </v-card>
    </GuestLayout>
</template>
