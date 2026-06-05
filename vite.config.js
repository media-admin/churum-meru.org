import { defineConfig } from 'vite';
import liveReload from 'vite-plugin-live-reload';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';
import autoprefixer from 'autoprefixer';

let compression = null;
try {
  const mod = await import('vite-plugin-compression2');
  compression = mod.compression ?? mod.default;
} catch (e) { /* not installed */ }

const __filename = fileURLToPath(import.meta.url);
const __dirname  = path.dirname(__filename);
const themeDir   = path.resolve(__dirname, 'cms/wp-content/themes/churum-meru-theme');
const isDev = process.env.NODE_ENV !== 'production' && process.env.NODE_ENV !== 'staging';

const copySwiperPlugin = {
  name: 'copy-swiper-umd',
  writeBundle() {
    const src = path.resolve(__dirname, 'node_modules/swiper/swiper-bundle.min.js');
    const dst = path.resolve(themeDir, 'assets/dist/js/chunks/swiper.js');
    if (!fs.existsSync(src)) {
      console.warn('[copy-swiper] swiper-bundle.min.js nicht gefunden:', src);
      return;
    }
    fs.mkdirSync(path.dirname(dst), { recursive: true });
    fs.copyFileSync(src, dst);
    console.log('[copy-swiper] ✓ chunks/swiper.js kopiert');
  },
};

export default defineConfig({
  // 'custom' = kein HTML-Einstiegspunkt → rolldown sucht nicht nach index.html
  appType: 'custom',

  root: path.resolve(themeDir, 'assets'),

  base: isDev
    ? 'http://localhost:3000/'
    : '/cms/wp-content/themes/churum-meru-theme/assets/dist/',

  plugins: [
    liveReload([
      'cms/wp-content/themes/churum-meru-theme/**/*.php',
      'cms/wp-content/themes/churum-meru-theme/**/*.twig',
    ]),
    copySwiperPlugin,
    ...(compression
      ? [
          compression({ algorithm: 'brotliCompress', exclude: [/\.(br|gz)$/] }),
          compression({ algorithm: 'gzip',           exclude: [/\.(br|gz)$/] }),
        ]
      : []),
  ],

  build: {
    outDir:      path.resolve(themeDir, 'assets/dist'),
    emptyOutDir: true,

    rollupOptions: {
      input: {
        main: path.resolve(themeDir, 'assets/src/js/main.js'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/chunks/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name?.endsWith('.css')) return 'css/style.css';
          if (/\.(png|jpe?g|svg|gif|webp)$/.test(assetInfo.name ?? '')) return 'images/[name][extname]';
          return 'assets/[name][extname]';
        },
      },
    },

    manifest:              true,
    cssCodeSplit:          false,
    chunkSizeWarningLimit: 200,
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
    host:       'localhost',
    port:       3000,
    strictPort: true,
    cors:       true,
    hmr: {
      host:     'localhost',
      port:     3000,
      protocol: 'ws',
    },
  },

  css: {
    preprocessorOptions: {
      scss: { api: 'modern-compiler' },
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