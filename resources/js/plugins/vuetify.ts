import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import { aliases, mdi } from 'vuetify/iconsets/mdi';

/**
 * Vuetify is the single source of truth for component styling in this app.
 *
 * Icons use the Material Design Icons (MDI) font, registered via the `mdi`
 * iconset. The CSS for the MDI font is imported in `resources/css/app.css`
 * so individual components can simply reference icon names as strings
 * (e.g. `<v-icon icon="mdi-account" />` or `prepend-icon="mdi-lock"`).
 */
export const vuetify = createVuetify({
    defaults: {
        VBtn: {
            variant: 'flat',
        },
        VTextField: {
            variant: 'outlined',
            density: 'comfortable',
            color: 'primary',
        },
        VCheckbox: {
            color: 'primary',
            density: 'comfortable',
            hideDetails: 'auto',
        },
        VCard: {
            rounded: 'lg',
        },
    },
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: { mdi },
    },
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                    background: '#FDFDFC',
                    surface: '#FFFFFF',
                    primary: '#171717',
                    'primary-darken-1': '#000000',
                    secondary: '#EBEBEB',
                    success: '#10B981',
                    info: '#3B82F6',
                    warning: '#F59E0B',
                    error: '#DC2626',
                    'on-surface': '#0A0A0A',
                    'on-background': '#0A0A0A',
                },
            },
            dark: {
                dark: true,
                colors: {
                    background: '#0A0A0A',
                    surface: '#161615',
                    primary: '#FAFAFA',
                    'primary-darken-1': '#E5E5E5',
                    secondary: '#262626',
                    success: '#10B981',
                    info: '#60A5FA',
                    warning: '#FBBF24',
                    error: '#F87171',
                    'on-surface': '#FAFAFA',
                    'on-background': '#FAFAFA',
                },
            },
        },
    },
});
