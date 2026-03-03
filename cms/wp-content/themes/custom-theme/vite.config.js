import { defineConfig } from 'vite';

export default defineConfig({
  build: {
    outDir: 'assets/dist/js',
    rollupOptions: {
      input: 'assets/src/js/main.js',
      output: {
        entryFileNames: 'main.js',
      },
    },
  },
});
