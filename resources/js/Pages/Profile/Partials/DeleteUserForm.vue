<script setup>
import {useForm} from '@inertiajs/vue3';
import {nextTick, ref} from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('user.profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <v-btn color="red" @click="confirmUserDeletion">Delete Account</v-btn>
        <v-dialog
            v-model="confirmingUserDeletion"
            max-width="290"
        >
            <v-card>
                <v-card-title>
                    <h2 class="text-lg font-medium">Are you sure you want to delete your account?</h2>
                </v-card-title>
                <v-card-text>
                    <p>
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please
                        enter your password to confirm you would like to permanently delete your account.
                    </p>
                    <v-form
                        @submit.prevent="deleteUser"
                    >
                        <v-text-field
                            v-model="form.password"
                            :error-messages="form.errors.password"
                            label="Password"
                            required
                            type="password"
                        />
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="closeModal">Cancel</v-btn>
                    <v-btn @click="deleteUser">Delete Account</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </section>
</template>
