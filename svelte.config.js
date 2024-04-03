import sveltePreprocess from 'svelte-preprocess';
// import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

export default {
  // svelte options
  extensions: ['.svelte'],

  compilerOptions: {},

  // @see https://kit.svelte.dev/docs/integrations
  // This is faster but has no support for :global{ //selectors }
  // This also has no need for npm install -D sass
  // preprocess: [vitePreprocess()],


  // must have npm install -D sass
  preprocess: [sveltePreprocess()],

  onwarn(warning, defaultHandler) {
    // don't warn on <marquee> elements, cos they're cool
    if (warning.code === 'a11y-distracting-elements') return;

    // handle all other warnings normally
    defaultHandler(warning);
  },

  // plugin options
  vitePlugin: {

    inspector: true,

    exclude: [],

    // experimental options
    experimental: {}
  }
};
