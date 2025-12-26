import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

const appName = import.meta.env.VITE_APP_NAME || 'Enderchive';

const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })

createInertiaApp({
  title: (title) => title ? `${title} - ${appName}` : appName,
  resolve: (name) => {
    const page = pages[`./Pages/${name}.vue`]
    if (!page) {
      throw new Error(`Page not found: ${name}`)
    }
    return page.default || page
  },
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
  progress: {
    color: '#7c3aed',
  },
})
