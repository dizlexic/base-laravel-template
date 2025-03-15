<script setup>
import {Head, router} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import AppToast from "@/Components/app-toast.vue";
</script>

<template>
    <Head :title="$page.props.title"/>
    <v-layout class="rounded rounded-md">
        <v-app-bar :title="$page.props.title" app color="primary">
            <template #title>
                <v-btn
                    :active="route().current('public.landing')"
                    :href="route('public.landing')"
                    variant="text"
                    @click.prevent="router.visit(route('public.landing'))"
                >
                    Site Title
                </v-btn>
            </template>
            <template #default>
                <v-btn
                    :active="route().current('public.login')"
                    :href="route('public.login')"
                    variant="text"
                    @click.prevent="router.visit(route('public.login'))"
                >
                    Login
                </v-btn>
                <v-btn
                    :active="route().current('public.register')"
                    :href="route('public.register')"
                    variant="text"
                    @click.prevent="router.get(route('public.register'))"
                >
                    Register
                </v-btn>
            </template>
        </v-app-bar>
        <v-main class="d-flex align-center justify-center" style="min-height: 300px;">
            <v-breadcrumbs :items="$page.props.breadcrumbs"/>
            <v-container>
                <slot/>
            </v-container>
        </v-main>
        <app-toast/>
    </v-layout>
</template>
