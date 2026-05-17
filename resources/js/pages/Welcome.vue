<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppSnackbar from '@/components/AppSnackbar.vue';
import { toUrl } from '@/lib/utils';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const startingSteps = [
    {
        title: 'Read the Documentation',
        href: 'https://laravel.com/docs',
        icon: 'mdi-book-open-page-variant-outline',
    },
    {
        title: 'Watch video tutorials at Laracasts',
        href: 'https://laracasts.com',
        icon: 'mdi-play-circle-outline',
    },
];
</script>

<template>
    <Head title="Welcome" />
    <!--
        Marketing landing page. Built entirely with Vuetify primitives so
        the look and feel stay aligned with the rest of the application.
    -->
    <v-app>
        <v-app-bar
            :elevation="0"
            color="surface"
            border="b"
            density="comfortable"
        >
            <v-container
                fluid
                max-width="1024"
                class="d-flex align-center py-0"
            >
                <v-spacer />
                <template v-if="authUser">
                    <v-btn
                        :href="toUrl(dashboard())"
                        variant="outlined"
                        prepend-icon="mdi-view-dashboard-outline"
                    >
                        Dashboard
                    </v-btn>
                </template>
                <template v-else>
                    <v-btn :href="toUrl(login())" variant="text">Log in</v-btn>
                    <v-btn
                        v-if="canRegister"
                        :href="toUrl(register())"
                        variant="outlined"
                        class="ms-2"
                    >
                        Register
                    </v-btn>
                </template>
            </v-container>
        </v-app-bar>

        <v-main>
            <v-container max-width="1024" class="py-12">
                <v-row align="stretch">
                    <v-col cols="12" md="6">
                        <v-card border flat rounded="xl" class="pa-8 h-100">
                            <v-card-title
                                class="text-h5 font-weight-medium px-0"
                            >
                                Let's get started
                            </v-card-title>
                            <v-card-subtitle
                                class="px-0 text-medium-emphasis text-wrap"
                            >
                                Laravel has an incredibly rich ecosystem. We
                                suggest starting with the following.
                            </v-card-subtitle>
                            <v-list class="mt-4" lines="two">
                                <v-list-item
                                    v-for="step in startingSteps"
                                    :key="step.href"
                                    :href="step.href"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :prepend-icon="step.icon"
                                    :title="step.title"
                                    append-icon="mdi-arrow-top-right"
                                />
                            </v-list>
                            <v-card-actions class="px-0 mt-4">
                                <v-btn
                                    href="https://cloud.laravel.com"
                                    target="_blank"
                                    color="primary"
                                    prepend-icon="mdi-rocket-launch-outline"
                                >
                                    Deploy now
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-card
                            flat
                            color="error"
                            rounded="xl"
                            class="pa-10 h-100 d-flex align-center justify-center welcome-brand"
                        >
                            <v-icon
                                icon="mdi-laravel"
                                size="160"
                                color="white"
                            />
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <AppSnackbar />
    </v-app>
</template>

<style scoped>
.welcome-brand {
    min-height: 320px;
}
</style>
