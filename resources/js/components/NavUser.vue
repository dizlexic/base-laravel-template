<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import UserInfo from '@/components/UserInfo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const menuOpen = ref(false);
</script>

<template>
    <!--
        Sidebar footer card that opens a `<v-menu>` with profile / logout
        actions. The activator is a clickable list item so it visually
        matches the rest of the drawer.
    -->
    <v-menu
        v-model="menuOpen"
        location="top end"
        offset="8"
        :close-on-content-click="false"
    >
        <template #activator="{ props: activator }">
            <v-list nav density="comfortable" class="pa-2">
                <v-list-item
                    v-bind="activator"
                    rounded="lg"
                    data-test="sidebar-menu-button"
                >
                    <UserInfo :user="user" />
                    <template #append>
                        <v-icon icon="mdi-unfold-more-horizontal" size="18" />
                    </template>
                </v-list-item>
            </v-list>
        </template>
        <v-card min-width="240">
            <UserMenuContent :user="user" @navigate="menuOpen = false" />
        </v-card>
    </v-menu>
</template>
