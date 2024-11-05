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
        'gray-300': '#dbdfe9',
        'primary': '#1b84ff',
        'success': '#17c653',
        // Continue to add other colors from your file as needed
      },
      boxShadow: {
        'default': '0px 4px 12px 0px rgba(0,0,0,.09)',
        'light': '0px 3px 4px 0px rgba(0,0,0,.03)',
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

