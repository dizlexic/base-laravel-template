<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <guest-layout>
        <v-card>
            <v-card-title class="bg-primary text-subtitle-1 py-1 d-flex justify-start align-items-center">
                <v-icon>
                    mdi-home
                </v-icon>
                Home
                <v-spacer/>
            </v-card-title>
            <v-card-text class="py-4">
                <p>Welcome to the home page</p>
            </v-card-text>
        </v-card>
    </guest-layout>
</template>
