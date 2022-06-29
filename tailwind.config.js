/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./traq-ui/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        brand: {
          100: "#d6e4ee",
          200: "#aec9dd",
          300: "#85aecc",
          400: "#5d93bb",
          500: "#3478aa",
          600: "#2a6088",
          700: "#1f4866",
          800: "#153044",
          900: "#0a1822",
        },
      },
    },
  },
  plugins: [],
  corePlugins: {
    preflight: false,
  },
}
