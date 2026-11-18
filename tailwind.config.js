/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                navy: { DEFAULT: '#0B1F6B', 700: '#122a85', 900: '#07154a' },
                kuning: { DEFAULT: '#FFC800', 600: '#e6b400' },
                sukses: '#16A34A',
            },
            fontFamily: { sans: ['Inter', 'Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
        },
    },
    plugins: [],
};
