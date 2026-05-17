<script setup lang="ts">
import { useId } from 'vue';

const patternId = `pattern-${useId()}`;
</script>

<template>
    <!--
        Subtle diagonal-stripe placeholder used by dashboard cards. The stroke
        colour follows the active Vuetify theme via the `on-surface` CSS var,
        so it remains legible on both the light and dark themes.
    -->
    <svg class="placeholder-pattern" fill="none">
        <defs>
            <pattern
                :id="patternId"
                x="0"
                y="0"
                width="8"
                height="8"
                patternUnits="userSpaceOnUse"
            >
                <path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path>
            </pattern>
        </defs>
        <rect
            stroke="none"
            :fill="`url(#${patternId})`"
            width="100%"
            height="100%"
        />
    </svg>
</template>

<style scoped>
.placeholder-pattern {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    stroke: rgba(var(--v-theme-on-surface), 0.2);
}
</style>
