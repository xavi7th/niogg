import fs from "node:fs";
import { defineConfig } from 'vite';
import concat from 'rollup-plugin-concat';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import { enhancedImages } from '@sveltejs/enhanced-img';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import collectModuleAssetsPaths from './vite-module-loader.js';

async function getConfig () {
  const paths = [];
  const modulesConfig = await collectModuleAssetsPaths( paths, 'Modules' );
  const folderName = 'public/build/assets';

  try {
    if (!fs.existsSync(folderName)) {
      fs.mkdirSync(folderName, {recursive: true});
    }
  } catch (err) {
    console.error(err);
  }

  return defineConfig( {
    build: {
      emptyOutDir: false,
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
      viteStaticCopy({
        targets: modulesConfig.publicFiles
      }),
      laravel( {
        input: modulesConfig.paths,
        // refresh: true,
      } ),
      enhancedImages(),
      svelte( {} )
    ],
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `
            @use '@publicpage-assets/sass/variables' as *;
          `,
        },
      },
    },
    resolve: {
      alias: {
        '@': '/resources/js',
        ...modulesConfig.aliases
      },
    },
  } );
}

export default getConfig();
