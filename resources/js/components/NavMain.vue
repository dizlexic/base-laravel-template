<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <!--
        Primary navigation list rendered inside the navigation drawer.
        `<v-list-item>` handles the active styling and rail-mode collapse;
        clicks delegate to Inertia's router for SPA navigation.
    -->
    <v-list nav density="comfortable" class="pa-2">
        <v-list-subheader>Platform</v-list-subheader>
        <v-list-item
            v-for="item in items"
            :key="item.title"
            :prepend-icon="item.icon"
            :title="item.title"
            :active="isCurrentUrl(item.href)"
            :href="toUrl(item.href)"
            rounded="lg"
            @click.prevent="router.visit(toUrl(item.href))"
        />
    </v-list>
</template>
