import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fbf8ed',
                    100: '#f5eecb',
                    200: '#eddca2',
                    300: '#e3c370',
                    400: '#daa744',
                    500: '#c58d2a',
                    600: '#a76f20',
                    700: '#7f4e1b',
                    800: '#673f1b',
                    900: '#563519',
                },
            },
        },
    },

    plugins: [forms],
};

