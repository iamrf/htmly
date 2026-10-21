/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.html.php',
    './functions.php',
    './js/**/*.js',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        vazir: ['Vazirmatn', 'Tahoma', 'ui-sans-serif', 'sans-serif'],
      },
      colors: {
        brand: {
          DEFAULT: '#18181b',
          accent: '#3f3f46',
          faint: '#f4f4f5',
        },
      },
      boxShadow: {
        glass: '0 1px 0 0 rgba(255,255,255,0.05) inset, 0 8px 32px rgba(0,0,0,0.08)',
      },
      backdropBlur: {
        glass: '12px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
};
