<?php
namespace OroCMS\Admin\Providers;

use OroCMS\Admin\Repositories\PluginRepository;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class PluginsServiceProvider extends ServiceProvider
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
        $this->app['plugins']->boot();
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
        $this->app->singleton('plugins', function ($app) {
            $path = $app['config']->get('admin.plugins.path');

            return new PluginRepository($app, $path);
        });

        $this->app['plugins']->register();
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
