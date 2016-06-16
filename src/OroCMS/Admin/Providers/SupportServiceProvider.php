<?php
namespace OroCMS\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * The service provider classes array.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        require __DIR__.'/../composers.php';
        require __DIR__.'/../listeners.php';
        require __DIR__.'/../routes.php';
        require __DIR__.'/../helpers.php';
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerProviders();
    }

    /**
     * Register service providers.
     */
    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
