<script setup lang="ts">
import { useSnackbar } from '@/composables/useSnackbar';

const { queue, dismiss } = useSnackbar();

const iconFor = (type: string): string => {
    switch (type) {
        case 'success':
            return 'mdi-check-circle-outline';
        case 'warning':
            return 'mdi-alert-outline';
        case 'error':
            return 'mdi-alert-octagon-outline';
        default:
            return 'mdi-information-outline';
    }
};
</script>

<template>
    <!--
        Renders the global toast queue published via `useSnackbar`. We stack
        each message into its own `<v-snackbar>` so a burst of flashes still
        shows all of them — Vuetify will naturally stack them along the bottom
        edge of the viewport.
    -->
    <template v-for="item in queue" :key="item.id">
        <v-snackbar
            :model-value="true"
            :color="item.type"
            location="bottom right"
            :timeout="5000"
            variant="elevated"
            @update:model-value="(value) => !value && dismiss(item.id)"
        >
            <div class="d-flex align-center ga-2">
                <v-icon :icon="iconFor(item.type)" size="20" />
                <span>{{ item.message }}</span>
            </div>
            <template #actions>
                <v-btn
                    variant="text"
                    icon="mdi-close"
                    size="small"
                    aria-label="Dismiss notification"
                    @click="dismiss(item.id)"
                />
            </template>
        </v-snackbar>
    </template>
</template>
