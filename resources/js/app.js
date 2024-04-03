import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/svelte'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  resolve: ( name ) => resolvePageComponent( `./Pages/${name}.svelte`, import.meta.glob( './Pages/**/*.svelte' ) ),
  // resolve: name => {
  //   const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
  //   return pages[`./Pages/${name}.svelte`]
  // },
  setup({ el, App, props }) {
    new App({ target: el, props })
  },
  progress: {
    color: '#4B5563',
  },
})
