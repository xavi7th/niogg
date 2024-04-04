import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import collectModuleAssetsPaths from './vite-module-loader.js';

async function getConfig () {
  const paths = [
    'resources/js/app.js',
  ];
  const modulesConfig = await collectModuleAssetsPaths( paths, 'Modules' );

  return defineConfig( {
    plugins: [
      laravel( {
        input: modulesConfig.paths,
        // refresh: true,
      } ),
      svelte( {} )
    ],
    resolve: {
      alias: {
        '@': '/resources/js',
        ...modulesConfig.aliases
      },
    },
  } );
}

export default getConfig();
