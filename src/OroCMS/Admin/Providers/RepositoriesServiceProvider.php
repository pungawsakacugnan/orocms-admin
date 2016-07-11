<?php
namespace OroCMS\Admin\Providers;

use OroCMS\Admin\Repositories\SettingsRepository;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $entities = [
        'Settings',
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        foreach ($this->entities as $entity) {
            $this->{'bind'.$entity.'Repository'}();
        }
    }

    protected function bindUserRepository()
    {
    }

    protected function bindSettingsRepository()
    {
        $this->app->bind('settings', function($app) {
            return new SettingsRepository;
        });
    }
}
