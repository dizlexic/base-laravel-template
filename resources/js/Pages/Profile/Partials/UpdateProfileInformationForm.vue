<script setup>
import {Link, useForm, usePage} from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <v-form
            class="mt-6 space-y-6"
            @submit.prevent="form.patch(route('profile.update'))"
        >
            <v-text-field
                v-model="form.name"
                :error-messages="form.errors.name"
                autocomplete="name"
                autofocus
                label="Name"
                required
                type="text"
                variant="outlined"
            />

            <v-text-field
                v-model="form.email"
                :error-messages="form.errors.email"
                autocomplete="username"
                label="Email"
                required
                type="email"
                variant="outlined"
            />

            <Link
                v-if="$page.props.mustVerifyEmail && $page.props.auth.user.email_verified_at === null"
                :href="route('user.verification.send')"
                class="text-sm text-gray-600 dark:text-gray-400"
                method="post"
            >
                Click here to re-send the verification email.
            </Link>

            <div
                v-show="$page.props.status === 'verification-link-sent'"
                class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
            >
                A new verification link has been sent to your email address.
            </div>
            <v-expand-transition
                enter-active-class="transition ease-in-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition
                ease-in-out duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="form.recentlySuccessful"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    Saved.
                </div>
            </v-expand-transition>
        </v-form>
    </section>
</template>
