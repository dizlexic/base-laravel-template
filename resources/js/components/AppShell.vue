<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { provide, ref } from 'vue';
import AppSnackbar from '@/components/AppSnackbar.vue';
import { sidebarOpenKey } from '@/composables/useSidebar';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
};

withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});

// Server-provided default visibility for the navigation drawer. Wrapped in a
// `ref` so the AppSidebar / header toggle button can mutate it locally.
const defaultOpen =
    (usePage().props.sidebarOpen as boolean | undefined) ?? true;
const sidebarOpen = ref<boolean>(defaultOpen);

provide(sidebarOpenKey, sidebarOpen);
</script>

<template>
    <!--
        `<v-app>` is Vuetify's required root component: it sets up the layout
        coordinate system used by `<v-app-bar>`, `<v-navigation-drawer>`, and
        `<v-main>` so they can size and offset each other automatically.
    -->
    <v-app>
        <slot />
        <AppSnackbar />
    </v-app>
</template>
