// tailwind.config.js (Laravel 12 + Vite) — full
import forms from '@tailwindcss/forms'
import daisyui from 'daisyui'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],
    content: [
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.ts',
        './resources/**/*.tsx',
        './resources/**/*.vue',
        './resources/css/**/*.css',
        './storage/framework/views/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        container: {
        center: true,
        padding: {
            DEFAULT: '1rem',
            sm: '1.5rem',
            lg: '2rem',
            xl: '2.5rem',
            '2xl': '3rem',
        },
        },
        extend: {},
    },
    plugins: [forms, daisyui],
    daisyui: {
        themes: ['emerald', 'light', 'dark'],
        base: true,
        styled: true,
        utils: true,
        logs: false,
        rtl: false,
        prefix: '',
    },
    }
