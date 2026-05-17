<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { useSidebar } from '@/composables/useSidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { toggle } = useSidebar();
</script>

<template>
    <!--
        Sticky `<v-app-bar>` aligned with the navigation drawer. Hosts the
        drawer toggle (MDI menu icon) and the optional breadcrumbs trail.
    -->
    <v-app-bar :elevation="0" density="comfortable" color="surface" border="b">
        <v-app-bar-nav-icon
            icon="mdi-menu"
            aria-label="Toggle sidebar"
            @click="toggle"
        />
        <Breadcrumbs
            v-if="breadcrumbs && breadcrumbs.length > 0"
            :breadcrumbs="breadcrumbs"
        />
    </v-app-bar>
</template>
