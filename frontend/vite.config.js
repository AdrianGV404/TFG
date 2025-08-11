// vite.config.js
import { defineConfig, loadEnv } from "vite";
import react from "@vitejs/plugin-react";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "VITE_");

  return {
    plugins: [react()],
    server: {
      proxy: {
        // Proxy para que /api se redirija al backend Django
        "/api": {
          target: env.VITE_API_BASE_URL, // viene de .env
          changeOrigin: true,
          secure: false,
          rewrite: (path) => path, // no modifica la ruta
        },
      },
    },
  };
});
