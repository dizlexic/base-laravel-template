<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const form = useForm({
    username: '',
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('public.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register"/>

        <div class="d-flex justify-center align-content-center pa-4">
            <v-card>
                <v-card-title>
                    <h2 class="text-md font-medium">Register</h2>
                </v-card-title>
                <v-card-text>
                    <v-form style="max-width: 400px;">
                        <v-text-field
                            v-model="form.username"
                            autocomplete="name"
                            autofocus
                            density="compact"
                            label="Username"
                            required
                            type="text"
                            variant="outlined"
                        />
                        <v-text-field
                            v-model="form.first_name"
                            :error-messages="form.errors.first_name"
                            autocomplete="name"
                            autofocus
                            density="compact"
                            label="Firstname"
                            required
                            type="text"
                            variant="outlined"
                        />
                        <v-text-field
                            v-model="form.last_name"
                            :error-messages="form.errors.last_name"
                            autocomplete="name"
                            autofocus
                            density="compact"
                            label="Lastname"
                            required
                            type="text"
                            variant="outlined"
                        />
                        <v-text-field
                            v-model="form.email"
                            :error-messages="form.errors.email"
                            autocomplete="username"
                            density="compact"
                            label="Email"
                            required
                            type="email"
                            variant="outlined"
                        />
                        <v-text-field
                            v-model="form.password"
                            :error-messages="form.errors.password"
                            autocomplete="new-password"
                            density="compact"
                            label="Password"
                            required
                            type="password"
                            variant="outlined"
                        />
                        <v-text-field
                            v-model="form.password_confirmation"
                            :error-messages="form.errors.password_confirmation"
                            autocomplete="new-password"
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
                        Register
                    </v-btn>
                    <v-spacer/>
                    <v-btn variant="text">
                        <Link :href="route('public.login')">Already registered?</Link>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </div>

    </GuestLayout>
</template>
