import { defineConfig } from 'vite';
import liveReload from 'vite-plugin-live-reload';
import path from 'path';
import { fileURLToPath } from 'url';
import autoprefixer from 'autoprefixer';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const themeDir = path.resolve(__dirname, 'cms/wp-content/themes/custom-theme');

export default defineConfig({
  root: path.resolve(themeDir, 'assets'),
  base: '/wp-content/themes/custom-theme/assets/dist/',

  plugins: [
    liveReload([
      'cms/wp-content/themes/custom-theme/**/*.php',
    ]),
  ],

  build: {
    outDir: path.resolve(themeDir, 'assets/dist'),
    emptyOutDir: true,

    rollupOptions: {
      input: {
        main:          path.resolve(themeDir, 'assets/src/js/main.js'),
        // Lazy-Chunks – nur geladen wenn DOM-Element vorhanden
        ajaxFilters:   path.resolve(themeDir, 'assets/src/js/components/ajax-filters.js'),
        ajaxSearch:    path.resolve(themeDir, 'assets/src/js/components/ajax-search.js'),
        loadMore:      path.resolve(themeDir, 'assets/src/js/components/load-more.js'),
        googleMaps:    path.resolve(themeDir, 'assets/src/js/components/google-maps.js'),
        notifications: path.resolve(themeDir, 'assets/src/js/components/notifications.js'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/chunks/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name?.endsWith('.css')) {
            return 'css/style.css';
          }
          if (/\.(png|jpe?g|svg|gif|webp)$/.test(assetInfo.name ?? '')) {
            return 'images/[name][extname]';
          }
          return 'assets/[name][extname]';
        },
      },
    },

    manifest: true,
    cssCodeSplit: false,
    chunkSizeWarningLimit: 200,

    // console.log + debugger in Production entfernen
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console:  true,
        drop_debugger: true,
        pure_funcs: ['console.log', 'console.info', 'console.debug'],
      },
    },
  },

  server: {
    host: 'localhost',
    port: 3000,
    strictPort: true,
    cors: true,
    hmr: {
      host: 'localhost',
      port: 3000,
    },
  },

  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler', // Unterdrückt legacy-js-api Warning
      },
    },
    postcss: {
      plugins: [
        autoprefixer({
          overrideBrowserslist: ['last 2 versions', '> 1%', 'not dead'],
        }),
      ],
    },
  },

  resolve: {
    alias: {
      '@': path.resolve(themeDir, 'assets/src'),
    },
  },
});