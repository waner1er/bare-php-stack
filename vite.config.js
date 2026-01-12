import { defineConfig } from "vite";
import { resolve } from "path";

export default defineConfig({
  build: {
    outDir: "public/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        // FrontEnd
        frontend: resolve(__dirname, "src/Interface/FrontEnd/Assets/js/app.js"),
        "frontend-style": resolve(
          __dirname,
          "src/Interface/FrontEnd/Assets/scss/app.scss"
        ),

        // Admin
        admin: resolve(__dirname, "src/Interface/Admin/Assets/js/admin.js"),
        "admin-style": resolve(
          __dirname,
          "src/Interface/Admin/Assets/scss/admin.scss"
        ),

        // Common (partagé)
        common: resolve(__dirname, "src/Interface/Common/Assets/js/common.js"),
        "common-style": resolve(
          __dirname,
          "src/Interface/Common/Assets/scss/common.scss"
        ),
      },
      output: {
        entryFileNames: "js/[name].js",
        chunkFileNames: "js/[name]-[hash].js",
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith(".css")) {
            return "css/[name][extname]";
          }
          return "assets/[name]-[hash][extname]";
        },
      },
    },
    manifest: true,
    sourcemap: true,
  },
  css: {
    devSourcemap: true,
  },
  server: {
    port: 3000,
    strictPort: false,
  },
});
