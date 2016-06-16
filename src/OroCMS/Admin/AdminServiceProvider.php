<?php
namespace OroCMS\Admin;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The providers package.
     *
     * @var array
     */
    protected $providers = [
        'Caffeinated\Menus\MenusServiceProvider',

        'OroCMS\Admin\Providers\SupportServiceProvider',
        'OroCMS\Admin\Providers\ConsoleServiceProvider',
        'OroCMS\Admin\Providers\MenuServiceProvider',
        'OroCMS\Admin\Providers\RepositoriesServiceProvider',

        'OroCMS\Admin\Providers\ModulesServiceProvider',
        'OroCMS\Admin\Providers\PluginsServiceProvider',
        'OroCMS\Admin\Providers\ThemesServiceProvider',
    ];

    /**
     * The facades package.
     *
     * @var array
     */
    protected $facades = [
        'Form' => 'Collective\Html\FormFacade',
        'Menu' => 'Caffeinated\Menus\Facades\Menu',
        'Module' => 'OroCMS\Admin\Facades\Module',
        'Plugin' => 'OroCMS\Admin\Facades\Plugin',
        'Theme' => 'OroCMS\Admin\Facades\Theme',
    ];

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $configPath = config_path('admin.php');
        $package_path = __DIR__ . '/../..';

        //
        // publishing
        //
        $this->publishes([
            $package_path . '/config/config.php' => $configPath,
        ], 'config');

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'admin');
        }

        $this->publishes([
            realpath($package_path) . '/../assets/' => base_path('resources/assets/admin/'),
        ], 'assets');

        $viewPath = base_path('resources/views/admin/');

        $this->publishes([
            $package_path . '/views/' => dirname($viewPath),
        ], 'views');

        $langPath = base_path('resources/lang/en/admin.php');

        $this->publishes([
            $package_path . '/lang/admin.php' => $langPath,
        ], 'lang');

        //
        // add view path
        //
        $this->loadViewsFrom([
            $viewPath . 'themes/' . config('admin.themes.cp.default_theme'),
            realpath($package_path) . '/views/admin/themes/' . config('admin.themes.cp.default_theme')
        ], 'admin');

        $this->loadViewsFrom([
            base_path('resources/views/themes/') . config('admin.themes.default_theme'),
            realpath($package_path) . '/views/themes/' . config('admin.themes.default_theme')
        ], 'theme');

        $this->app['view']->addLocation($viewPath . 'themes/' . config('admin.themes.cp.default_theme'));
        $this->app['view']->addLocation( realpath($package_path) . '/views/admin/themes/' . config('admin.themes.cp.default_theme') );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerProviders();
        $this->registerFacades();
        $this->registerRoutes();
    }

    /**
     * Register the providers.
     */
    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register the facades.
     */
    public function registerFacades()
    {
        AliasLoader::getInstance($this->facades);
    }

    /**
     * Register events.
     */
    public function registerRoutes()
    {
        $this->app->booted(function () {
            $this->app['events']->fire('admin::routes');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['admin'];
    }
}