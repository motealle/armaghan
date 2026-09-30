import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import { fileURLToPath, URL } from 'node:url'

const uiTarget = process.env.ARMAGHAN_UI_TARGET?.trim()

if (uiTarget && (!/^\d{2}$/.test(uiTarget) || Number(uiTarget) <= 26)) {
  throw new Error('Refusing to build into a frozen or invalid numbered UI test target')
}

export default defineConfig({
  base: './',
  plugins: [vue(), tailwindcss()],
  resolve: {
    alias: { '@': fileURLToPath(new URL('./src', import.meta.url)) },
  },
  build: {
    outDir: uiTarget ? `../../t/${uiTarget}` : '../../.build/frontend',
    emptyOutDir: true,
    sourcemap: true,
    target: 'es2022',
  },
})
