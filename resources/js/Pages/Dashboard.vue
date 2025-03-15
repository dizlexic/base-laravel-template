<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {route} from 'ziggy-js'
</script>

<template>
    <Head title="Dashboard"/>
    <authenticated-layout>
        <h1>Dashboard</h1>
        <v-btn
            :href="route('user.logout')"
            color="primary"
            @click.prevent="router.post(route('user.logout'))"
        >
            Logout
        </v-btn>
    </authenticated-layout>
</template>
