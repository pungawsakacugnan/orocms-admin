<?php
namespace OroCMS\Admin\Providers;

use Blade;
use OroCMS\Admin\Facades\Theme;
use OroCMS\Admin\Services\ThemeFileViewFinder;
use OroCMS\Admin\Repositories\ThemeRepository;
use OroCMS\Admin\Facades\Settings as AdminSettings;
use Illuminate\Http\Request;
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
    public function boot(Request $request)
    {
        // set default theme
        $base_path = config('admin.themes.path', base_path('resources/views/themes'));
        $theme_path = AdminSettings::settings('site_theme') ?: config('admin.themes.default_theme');
        $this->loadViewsFrom([
            $base_path .'/'. $theme_path
        ], 'theme');

        // prioritized our theme path
        // if not on cp
        if ($request->segment(1) != 'admin') {
            $this->app['view']->prependLocation($base_path .'/'. $theme_path);
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
         * Extend/add blade directives
         */
        Blade::extend(function($value) {
            return preg_replace('|@define(.+);|sU', '<?php ${1}; ?>', $value);
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
