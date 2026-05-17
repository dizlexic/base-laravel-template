import type { InertiaLinkProps } from '@inertiajs/vue3';

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

/**
 * `icon` is a Material Design Icons (MDI) identifier, e.g. `'mdi-view-dashboard'`,
 * passed as-is to Vuetify's `<v-icon :icon="..." />` or component `prepend-icon`
 * props. See https://pictogrammers.com/library/mdi/ for the catalogue.
 */
export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: string;
    isActive?: boolean;
};
