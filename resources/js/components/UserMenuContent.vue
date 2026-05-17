<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import UserInfo from '@/components/UserInfo.vue';
import { toUrl } from '@/lib/utils';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';

type Props = {
    user: User;
};

defineProps<Props>();

const emit = defineEmits<{
    (event: 'navigate'): void;
}>();

const goToSettings = (): void => {
    emit('navigate');
    router.visit(toUrl(edit()));
};

const handleLogout = (): void => {
    emit('navigate');
    router.post(toUrl(logout()), {}, { onSuccess: () => router.flushAll() });
};
</script>

<template>
    <!--
        Rendered inside the `<v-menu>` activator card by AppHeader / NavUser.
        Uses a Vuetify list so the menu items pick up consistent ripple +
        focus styling, and an MDI icon for each action.
    -->
    <v-list density="comfortable" nav>
        <v-list-item class="pa-2">
            <UserInfo :user="user" :show-email="true" />
        </v-list-item>
        <v-divider class="my-1" />
        <v-list-item
            prepend-icon="mdi-cog-outline"
            title="Settings"
            @click="goToSettings"
        />
        <v-divider class="my-1" />
        <v-list-item
            prepend-icon="mdi-logout"
            title="Log out"
            data-test="logout-button"
            @click="handleLogout"
        />
    </v-list>
</template>
