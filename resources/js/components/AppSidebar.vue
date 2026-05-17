<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { useSidebar } from '@/composables/useSidebar';
import { toUrl } from '@/lib/utils';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const { open } = useSidebar();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: 'mdi-view-dashboard-outline',
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: 'mdi-source-repository',
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: 'mdi-book-open-page-variant-outline',
    },
];
</script>

<template>
    <!--
        Persistent navigation drawer for the sidebar layout. Vuetify keeps
        the drawer fixed on desktop and falls back to an overlay on mobile
        automatically via the `mobile-breakpoint` prop.
    -->
    <v-navigation-drawer
        v-model="open"
        :width="260"
        rail-width="72"
        :rail="!open"
        location="left"
        color="surface"
        border="r"
    >
        <div class="pa-3">
            <Link
                :href="toUrl(dashboard())"
                class="text-decoration-none text-inherit"
            >
                <AppLogo />
            </Link>
        </div>
        <v-divider />
        <NavMain :items="mainNavItems" />

        <template #append>
            <v-divider />
            <NavFooter :items="footerNavItems" />
            <v-divider />
            <NavUser />
        </template>
    </v-navigation-drawer>
    <slot />
</template>
