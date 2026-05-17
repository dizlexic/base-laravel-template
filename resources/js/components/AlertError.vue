<script setup lang="ts">
import { computed } from 'vue';

type Props = {
    errors: string[];
    title?: string;
};

const props = withDefaults(defineProps<Props>(), {
    title: 'Something went wrong.',
});

const uniqueErrors = computed(() => Array.from(new Set(props.errors)));
</script>

<template>
    <v-alert
        type="error"
        variant="tonal"
        density="comfortable"
        icon="mdi-alert-circle-outline"
        :title="title"
    >
        <ul class="ms-4 alert-error-list">
            <li
                v-for="(error, index) in uniqueErrors"
                :key="index"
                class="text-body-2"
            >
                {{ error }}
            </li>
        </ul>
    </v-alert>
</template>

<style scoped>
.alert-error-list {
    list-style: disc inside;
}
</style>
