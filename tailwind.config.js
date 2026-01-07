import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            // --- ADDED: Custom Maroon Color Palette ---
            colors: {
                maroon: {
                    50: '#fdf2f2',
                    100: '#fde8e8',
                    200: '#fbd5d5',
                    300: '#f8b4b4',
                    400: '#f98080',
                    500: '#f05252',
                    600: '#e02424',
                    700: '#800000', // Primary Brand Color
                    800: '#600000', // Hover State
                    900: '#400000', // Darker State
                },
            },
        },
    },

    plugins: [forms],
};