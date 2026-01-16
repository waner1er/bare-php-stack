import { defineConfig } from "vite";
import { resolve } from "path";

export default defineConfig({
  publicDir: false,
  build: {
    outDir: "public/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        frontend: resolve(__dirname, "src/Interface/FrontEnd/Assets/js/app.js"),
        "frontend-style": resolve(
          __dirname,
          "src/Interface/FrontEnd/Assets/scss/app.scss"
        ),

        admin: resolve(__dirname, "src/Interface/Admin/Assets/js/admin.js"),
        "admin-style": resolve(
          __dirname,
          "src/Interface/Admin/Assets/scss/admin.scss"
        ),
        "admin-crud": resolve(
          __dirname,
          "src/Interface/Admin/Assets/js/crud.js"
        ),
        "admin-crud-style": resolve(
          __dirname,
          "src/Interface/Admin/Assets/scss/pages/_crud.scss"
        ),

        // common: resolve(__dirname, "src/Interface/Common/Assets/js/common.js"),
        // "common-style": resolve(
        //   __dirname,
        //   "src/Interface/Common/Assets/scss/common.scss"
        // ),
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
    host: true,
    // Ensure the dev server watches source files under `src` (HMR)
    watch: {
      // ignore build outputs and node_modules but watch everything in src
      ignored: ['**/node_modules/**', '**/public/**']
    },
  },
});
