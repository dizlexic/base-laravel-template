import type { InertiaLinkProps } from '@inertiajs/vue3';

export function toUrl(href: NonNullable<InertiaLinkProps['href']>): string {
    return typeof href === 'string' ? href : href?.url;
}
