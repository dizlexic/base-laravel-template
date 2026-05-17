<script setup lang="ts">
import { computed } from 'vue';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});

const className = computed(() => props.class);
</script>

<template>
    <!--
        Vuetify's `<v-main>` already takes care of offsetting the content
        for any `<v-app-bar>` or `<v-navigation-drawer>` in the layout, so
        we don't need separate sidebar / header branches like shadcn did.
    -->
    <v-main :class="className">
        <v-container
            fluid
            class="h-100 d-flex flex-column ga-4"
            :class="props.variant === 'header' ? 'mx-auto' : ''"
            :max-width="props.variant === 'header' ? 1280 : undefined"
        >
            <slot />
        </v-container>
    </v-main>
</template>
