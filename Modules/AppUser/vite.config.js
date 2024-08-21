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
    outputFile: 'public/build/assets/appuser-vendor.js',
  },
  {
    files: [
      'Modules/AppUser/resources/js/vendor/three.js',
    ],
    outputFile: 'public/build/assets/appuser-init.js',
  }
];
