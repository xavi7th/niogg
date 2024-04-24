<?php

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Providers\ConsoleServiceProvider;

return [

  /*
   |--------------------------------------------------------------------------
   | Module Namespace
   |--------------------------------------------------------------------------
   |
   | Default module namespace.
   |
   */

  'namespace' => 'Modules',

  /*
   |--------------------------------------------------------------------------
   | Module Stubs
   |--------------------------------------------------------------------------
   |
   | Default module stubs.
   |
   */

  'stubs' => [
    'enabled' => TRUE,
    'path' => base_path('stubs/nwidart-stubs'),
    'files' => [
      'routes/web' => 'routes/web.php',
      'routes/api' => 'routes/api.php',
      'views/index' => 'resources/views/app.blade.php',
      'scaffold/config' => 'config/config.php',
      'composer' => 'composer.json',
      'assets/js/app' => 'resources/js/app.js',
      'assets/js/vendor/vendor-file-one' => 'resources/js/vendor/one.js',
      'assets/js/vendor/vendor-file-two' => 'resources/js/vendor/two.js',
      'assets/js/vendor/vendor-file-three' => 'resources/js/vendor/three.js',
      'assets/js/svelte-files/index' => 'resources/js/Pages/Index.svelte',
      'assets/js/svelte-files/layout' => 'resources/js/Pages/Layouts/PageLayout.svelte',
      'assets/sass/app' => 'resources/sass/app.scss',
      'vite' => 'vite.config.js',
      'package' => 'package.json',
    ],
    'replacements' => [
      'routes/web' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'CONTROLLER_NAMESPACE'],
      'routes/api' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'CONTROLLER_NAMESPACE'],
      'vite' => ['LOWER_NAME', 'STUDLY_NAME'],
      'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
      'views/index' => ['LOWER_NAME', 'STUDLY_NAME'],
      'assets/js/app' => ['STUDLY_NAME', 'LOWER_NAME'],
      'assets/js/svelte-files/index' => ['STUDLY_NAME', 'LOWER_NAME'],
      'assets/js/svelte-files/layout' => ['STUDLY_NAME', 'LOWER_NAME'],
      'scaffold/config' => ['STUDLY_NAME'],
      'composer' => [
        'LOWER_NAME',
        'STUDLY_NAME',
        'VENDOR',
        'AUTHOR_NAME',
        'AUTHOR_EMAIL',
        'MODULE_NAMESPACE',
        'PROVIDER_NAMESPACE',
      ],
    ],
    'gitkeep' => TRUE,
  ],
  'paths' => [
    /*
     |--------------------------------------------------------------------------
     | Modules path
     |--------------------------------------------------------------------------
     |
     | This path is used to save the generated module.
     | This path will also be added automatically to the list of scanned folders.
     |
     */

    'modules' => base_path('Modules'),
    /*
     |--------------------------------------------------------------------------
     | Modules assets path
     |--------------------------------------------------------------------------
     |
     | Here you may update the modules' assets path.
     |
     */

    'assets' => public_path('modules'),
    /*
     |--------------------------------------------------------------------------
     | The migrations' path
     |--------------------------------------------------------------------------
     |
     | Where you run the 'module:publish-migration' command, where do you publish the
     | the migration files?
     |
     */

    'migration' => base_path('database/migrations'),

    /*
     |--------------------------------------------------------------------------
     | The app path
     |--------------------------------------------------------------------------
     |
     | app folder name
     | for example can change it to 'src' or 'App'
     */
    'app_folder' => 'app/',

    /*
     |--------------------------------------------------------------------------
     | Generator path
     |--------------------------------------------------------------------------
     | Customise the paths where the folders will be generated.
     | Setting the generate key to false will not generate that folder
     */

    'generator' => [
      // app/
      'channels' => ['path' => 'app/Broadcasting', 'generate' => FALSE],
      'command' => ['path' => 'app/Console', 'generate' => FALSE],
      'emails' => ['path' => 'app/Emails', 'generate' => FALSE],
      'event' => ['path' => 'app/Events', 'generate' => FALSE],
      'jobs' => ['path' => 'app/Jobs', 'generate' => FALSE],
      'listener' => ['path' => 'app/Listeners', 'generate' => FALSE],
      'model' => ['path' => 'app/Models', 'generate' => TRUE],
      'notifications' => ['path' => 'app/Notifications', 'generate' => FALSE],
      'observer' => ['path' => 'app/Observers', 'generate' => FALSE],
      'policies' => ['path' => 'app/Policies', 'generate' => TRUE],
      'provider' => ['path' => 'app/Providers', 'generate' => TRUE],
      'route-provider' => ['path' => 'app/Providers', 'generate' => TRUE],
      'repository' => ['path' => 'app/Repositories', 'generate' => FALSE],
      'resource' => ['path' => 'app/Transformers', 'generate' => TRUE],
      'rules' => ['path' => 'app/Rules', 'generate' => FALSE],
      'component-class' => ['path' => 'app/View/Components', 'generate' => FALSE],

      // app/Http/
      'controller' => ['path' => 'app/Http/Controllers', 'generate' => TRUE],
      'filter' => ['path' => 'app/Http/Middleware', 'generate' => FALSE],
      'request' => ['path' => 'app/Http/Requests', 'generate' => TRUE],

      // config/
      'config' => ['path' => 'config', 'generate' => TRUE],

      // database/
      'migration' => ['path' => 'database/migrations', 'generate' => TRUE],
      'seeder' => ['path' => 'database/seeders', 'generate' => FALSE],
      'factory' => ['path' => 'database/factories', 'generate' => TRUE],

      // lang/
      'lang' => ['path' => 'lang', 'generate' => FALSE],

      // resource/
      'assets' => ['path' => 'resources', 'generate' => TRUE],
      'views' => ['path' => 'resources/views', 'generate' => TRUE],
      'component-view' => ['path' => 'resources/views/components', 'generate' => FALSE],

      // routes/
      'routes' => ['path' => 'routes', 'generate' => TRUE],

      // tests/
      'test-unit' => ['path' => 'tests/Unit', 'generate' => TRUE],
      'test-feature' => ['path' => 'tests/Feature', 'generate' => TRUE],
    ],
  ],

  /*
   |--------------------------------------------------------------------------
   | Package commands
   |--------------------------------------------------------------------------
   |
   | Here you can define which commands will be visible and used in your
   | application. You can add your own commands to merge section.
   |
   */

  'commands' => ConsoleServiceProvider::defaultCommands()
      ->merge([
        // New commands go here
      ])->toArray(),

  /*
   |--------------------------------------------------------------------------
   | Scan Path
   |--------------------------------------------------------------------------
   |
   | Here you define which folder will be scanned. By default will scan vendor
   | directory. This is useful if you host the package in packagist website.
   |
   */

  'scan' => [
    'enabled' => FALSE,
    'paths' => [
      base_path('vendor/*/*'),
    ],
  ],

  /*
   |--------------------------------------------------------------------------
   | Composer File Template
   |--------------------------------------------------------------------------
   |
   | Here is the config for the composer.json file, generated by this package
   |
   */

  'composer' => [
    'vendor' => env('MODULES_VENDOR', 'xavi7th'),
    'author' => [
      'name' => env('MODULES_NAME', 'Daniel Akhile'),
      'email' => env('MODULES_EMAIL', 'xavi7th@gmail.com'),
    ],
    'composer-output' => FALSE,
  ],

  /*
   |--------------------------------------------------------------------------
   | Caching
   |--------------------------------------------------------------------------
   |
   | Here is the config for setting up the caching feature.
   |
   */

  'cache' => [
    'enabled' => FALSE,
    'driver' => 'file',
    'key' => 'laravel-modules',
    'lifetime' => 60,
  ],

  /*
   |--------------------------------------------------------------------------
   | Choose what laravel-modules will register as custom namespaces.
   | Setting one to false will require you to register that part
   | in your own Service Provider class.
   |--------------------------------------------------------------------------
   */

  'register' => [
    'translations' => TRUE,
    /**
     * load files on boot or register method
     */
    'files' => 'register',
  ],

  /*
   |--------------------------------------------------------------------------
   | Activators
   |--------------------------------------------------------------------------
   |
   | You can define new types of activators here, file, database, etc. The only
   | required parameter is 'class'.
   | The file activator will store the activation status in storage/installed_modules
   */

  'activators' => [
    'file' => [
      'class' => FileActivator::class,
      'statuses-file' => base_path('modules_statuses.json'),
      'cache-key' => 'activator.installed',
      'cache-lifetime' => 604800,
    ],
  ],

  'activator' => 'file',
];
