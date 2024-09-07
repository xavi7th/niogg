export const paths = [
  'Modules/PublicPage/resources/js/app.js',
];

export const aliases = {
  '@publicpage-pages': '/Modules/PublicPage/resources/js/Pages',
  '@publicpage-shared': '/Modules/PublicPage/resources/js/Shared',
  '@publicpage-components': '/Modules/PublicPage/resources/js/Components',
  '@publicpage-partials': '/Modules/PublicPage/resources/js/Pages/Partials',
  '@publicpage-assets': '/Modules/PublicPage/resources',
};

export const concatFiles = [
  {
    files: [
      'Modules/PublicPage/resources/template/assets/js/jquery-3.3.1.min.js',
      'Modules/PublicPage/resources/template/assets/js/plugins.js',
    ],
    outputFile: 'public/build/assets/app.js',
  },
  {
    files: [
      'Modules/PublicPage/resources/template/assets/js/main.js',
    ],
    outputFile: 'public/build/assets/app-init.js',
  }
];

export const publicFiles = [
  {
    src: 'Modules/PublicPage/resources/images/static',
    dest: './',
    rename: 'img'
  },
  {
    src: 'Modules/PublicPage/resources/template/assets/fonts',
    dest: './'
  },
];
