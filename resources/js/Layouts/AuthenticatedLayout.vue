<script setup>
import AppToast from "@/Components/app-toast.vue";
</script>

<template>
    <v-layout class="rounded rounded-md">
        <v-system-bar color="grey-darken-3"></v-system-bar>
        <v-navigation-drawer
            color="grey-darken-2"
            permanent
            width="72"
        ></v-navigation-drawer>

        <v-navigation-drawer
            color="grey-darken-1"
            permanent
            width="150"
        ></v-navigation-drawer>

        <v-app-bar
            color="grey"
            flat
            height="48"
        ></v-app-bar>

        <v-navigation-drawer
            color="grey-lighten-1"
            location="right"
            permanent
            width="150"
        ></v-navigation-drawer>

        <v-app-bar
            color="grey-lighten-2"
            flat
            height="48"
            location="bottom"
        ></v-app-bar>

        <v-main class="d-flex align-center justify-center" height="300">
            <v-container>
                <v-sheet
                    border="dashed md"
                    color="surface-light"
                    height="150"
                    rounded="lg"
                    width="100%"
                >
                    <slot name="default"></slot>
                </v-sheet>
            </v-container>
        </v-main>
        <app-toast/>
    </v-layout>
</template>
