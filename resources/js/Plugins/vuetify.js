// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import {BaseLightTheme} from "@/Themes/BaseLightTheme.js";
import {BaseDarkTheme} from "@/Themes/BaseDarkTheme.js";

const conf = {
    components,
    directives,
    theme: {
        defaultTheme: "BaseLightTheme",
        variations: {
            colors: ['primary', 'secondary'],
            lighten: 4,
            darken: 4,
        },
        themes: {
            BaseLightTheme,
            BaseDarkTheme,
        },
    },
}

export const vuetify = createVuetify(conf)

export const ssr = createVuetify(Object.assign({}, conf, {ssr: true}))

export default vuetify
