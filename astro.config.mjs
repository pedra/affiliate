import { defineConfig } from 'astro/config';

// https://astro.build/config
export default defineConfig({
	outDir: './app/public',
	vite: {
		build: {
			rollupOptions: {
				output: {
					entryFileNames: 'js/script.js',
					chunkFileNames: 'chunks/chunk.[hash].mjs',
					assetFileNames: 'assets/asset.[hash][extname]',
				},
			},
		},
	},
});
