import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';
import plugin from 'tailwindcss/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['Roboto Mono', ...defaultTheme.fontFamily.mono],
                condensed: ['Sofia Sans Extra Condensed', ...defaultTheme.fontFamily.mono]
            },
            colors: {
                primary: `var(--primary)`,//colors.yellow[400],
                secondary: `var(--secondary)`//colors.orange[500]
            }
        },
    },

    plugins: [
        forms,
        plugin(function ({ addVariant }) {
            addVariant("light", ".light-mode &");
        })
    ],
};
