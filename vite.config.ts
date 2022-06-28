import { fileURLToPath, URL } from "url"

import { defineConfig } from "vite"
import vue from "@vitejs/plugin-vue"

import path, { resolve } from "path"

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
  build: {
    outDir: resolve(__dirname, "assets/ui"),
    assetsDir: "",
    manifest: true,
    rollupOptions: {
      input: {
        "ticket-listing": resolve(
          __dirname,
          "traq-ui/ticket-listing/ticket-listing.ts"
        ),
      },
    },
  },
})
