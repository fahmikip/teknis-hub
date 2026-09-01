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
            colors: {
                brand: {
                    DEFAULT: '#C8102E',
                    dark: '#9E0B24',
                    darker: '#7A081C',
                },
                gold: '#C9A227',
                surface: '#FFFFFF',
                app: '#F7F7F5',
                ink: {
                    DEFAULT: '#1F2937',
                    muted: '#6B7280',
                    light: '#9CA3AF',
                },
                line: '#E5E7EB',
                success: '#166534',
                warning: '#A16207',
                danger: '#B91C1C',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': ['0.6875rem', { lineHeight: '1rem' }],
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(0 0 0 / 0.04)',
            },
            borderRadius: {
                md: '0.375rem',
                lg: '0.5rem',
            },
        },
    },

    plugins: [forms],
};
