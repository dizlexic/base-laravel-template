<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: editProfile(),
        icon: 'mdi-account-circle-outline',
    },
    {
        title: 'Security',
        href: editSecurity(),
        icon: 'mdi-shield-lock-outline',
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: 'mdi-palette-outline',
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <!--
        Nested layout used inside `AppLayout`. Renders a settings sub-nav
        (`<v-list>`) alongside the active settings page.
    -->
    <div class="pa-4">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />
        <v-row>
            <v-col cols="12" lg="3">
                <v-list nav density="comfortable" aria-label="Settings">
                    <v-list-item
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        :prepend-icon="item.icon"
                        :title="item.title"
                        :href="toUrl(item.href)"
                        :active="isCurrentOrParentUrl(item.href)"
                        rounded="lg"
                        @click.prevent="router.visit(toUrl(item.href))"
                    />
                </v-list>
                <v-divider class="d-lg-none my-4" />
            </v-col>
            <v-col cols="12" lg="9">
                <section style="max-width: 640px">
                    <div class="d-flex flex-column ga-10">
                        <slot />
                    </div>
                </section>
            </v-col>
        </v-row>
    </div>
</template>
