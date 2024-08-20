export const paths = [
  'Modules/UserAuth/resources/js/app.js',
];

export const aliases = {
  '@userauth-pages': '/Modules/UserAuth/resources/js/Pages',
  '@userauth-shared': '/Modules/UserAuth/resources/js/Shared',
  '@userauth-components': '/Modules/UserAuth/resources/js/Components',
  '@userauth-partials': '/Modules/UserAuth/resources/js/Pages/Partials',
  '@userauth-assets': '/Modules/UserAuth/resources'
};

export const concatFiles = [
  {
    files: [
      'Modules/UserAuth/resources/js/vendor/one.js',
      'Modules/UserAuth/resources/js/vendor/two.js',
    ],
    outputFile: 'public/build/assets/vendor/userauth-vendor.js',
  },
  {
    files: [
      'Modules/UserAuth/resources/js/vendor/three.js',
    ],
    outputFile: 'public/build/assets/vendor/userauth-init.js',
  }
];
