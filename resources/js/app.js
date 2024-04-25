// import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/svelte'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

console.log(appName);

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob(['./Pages/**/*.svelte', '../../Modules/**/Pages/**/*.svelte'], { eager: true })

    let pageUrl = `./Pages/${name}.svelte`;

    if (name.includes('::')) {
      let [module, pageLocation] = name.split('::');
      pageUrl = `../../Modules/${module}/resources/js/Pages/${pageLocation}.svelte`;
    }

    const page = pages[pageUrl];

    if (page == undefined) {
      throw new Error(`Page not found: ${pageUrl}`);
    }

    return page
  },

  setup({ el, App, props }) {
    new App({ target: el, props })
  },

  progress: {
    color: '#4B5563',
  },
})
