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
        Card-style auth shell: a centred `<v-card>` on top of the secondary
        Vuetify surface for emphasis. Used by flows that want a stronger
        visual frame around the form (e.g. confirm-password).
    -->
    <v-app>
        <v-main class="bg-secondary">
            <v-container
                class="d-flex flex-column align-center justify-center min-h-screen ga-6"
            >
                <div
                    class="d-flex flex-column align-center ga-6 w-100"
                    style="max-width: 480px"
                >
                    <Link
                        :href="toUrl(home())"
                        class="text-decoration-none text-inherit"
                    >
                        <v-avatar size="48" color="primary" rounded="md">
                            <AppLogoIcon />
                        </v-avatar>
                    </Link>

                    <v-card class="w-100" rounded="xl">
                        <v-card-item class="text-center pt-8 pb-0 px-8">
                            <v-card-title class="text-h6">
                                {{ title }}
                            </v-card-title>
                            <v-card-subtitle>
                                {{ description }}
                            </v-card-subtitle>
                        </v-card-item>
                        <v-card-text class="px-8 py-8">
                            <slot />
                        </v-card-text>
                    </v-card>
                </div>
            </v-container>
        </v-main>
        <AppSnackbar />
    </v-app>
</template>
