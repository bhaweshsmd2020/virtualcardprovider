import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import DefineOptions from 'unplugin-vue-define-options/vite'
export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js', 'resources/scss/admin/main.scss'],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    DefineOptions()
  ]
})
