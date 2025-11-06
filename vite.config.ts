import { fileURLToPath, URL } from "url"
import { defineConfig } from "vite"
import path, { resolve } from "path"

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src/assets", import.meta.url)),
    },
  },
  build: {
    outDir: resolve(__dirname, "assets/ui"),
    assetsDir: "",
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, "src/assets/main.ts"),
      },
      output: {
        manualChunks: {
          vendor: ["alpinejs", "axios", "luxon", "marked"],
          easymde: ["easymde"],
          fontawesome: ["@fortawesome/fontawesome-svg-core", "@fortawesome/free-regular-svg-icons", "@fortawesome/free-solid-svg-icons", "@fortawesome/vue-fontawesome"],
        }
      },
    },
  },
})
