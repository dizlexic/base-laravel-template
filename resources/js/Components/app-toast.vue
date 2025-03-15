<script setup>
import {toast, Toaster} from 'vue-sonner'
import {usePage} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import {vuetify} from "@/plugins/vuetify";

const themeColors = vuetify.theme.current.value.colors;

const last = ref(null)
onMounted(() => {
    const flash = usePage().props.flash;
    if (flash && flash.id !== last.value) {
        last.value = flash.id
        toast(flash.message, {
            type: flash.type || 'success',
            duration: flash.duration || 3000,
            position: flash.position || 'top-right',
            class: flash.type || 'success',
            style: {
                background: themeColors[flash.type || 'primary'],
                color: (themeColors?.['on-' + flash.type] ?? themeColors['on-primary']),
                border: 'none',
                opacity: 0.9,
            }
        })
        console.debug(JSON.stringify(flash))
        console.debug(JSON.stringify(themeColors, null, 2))

    }
})

</script>

<template>
    <Toaster
        :duration="3000"
    />
</template>

<style scoped>

</style>
