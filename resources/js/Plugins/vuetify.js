// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const conf = {
    components,
    directives,
}
export const vuetify = createVuetify(conf)

export const ssr = createVuetify({
    ...conf,
    ssr: true,
})

export default vuetify
