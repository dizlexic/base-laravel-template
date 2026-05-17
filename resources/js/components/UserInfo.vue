<script setup lang="ts">
import { computed } from 'vue';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

type Props = {
    user: User;
    showEmail?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

const showAvatar = computed(
    () => props.user.avatar && props.user.avatar !== '',
);
</script>

<template>
    <!--
        Compact identity row used in headers, sidebars, and menus. The
        `<v-avatar>` shows the uploaded image when present and falls back to
        the user's initials over the Vuetify `secondary` colour.
    -->
    <div class="d-flex align-center ga-3 w-100">
        <v-avatar size="32" rounded="lg" color="secondary">
            <v-img
                v-if="showAvatar"
                :src="user.avatar!"
                :alt="user.name"
                cover
            />
            <span v-else class="text-body-2 font-weight-bold">
                {{ getInitials(user.name) }}
            </span>
        </v-avatar>
        <div class="d-flex flex-column flex-grow-1 overflow-hidden">
            <span class="text-body-2 font-weight-medium text-truncate">
                {{ user.name }}
            </span>
            <span
                v-if="showEmail"
                class="text-caption text-medium-emphasis text-truncate"
            >
                {{ user.email }}
            </span>
        </div>
    </div>
</template>
