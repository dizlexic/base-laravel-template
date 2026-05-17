<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />
    <!--
        Dashboard placeholder. Each card uses `<v-card>` with the diagonal
        stripe placeholder layered behind for a "loading" feel.
    -->
    <div class="d-flex flex-column ga-4">
        <v-row>
            <v-col v-for="n in 3" :key="n" cols="12" md="4">
                <v-card
                    rounded="xl"
                    border
                    flat
                    class="dashboard-card position-relative overflow-hidden"
                >
                    <PlaceholderPattern />
                </v-card>
            </v-col>
        </v-row>
        <v-card
            rounded="xl"
            border
            flat
            class="dashboard-card-tall position-relative overflow-hidden flex-grow-1"
        >
            <PlaceholderPattern />
        </v-card>
    </div>
</template>

<style scoped>
.dashboard-card {
    aspect-ratio: 16 / 9;
}

.dashboard-card-tall {
    min-height: 360px;
}
</style>
