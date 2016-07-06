<?php
namespace OroCMS\Admin\Providers;

use Blade;
use OroCMS\Admin\Facades\Theme;
use OroCMS\Admin\Services\ThemeFileViewFinder;
use OroCMS\Admin\Repositories\ThemeRepository;
use Illuminate\Foundation\AliasLoader;
use Illuminate\View\FileViewFinder;
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
            view()->share('default_theme', $theme->getName());
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

        /**
         * Add blade directives/extensions
         */

        /**
         * @directive: define
         * @usage: @define x = 1 // assigns 1 to x
         * http://stackoverflow.com/questions/13002626/laravels-blade-how-can-i-set-variables-in-a-template
         */
        Blade::extend(function($value) {
            return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
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
