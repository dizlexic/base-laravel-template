<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <!--
        Authenticated app shell with persistent navigation drawer. The
        global `<AppSnackbar />` is rendered by `<AppShell>` so flash
        toasts surface from every page in this layout automatically.
    -->
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppSidebarHeader :breadcrumbs="breadcrumbs" />
        <AppContent variant="sidebar">
            <slot />
        </AppContent>
    </AppShell>
</template>
