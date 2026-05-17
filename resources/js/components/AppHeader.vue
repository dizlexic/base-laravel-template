<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { getInitials } from '@/composables/useInitials';
import { toUrl } from '@/lib/utils';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const { isCurrentUrl } = useCurrentUrl();

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: 'mdi-view-dashboard-outline',
    },
];

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: 'mdi-folder-outline',
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
        Top app bar for the "header" layout variant. Uses Vuetify's
        `<v-app-bar>` with a flex toolbar inside `<v-container>` to keep the
        content aligned with the page's main column. The mobile drawer
        (`<v-navigation-drawer>` in temporary mode) mirrors the desktop links.
    -->
    <v-app-bar :elevation="0" color="surface" border="b" density="comfortable">
        <v-container
            fluid
            max-width="1280"
            class="d-flex align-center ga-3 py-0"
        >
            <v-app-bar-nav-icon
                class="d-lg-none"
                icon="mdi-menu"
                aria-label="Open navigation menu"
                @click="mobileMenuOpen = true"
            />

            <Link
                :href="dashboard().url"
                class="d-flex align-center text-decoration-none text-inherit"
            >
                <AppLogo />
            </Link>

            <div class="d-none d-lg-flex align-center ms-6 ga-1">
                <v-btn
                    v-for="item in mainNavItems"
                    :key="item.title"
                    :href="toUrl(item.href)"
                    :prepend-icon="item.icon"
                    :active="isCurrentUrl(item.href)"
                    variant="text"
                    :ripple="false"
                    @click.prevent="router.visit(toUrl(item.href))"
                >
                    {{ item.title }}
                </v-btn>
            </div>

            <v-spacer />

            <v-btn
                icon="mdi-magnify"
                variant="text"
                density="comfortable"
                aria-label="Search"
            />

            <template v-for="item in rightNavItems" :key="item.title">
                <v-tooltip :text="item.title" location="bottom">
                    <template #activator="{ props: activator }">
                        <v-btn
                            v-bind="activator"
                            :icon="item.icon"
                            :href="toUrl(item.href)"
                            target="_blank"
                            rel="noopener noreferrer"
                            variant="text"
                            density="comfortable"
                            class="d-none d-lg-inline-flex"
                            :aria-label="item.title"
                        />
                    </template>
                </v-tooltip>
            </template>

            <v-menu
                v-model="userMenuOpen"
                location="bottom end"
                :close-on-content-click="false"
                offset="8"
            >
                <template #activator="{ props: activator }">
                    <v-btn
                        v-bind="activator"
                        icon
                        variant="text"
                        density="comfortable"
                        :aria-label="`Open user menu for ${auth.user.name}`"
                    >
                        <v-avatar size="32" color="secondary">
                            <v-img
                                v-if="auth.user.avatar"
                                :src="auth.user.avatar"
                                :alt="auth.user.name"
                            />
                            <span v-else class="text-body-2 font-weight-bold">
                                {{ getInitials(auth.user?.name) }}
                            </span>
                        </v-avatar>
                    </v-btn>
                </template>
                <v-card min-width="240">
                    <UserMenuContent
                        :user="auth.user"
                        @navigate="userMenuOpen = false"
                    />
                </v-card>
            </v-menu>
        </v-container>
    </v-app-bar>

    <!-- Optional second-row breadcrumbs bar, identical to the original shell. -->
    <v-system-bar
        v-if="props.breadcrumbs.length > 1"
        color="surface"
        height="48"
        class="border-b"
    >
        <v-container fluid max-width="1280" class="py-0">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </v-container>
    </v-system-bar>

    <!-- Mobile navigation drawer (temporary mode). -->
    <v-navigation-drawer
        v-model="mobileMenuOpen"
        temporary
        location="left"
        width="300"
    >
        <v-list nav density="comfortable">
            <v-list-item
                v-for="item in mainNavItems"
                :key="item.title"
                :href="toUrl(item.href)"
                :prepend-icon="item.icon"
                :title="item.title"
                :active="isCurrentUrl(item.href)"
                @click.prevent="
                    () => {
                        mobileMenuOpen = false;
                        router.visit(toUrl(item.href));
                    }
                "
            />
            <v-divider class="my-2" />
            <v-list-item
                v-for="item in rightNavItems"
                :key="item.title"
                :href="toUrl(item.href)"
                :prepend-icon="item.icon"
                :title="item.title"
                target="_blank"
                rel="noopener noreferrer"
            />
        </v-list>
    </v-navigation-drawer>
</template>
