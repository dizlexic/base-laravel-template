<script setup lang="ts">
import { ref, useTemplateRef } from 'vue';
import type { VTextField } from 'vuetify/components';

defineOptions({ inheritAttrs: false });

const showPassword = ref(false);
const fieldRef = useTemplateRef<InstanceType<typeof VTextField>>('fieldRef');

defineExpose({
    focus: () => fieldRef.value?.focus(),
});
</script>

<template>
    <!--
        Thin wrapper around Vuetify's `<v-text-field>` that wires a Material
        Design Icons eye toggle into the field's `append-inner` slot. All
        other props (`v-model`, `label`, `error-messages`, …) flow through
        via `v-bind="$attrs"`.
    -->
    <v-text-field
        ref="fieldRef"
        :type="showPassword ? 'text' : 'password'"
        :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
        autocomplete="current-password"
        v-bind="$attrs"
        @click:append-inner="showPassword = !showPassword"
    />
</template>
