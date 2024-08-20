import '@publicpage-assets/sass/app.scss'
import { createInertiaApp } from '@inertiajs/svelte'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob(['./Pages/**/*.svelte', '../../Modules/**/Pages/**/*.svelte'], { eager: true })

    let page = undefined, pageUrl = undefined;

    if ( name.includes('::') ) {
      let [module, pageLocation] = name.split('::');
      page = pages[pageUrl = `../../Modules/${module}/resources/js/Pages/${pageLocation}.svelte`] ?? pages[pageUrl = `./Pages/${pageLocation}.svelte`];
    }

    if ( page == undefined ) {
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
