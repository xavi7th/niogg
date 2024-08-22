import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  safelist: [
    {
      pattern: /((text|bg|border)-(red|teal|yellow|blue)-(\d){3})/,
      variants: ['dark']
    },
    // {
    //   pattern: /(text|bg)-(red|green|blue|yellow)-+/,
    //   variants: ['lg', 'hover', 'focus', 'lg:hover'],
    // },
  ],
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.svelte',
    './Modules/**/*.svelte',
  ],

  theme: {
    screens: {
      xs: '320px',
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px',
    },
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [forms],
};
