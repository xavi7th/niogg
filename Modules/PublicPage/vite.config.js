export const paths = [
  'Modules/PublicPage/resources/js/app.js',
];

export const aliases = {
  '@publicpage-pages': '/Modules/PublicPage/resources/js/Pages',
  '@publicpage-shared': '/Modules/PublicPage/resources/js/Shared',
  '@publicpage-components': '/Modules/PublicPage/resources/js/Components',
  '@publicpage-partials': '/Modules/PublicPage/resources/js/Pages/Partials',
  '@publicpage-assets': '/Modules/PublicPage/resources',
  '@publicpage-template': '/Modules/PublicPage/resources/template/assets',
};

export const concatFiles = [
  {
    files: [
      'Modules/PublicPage/resources/js/vendor/one.js',
      'Modules/PublicPage/resources/js/vendor/two.js',
    ],
    outputFile: 'public/build/assets/vendor/publicpage-vendor.js',
  },
  {
    files: [
      'Modules/PublicPage/resources/js/vendor/three.js',
    ],
    outputFile: 'public/build/assets/vendor/publicpage-init.js',
  }
];

export const publicFiles = [
  // {
  //   src: 'Modules/PublicPage/resources/template/assets/images',
  //   dest: '',
  //   rename: 'img'
  // },
  {
    src: 'Modules/PublicPage/resources/template/assets/fonts',
    dest: './'
  },
];
