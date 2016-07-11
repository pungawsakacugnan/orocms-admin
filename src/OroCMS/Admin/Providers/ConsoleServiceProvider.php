<?php
namespace OroCMS\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The available command shortname.
     *
     * @var array
     */
    protected $commands = [
        'Seed',
        'Refresh',
        'Install',
        'Migration',
        'Rollback',
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        foreach ($this->commands as $command) {
            $this->commands('OroCMS\\Admin\\Console\\'.$command.'Command');
        }
    }

    /**
     * @return array
     */
    public function provides()
    {
        $provides = [];

        foreach ($this->commands as $command) {
            $provides[] = 'OroCMS\\Admin\\Console\\'.$command.'Command';
        }

        return $provides;
    }
}
