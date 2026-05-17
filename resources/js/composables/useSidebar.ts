import { inject, ref } from 'vue';
import type { InjectionKey, Ref } from 'vue';

/**
 * Shared injection key for the navigation drawer's open state. `AppShell`
 * provides it; `AppSidebar`, `AppHeader`, and `AppSidebarHeader` consume it
 * through `useSidebar()` so they can toggle the drawer from anywhere in the
 * layout tree without prop drilling.
 */
export const sidebarOpenKey: InjectionKey<Ref<boolean>> =
    Symbol('app-sidebar-open');

export function useSidebar(): {
    open: Ref<boolean>;
    toggle: () => void;
} {
    // Fallback to a local ref so the composable is safe to call from contexts
    // that don't sit inside an `<AppShell>` (e.g. isolated component tests).
    const open = inject(sidebarOpenKey, ref(true));

    const toggle = (): void => {
        open.value = !open.value;
    };

    return { open, toggle };
}
