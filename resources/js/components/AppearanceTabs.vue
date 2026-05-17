<script setup lang="ts">
import type { Appearance } from '@/composables/useAppearance';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const tabs: Array<{ value: Appearance; icon: string; label: string }> = [
    { value: 'light', icon: 'mdi-weather-sunny', label: 'Light' },
    { value: 'dark', icon: 'mdi-weather-night', label: 'Dark' },
    { value: 'system', icon: 'mdi-monitor', label: 'System' },
];
</script>

<template>
    <!--
        Vuetify button toggle ties the appearance selection to the theme via
        the `useAppearance` composable; the composable in turn updates
        Vuetify's runtime theme (`vuetify.theme.global.name`) so every
        component re-paints immediately.
    -->
    <v-btn-toggle
        :model-value="appearance"
        density="comfortable"
        variant="outlined"
        divided
        mandatory
        @update:model-value="
            (value) => value && updateAppearance(value as Appearance)
        "
    >
        <v-btn
            v-for="tab in tabs"
            :key="tab.value"
            :value="tab.value"
            :prepend-icon="tab.icon"
        >
            {{ tab.label }}
        </v-btn>
    </v-btn-toggle>
</template>
