<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toUrl } from '@/lib/utils';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

type Props = {
    breadcrumbs: BreadcrumbItemType[];
};

const props = defineProps<Props>();

// `<v-breadcrumbs>` expects items where `href` is a plain string. Our
// internal `BreadcrumbItem` allows Inertia's `UrlMethodPair` too, so we map
// here to the Vuetify-compatible shape.
const vuetifyItems = computed(() =>
    props.breadcrumbs.map((item) => ({
        title: item.title,
        href: toUrl(item.href),
    })),
);
</script>

<template>
    <!--
        Vuetify's `<v-breadcrumbs>` handles separators and accessibility
        wiring; we render each item ourselves so the non-final entries can
        navigate via Inertia's `<Link>` (preserving client-side transitions).
    -->
    <v-breadcrumbs :items="vuetifyItems" density="compact" class="pa-0">
        <template #divider>
            <v-icon icon="mdi-chevron-right" size="16" />
        </template>
        <template #item="{ item, index }">
            <v-breadcrumbs-item
                :title="item.title"
                :active="index === vuetifyItems.length - 1"
                :disabled="index === vuetifyItems.length - 1"
            >
                <Link
                    v-if="index !== vuetifyItems.length - 1"
                    :href="item.href"
                    class="text-medium-emphasis"
                >
                    {{ item.title }}
                </Link>
                <span v-else>{{ item.title }}</span>
            </v-breadcrumbs-item>
        </template>
    </v-breadcrumbs>
</template>
