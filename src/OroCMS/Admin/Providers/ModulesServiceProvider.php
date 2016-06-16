<?php
namespace OroCMS\Admin\Providers;

use OroCMS\Admin\Repositories\ModuleRepository;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->app['modules']->boot();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * Register the service provider.
     */
    protected function registerServices()
    {
        $this->app->singleton('modules', function ($app) {
            $path = $app['config']->get('admin.modules.path');

            return new ModuleRepository($app, $path);
        });

        $this->app['modules']->register();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
