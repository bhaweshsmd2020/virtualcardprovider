import './bootstrap'
import '@vueform/multiselect/themes/default.css'
import VueLazyLoad from 'vue3-lazyload'
import trans from '@/Composables/transComposable'
import { Icon } from '@iconify/vue'
import { createApp, h } from 'vue'
import { Link, createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

const appName = document.querySelector('meta[name="app-name"]')?.content || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .mixin({ methods: { trans, route: window.route } })
      .component('Link', Link)
      .component('Icon', Icon)
      .use(plugin)
      .use(VueLazyLoad, {
        error: '/assets/images/no-image-icon.png',
        loading: '/assets/images/lazy.svg',
      })
      .use(createPinia())
      .mount(el)
  },
  progress: {
    color: '#d2f34c',
    showSpinner: true
  }
})
