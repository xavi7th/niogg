// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
//
// export default defineConfig({
//     build: {
//         outDir: '../../public/build-appuser',
//         emptyOutDir: true,
//         manifest: true,
//     },
//     plugins: [
//         laravel({
//             publicDirectory: '../../public',
//             buildDirectory: 'build-appuser',
//             input: [
//                 __dirname + '/resources/sass/app.scss',
//                 __dirname + '/resources/js/app.js'
//             ],
//             refresh: true,
//         }),
//     ],
// });

export const paths = [
  'Modules/AppUser/resources/js/app.js',
];

export const aliases = {
  '@appuser-pages': '/Modules/AppUser/resources/js/Pages',
  '@appuser-shared': '/Modules/AppUser/resources/js/Shared',
  '@appuser-components': '/Modules/AppUser/resources/js/Components',
  '@appuser-partials': '/Modules/AppUser/resources/js/Pages/Partials',
  '@appuser-assets': '/Modules/AppUser/resources'
};

export const concatFiles = [
  {
    files: [
      'Modules/AppUser/resources/js/vendor/one.js',
      'Modules/AppUser/resources/js/vendor/two.js',
    ],
    outputFile: 'public/build/assets/vendor/appuser-vendor.js',
  },
  {
    files: [
      'Modules/AppUser/resources/js/vendor/three.js',
    ],
    outputFile: 'public/build/assets/vendor/appuser-init.js',
  }
];
