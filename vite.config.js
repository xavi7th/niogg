import fs from "node:fs";
import { defineConfig } from 'vite';
import concat from 'rollup-plugin-concat';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import collectModuleAssetsPaths from './vite-module-loader.js';

async function getConfig () {
  const paths = [
    'resources/js/app.js',
  ];
  const modulesConfig = await collectModuleAssetsPaths( paths, 'Modules' );
  const folderName = 'public/build/assets/vendor';

  try {
    if (!fs.existsSync(folderName)) {
      fs.mkdirSync(folderName);
    }
  } catch (err) {
    console.error(err);
  }

  return defineConfig( {
    build: {
      rollupOptions: {
        external: [ // Any url that begins from these absolute paths should be considered external urls and will be resolved at runtime
          /^\/build\/vendor/,
          /^\/img\//,
        ]
      }
    },
    plugins: [
      concat({
        groupedFiles: [...modulesConfig.concatFiles],
      }),
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
