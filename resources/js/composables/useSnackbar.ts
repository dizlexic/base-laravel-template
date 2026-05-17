import { ref } from 'vue';
import type { FlashToast } from '@/types';

export type SnackbarMessage = {
    id: number;
    type: FlashToast['type'];
    message: string;
};

const queue = ref<SnackbarMessage[]>([]);
let counter = 0;

export type UseSnackbarReturn = {
    queue: typeof queue;
    push: (type: FlashToast['type'], message: string) => void;
    dismiss: (id: number) => void;
};

/**
 * Lightweight global snackbar queue consumed by `<AppSnackbar />` so any
 * component (or the Inertia flash-toast handler) can surface a Vuetify
 * `<v-snackbar>` message without coupling to a particular layout.
 */
export function useSnackbar(): UseSnackbarReturn {
    const push = (type: FlashToast['type'], message: string): void => {
        counter += 1;
        queue.value.push({ id: counter, type, message });
    };

    const dismiss = (id: number): void => {
        queue.value = queue.value.filter((item) => item.id !== id);
    };

    return { queue, push, dismiss };
}
