/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Define your color scheme based on the extracted colors
        'gray-100': '#f9f9f9',
        'gray-200': '#f1f1f4',
        'gray-300': '#dbdfe9',
        'gray-400': '#c4cad4',
        'gray-500': '#a0a8b5',
        'gray-600': '#717786',
        'gray-700': '#4a4f5c',
        'gray-800': '#2e323b',
        'gray-900': '#1a1d24',
        'primary': '#1b84ff',
        'primary-dark': '#0e6cd9',
        'success': '#17c653',
        'success-dark': '#0ea53f',
        'warning': '#ffab00',
        'warning-dark': '#e09600',
        'danger': '#ff5252',
        'danger-dark': '#e13b3b',
        'info': '#0ea5e9',
        'info-dark': '#0284c7',
      },
      boxShadow: {
        'default': '0px 4px 12px 0px rgba(0,0,0,.09)',
        'light': '0px 3px 4px 0px rgba(0,0,0,.03)',
        'medium': '0px 6px 16px 0px rgba(0,0,0,.12)',
      },
      borderRadius: {
        'sm': '.375rem',
        'md': '.5rem',
        'lg': '.75rem',
      },
      screens: {
        'sm': '640px',
        'md': '768px',
        'lg': '1024px',
        'xl': '1280px',
        '2xl': '1536px',
      },
      spacing: {
        'sm': '.625rem',
        'md': '.75rem',
        'lg': '1.25rem',
      },
    },
  },
  plugins: [],
}