import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from "node:url";

export default defineConfig(({ mode }) => {
    const isDev = mode === 'development';

    return {
        plugins: [vue()],
        resolve: {
            alias: {
              "@": fileURLToPath(new URL("./src", import.meta.url)), // Alias für "src"-Ordner
            },
        },
        build: {
            sourcemap: true,
            rollupOptions: {
                input: {
                    'studip-checkin': 'src/checkin.js',
                    'studip-checkin-admin': 'src/checkin-admin.js',
                    'studip-checkin-profile': 'src/checkin-profile.js',
                },
                output: {
                    entryFileNames: `[name].js`,
                    assetFileNames: (assetInfo) => {
                        if (assetInfo.name == 'style.css') return 'checkin.css';
                        return assetInfo.name;
                    },
                },
            },
        },
        define: {
            'process.env.NODE_ENV': JSON.stringify(mode),
            '__VUE_PROD_DEVTOOLS__': isDev,
            '__VUE_PROD_HYDRATION_MISMATCH_DETAILS__': isDev
        },
    };
});
