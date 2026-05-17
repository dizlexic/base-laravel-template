<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import AppSnackbar from '@/components/AppSnackbar.vue';
import { toUrl } from '@/lib/utils';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <!--
        Centred authentication shell used by the login / register / forgot
        password screens. Wraps the form in a `<v-app>` so any Vuetify
        components (snackbars, dialogs) work without an outer shell.
    -->
    <v-app>
        <v-main class="bg-background">
            <v-container
                class="d-flex flex-column align-center justify-center min-h-screen ga-6"
            >
                <v-sheet width="100%" max-width="400" color="transparent">
                    <div class="d-flex flex-column align-center ga-4 mb-6">
                        <Link
                            :href="toUrl(home())"
                            class="text-decoration-none text-inherit"
                        >
                            <v-avatar size="48" color="primary" rounded="md">
                                <AppLogoIcon />
                            </v-avatar>
                            <span class="sr-only">{{ title }}</span>
                        </Link>
                        <div class="text-center">
                            <h1 class="text-h6 font-weight-medium mb-1">
                                {{ title }}
                            </h1>
                            <p class="text-body-2 text-medium-emphasis">
                                {{ description }}
                            </p>
                        </div>
                    </div>
                    <slot />
                </v-sheet>
            </v-container>
        </v-main>
        <AppSnackbar />
    </v-app>
</template>
