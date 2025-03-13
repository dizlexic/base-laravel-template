<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Link, useForm} from '@inertiajs/vue3';
import {route} from 'ziggy-js';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

</script>

<template>
    <guest-layout>
        <div class="d-flex flex-column justify-center align-center pa-4">
            <div v-if="$page.props.status" class="mb-4 text-sm font-medium text-green-600">
                {{ $page.props.status }}
            </div>
            <v-card style="max-width: 500px">
                <v-card-title>
                    <h2 class="text-md font-medium">Log in</h2>
                </v-card-title>
                <v-card-text>
                    <v-form style="max-width: 400px;" @submit.prevent="form.post(route('public.login'))">
                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="form.email"
                                    :error-messages="form.errors.email"
                                    autocomplete="username"
                                    autofocus
                                    density="compact"
                                    label="Email"
                                    required
                                    type="email"
                                    variant="outlined"
                                />
                                <v-text-field
                                    v-model="form.password"
                                    :error-messages="form.errors.password"
                                    autocomplete="current-password"
                                    density="compact"
                                    label="Password"
                                    required
                                    type="password"
                                    variant="outlined"
                                />
                                <v-checkbox
                                    v-model="form.remember"
                                    :error-messages="form.errors.remember"
                                    label="Remember me"
                                />
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        :loading="form.processing"
                        type="submit"
                        variant="elevated"
                        @click="form.post(route('public.login'))"
                    >
                        Log in
                    </v-btn>
                    <v-spacer/>
                    <v-btn variant="text">
                        <Link :href="route('public.password.request')">Forgot your password?</Link>
                    </v-btn>
                    <v-btn variant="text">
                        <Link :href="route('public.register')">Register</Link>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </div>
    </guest-layout>
</template>
