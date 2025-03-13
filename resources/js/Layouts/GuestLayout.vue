<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
</script>

<template>
    <Head :title="$page.props.title"/>
    <v-layout class="rounded rounded-md">
        <v-app-bar :title="$page.props.title" app>
            <template #title>
                <v-btn variant="text">
                    <Link :href="route('public.welcome')">Site Title</Link>
                </v-btn>
            </template>
            <template #default>
                <v-btn :active="route().current('public.login')" variant="text">
                    <Link v-if="!$page.props.auth.user" :href="route('public.login')">Login</Link>
                </v-btn>
                <v-btn :active="route().current('public.register')" variant="text">
                    <Link v-if="!$page.props.auth.user" :href="route('public.register')">Register</Link>
                </v-btn>
            </template>
        </v-app-bar>
        <v-main class="d-flex align-center justify-center" style="min-height: 300px;">
            <v-breadcrumbs :items="$page.props.breadcrumbs"/>
            <v-container>
                <slot/>
            </v-container>
        </v-main>
    </v-layout>
</template>
