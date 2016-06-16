<?php
namespace OroCMS\Admin\Providers;

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
        'User',
        'Role'
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
        $this->app->bind(
            'OroCMS\Admin\Repositories\Users\UserRepository'
        );
    }

    protected function bindRoleRepository()
    {
        $this->app->bind(
            'OroCMS\Admin\Repositories\Roles\RoleRepository'
        );
    }
}
