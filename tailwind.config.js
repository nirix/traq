/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{vue,js,ts,jsx,tsx,php,phtml}", "./vendor/traq/**/*.phtml"],
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
        gold: {
          100: "#fefdf6",
          200: "#fdfaee",
          300: "#fdf8e5",
          400: "#fcf5dd",
          500: "#fbf3d4",
          600: "#c9c2aa",
          700: "#97927f",
          800: "#646155",
          900: "#32312a",
        },
        // gold: {
        //   100: "#fffff8",
        //   200: "#fffff1",
        //   300: "#ffffeb",
        //   400: "#ffffe4",
        //   500: "#ffffdd",
        //   600: "#ccccb1",
        //   700: "#999985",
        //   800: "#666658",
        //   900: "#33332c",
        // },
      },
    },
  },
  plugins: [],
  corePlugins: {
    // preflight: false,
  },
}
