import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  root: resolve(__dirname, '.'),
  server: {
    port: process.env.PORT || 3000,
    host: '0.0.0.0',
    cors: true,
    proxy: {
      '/api': {
        target: 'http://localhost:3000',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, ''),
      },
    },
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        server: resolve(__dirname, 'server.js'),
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: '[name]-[hash].js',
        assetFileNames: '[name]-[hash].[extname]',
        manualChunks: undefined,
      },
    },
  },
  define: {
    'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development'),
    'process.env.PORT': JSON.stringify(process.env.PORT || '3000'),
    'process.env.SQLHOST': JSON.stringify(process.env.SQLHOST || ''),
    'process.env.SQLUSER': JSON.stringify(process.env.SQLUSER || ''),
  },
  optimizeDeps: {
    include: [
      '@ai-rpg-engine/core',
      'express',
      'cookie-parser',
      'morgan',
      'path',
      'cors',
      'multer',
      'helmet',
      'mysql',
      'csurf',
    ],
  },
});
