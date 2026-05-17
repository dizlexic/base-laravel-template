import { router } from '@inertiajs/vue3';
import { useSnackbar } from '@/composables/useSnackbar';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    const { push } = useSnackbar();

    router.on('flash', (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        push(data.type, data.message);
    });
}
