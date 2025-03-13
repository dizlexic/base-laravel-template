<script setup>
import {useForm} from '@inertiajs/vue3';
import {ref} from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user.password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Update Password
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form class="mt-6 space-y-6" @submit.prevent="updatePassword">
            <div>
                <v-text-field
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    :error-messages="form.errors.current_password"
                    autocomplete="current-password"
                    label="Current Password"
                    required
                    type="password"
                />
            </div>

            <div>
                <v-text-field
                    ref="passwordInput"
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    autocomplete="new-password"
                    label="New Password"
                    required
                    type="password"
                />
            </div>

            <div>
                <v-text-field
                    v-model="form.password_confirmation"
                    :error-messages="form.errors.password_confirmation"
                    autocomplete="new-password"
                    label="Confirm Password"
                    required
                    type="password"
                />
            </div>

            <div class="d-flex justify-center ga-4">

                <v-btn
                    :disabled="form.processing"
                    color="primary"
                    type="submit"
                >
                    Save
                </v-btn>

                <v-expand-transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </v-expand-transition>
            </div>
        </form>
    </section>
</template>
