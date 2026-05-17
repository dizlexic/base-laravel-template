<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import AppSnackbar from '@/components/AppSnackbar.vue';
import { toUrl } from '@/lib/utils';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <!--
        Marketing-style split auth shell: a branded panel on the left
        (desktop only) and the form on the right. The brand panel uses a
        dark fixed colour so it stays consistent across Vuetify themes.
    -->
    <v-app>
        <v-main>
            <v-row no-gutters class="min-h-screen">
                <v-col
                    cols="12"
                    md="6"
                    class="d-none d-md-flex brand-panel pa-10"
                >
                    <Link
                        :href="toUrl(home())"
                        class="d-flex align-center text-decoration-none brand-link"
                    >
                        <v-avatar
                            size="32"
                            rounded="md"
                            color="white"
                            class="me-3"
                        >
                            <AppLogoIcon />
                        </v-avatar>
                        <span class="text-h6 font-weight-medium">
                            {{ name }}
                        </span>
                    </Link>
                </v-col>
                <v-col
                    cols="12"
                    md="6"
                    class="d-flex align-center justify-center pa-8"
                >
                    <div
                        class="d-flex flex-column ga-6 w-100"
                        style="max-width: 360px"
                    >
                        <div class="text-center d-flex flex-column ga-2">
                            <h1 v-if="title" class="text-h6 font-weight-medium">
                                {{ title }}
                            </h1>
                            <p
                                v-if="description"
                                class="text-body-2 text-medium-emphasis"
                            >
                                {{ description }}
                            </p>
                        </div>
                        <slot />
                    </div>
                </v-col>
            </v-row>
        </v-main>
        <AppSnackbar />
    </v-app>
</template>

<style scoped>
.brand-panel {
    background-color: #18181b;
    color: #ffffff;
    align-items: flex-start;
}

.brand-link {
    color: #ffffff;
}
</style>
