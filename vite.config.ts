import { fileURLToPath, URL } from "url"

import { defineConfig } from "vite"
import vue from "@vitejs/plugin-vue"

import path, { resolve } from "path"

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
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
      manualChunks: {
        // easymde: ["easymde"],
      },
    },
  },
})
