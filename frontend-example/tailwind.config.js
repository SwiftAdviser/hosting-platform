/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        abyss: '#020617',
        chrome: '#E2E8F0',
        mercury: '#94A3B8',
        cyanGlow: '#22D3EE',
        deepBlue: '#0F172A',
      },
      fontFamily: {
        heading: ['"Clash Display"', 'sans-serif'],
        sans: ['Manrope', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
