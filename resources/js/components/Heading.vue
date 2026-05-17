<script setup lang="ts">
type Props = {
    title: string;
    description?: string;
    variant?: 'default' | 'small';
};

withDefaults(defineProps<Props>(), {
    variant: 'default',
});
</script>

<template>
    <header :class="variant === 'small' ? '' : 'mb-6'">
        <h2
            :class="
                variant === 'small'
                    ? 'text-body-1 font-weight-medium mb-1'
                    : 'text-h6 font-weight-bold mb-1'
            "
        >
            {{ title }}
        </h2>
        <p v-if="description" class="text-body-2 text-medium-emphasis">
            {{ description }}
        </p>
    </header>
</template>
