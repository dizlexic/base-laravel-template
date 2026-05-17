<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import { toUrl } from '@/lib/utils';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <section class="d-flex flex-column ga-6">
        <Heading
            variant="small"
            title="Profile information"
            description="Update your name and email address"
        />

        <Form
            v-bind="ProfileController.update.form()"
            v-slot="{ errors, processing }"
        >
            <div class="d-flex flex-column ga-4">
                <v-text-field
                    id="name"
                    name="name"
                    label="Name"
                    placeholder="Full name"
                    :default-value="user.name"
                    autocomplete="name"
                    required
                    :error-messages="errors.name"
                />

                <v-text-field
                    id="email"
                    type="email"
                    name="email"
                    label="Email address"
                    placeholder="Email address"
                    :default-value="user.email"
                    autocomplete="username"
                    required
                    :error-messages="errors.email"
                />

                <v-alert
                    v-if="mustVerifyEmail && !user.email_verified_at"
                    type="info"
                    variant="tonal"
                    density="comfortable"
                    icon="mdi-information-outline"
                >
                    Your email address is unverified.
                    <Link
                        :href="toUrl(send())"
                        as="button"
                        class="font-weight-medium text-decoration-underline"
                    >
                        Click here to resend the verification email.
                    </Link>
                    <div
                        v-if="status === 'verification-link-sent'"
                        class="mt-2 text-success font-weight-medium"
                    >
                        A new verification link has been sent to your email
                        address.
                    </div>
                </v-alert>

                <div>
                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="processing"
                        :disabled="processing"
                        data-test="update-profile-button"
                    >
                        Save
                    </v-btn>
                </div>
            </div>
        </Form>
    </section>

    <DeleteUser />
</template>
