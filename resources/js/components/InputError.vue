<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <!--
        Vuetify's form inputs already render their own error messages via
        their `:error-messages` prop, so prefer wiring validation directly
        into the field. This component remains for the rare standalone case
        (e.g. cross-field server errors) and styles the message to match the
        Vuetify error colour.
    -->
    <div v-if="message" class="text-error text-caption mt-1">
        {{ message }}
    </div>
</template>
