import { fileURLToPath, URL } from 'node:url';
import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');

  return {
    plugins: [vue()],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
      },
    },
    server: {
      port: 5173,
      // Local development only: forward /api to Laravel so the browser
      // talks to a single origin (same idea as Nginx in Docker).
      proxy: env.VITE_DEV_PROXY_TARGET
        ? {
            '/api': {
              target: env.VITE_DEV_PROXY_TARGET,
              changeOrigin: true,
            },
          }
        : undefined,
    },
    build: {
      sourcemap: false,
    },
  };
});
