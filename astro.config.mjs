import { defineConfig } from 'astro/config';

// https://astro.build/config
export default defineConfig({
	outDir: './app/public',
	vite: {
		build: {
			inlineStylesheets: 'never',
			rollupOptions: {
				output: {
					entryFileNames: 'js/[name].js',
					chunkFileNames: 'chunks/chunk.[hash].mjs',
					assetFileNames: 'css/[name][extname]',
				},
			},
		},
	},
});
