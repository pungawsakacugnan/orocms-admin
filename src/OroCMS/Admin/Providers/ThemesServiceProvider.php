<?php
namespace OroCMS\Admin\Providers;

use OroCMS\Admin\Facades\Theme;
use OroCMS\Admin\Repositories\ThemeRepository;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ThemesServiceProvider extends ServiceProvider
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
        // set default theme
        $name = $this->app['config']->get('admin.themes.default_theme');
        if ($theme = Theme::find($name)) {
            view()->share('default_theme', $theme->getLayout());
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('themes', function ($app) {
            $path = $app['config']->get('admin.themes.path');

            return new ThemeRepository($app, $path);
        });
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
