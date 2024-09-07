<?php

namespace Modules\Conference\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ConferenceServiceProvider extends ServiceProvider
{
  protected string $moduleName = 'Conference';

  protected string $moduleNameLower = 'conference';

  public function boot(): void
  {
    $this->registerConfig();
    $this->registerViews();
    $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
  }

  public function register(): void
  {
    $this->app->register(RouteServiceProvider::class);
  }

  protected function registerConfig(): void
  {
    $this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php')], 'config');
    $this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);
  }

  public function registerViews(): void
  {
    $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
    $sourcePath = module_path($this->moduleName, 'resources/views');

    $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower . '-module-views']);

    $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

    $componentNamespace = str_replace('/', '\\', config('modules.namespace') . '\\' . $this->moduleName . '\\' . ltrim(config('modules.paths.generator.component-class.path'), config('modules.paths.app_folder', '')));
    Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
  }

  private function getPublishableViewPaths(): array
  {
    $paths = [];
    foreach (config('view.paths') as $path) {
      if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
        $paths[] = $path . '/modules/' . $this->moduleNameLower;
      }
    }

    return $paths;
  }
}
