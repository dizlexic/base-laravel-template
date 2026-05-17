<script setup lang="ts">
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types';

type Props = {
    items: NavItem[];
    class?: string;
};

defineProps<Props>();
</script>

<template>
    <!--
        Secondary list rendered in the navigation drawer's append slot.
        Items always open in a new tab (external repositories / docs).
    -->
    <v-list nav density="comfortable" :class="['pa-2', $props.class]">
        <v-list-item
            v-for="item in items"
            :key="item.title"
            :prepend-icon="item.icon"
            :title="item.title"
            :href="toUrl(item.href)"
            target="_blank"
            rel="noopener noreferrer"
            rounded="lg"
        />
    </v-list>
</template>
