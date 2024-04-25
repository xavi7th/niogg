import sveltePreprocess from 'svelte-preprocess';
// import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

export default {
  // svelte options
  extensions: ['.svelte'],

  compilerOptions: {},

  // @see https://kit.svelte.dev/docs/integrations
  // This is faster but has no support for :global{ //selectors }
  // This alos has no support for template tags wrapping HTML code in svelte files
  // This also has no need for npm install -D sass
  // preprocess: [vitePreprocess()],


  // must have npm install -D sass
  preprocess: [sveltePreprocess()],

  onwarn(warning, defaultHandler) {
    if (
      warning.code === 'a11y-distracting-elements' ||
      warning.code === 'anchor-is-valid' ||
      warning.code === 'a11y-invalid-attribute' ||
      warning.code === 'a11y-media-has-caption' ||
      warning.code === 'a11y-missing-attribute' ||
      warning.code === 'a11y-missing-content' ||
      warning.code === 'a11y-no-static-element-interactions' ||
      (warning. code === 'missing-declaration' && warning.frame.includes( 'route' ) )
    ) return;

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
