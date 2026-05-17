<script setup lang="ts">
import type { LinkComponentBaseProps, Method } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';

type Props = {
    href: LinkComponentBaseProps['href'];
    tabindex?: number;
    method?: Method;
    as?: string;
};

defineProps<Props>();
</script>

<template>
    <!--
        Renders an inline Inertia link with Vuetify-aware styling: it inherits
        the surface foreground colour and uses a subtle underline that follows
        the active theme.
    -->
    <Link
        :href="href"
        :tabindex="tabindex"
        :method="method"
        :as="as"
        class="app-text-link"
    >
        <slot />
    </Link>
</template>

<style scoped>
.app-text-link {
    color: rgb(var(--v-theme-on-surface));
    text-decoration: underline;
    text-decoration-color: rgba(var(--v-theme-on-surface), 0.35);
    text-underline-offset: 4px;
    transition: text-decoration-color 200ms ease-out;
}

.app-text-link:hover {
    text-decoration-color: rgba(var(--v-theme-on-surface), 1);
}
</style>
