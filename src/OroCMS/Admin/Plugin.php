<?php
namespace OroCMS\Admin;

use OroCMS\Admin\Entities\Plugin as PluginEntity;
use OroCMS\Admin\Entities\PluginHook;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class Plugin extends ServiceProvider
{
    /**
     * The laravel application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The plugin name.
     *
     * @var
     */
    protected $name;

    /**
     * The plugin path.
     *
     * @var string
     */
    protected $path;

    /**
     * Plugin publish status.
     *
     * @var
     */
    protected $enabled;

    /**
     * The constructor.
     *
     * @param Application $app
     * @param $name
     * @param $path
     */
    public function __construct(Application $app, $name, $path)
    {
        $this->app = $app;
        $this->name = $name;
        $this->path = realpath($path);
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        $title = $this->get('title');
        return $title ?: $this->getStudlyName();
    }

    /**
     * Get name in lower case.
     *
     * @return string
     */
    public function getLowerName()
    {
        return strtolower($this->name);
    }

    /**
     * Get name in studly case.
     *
     * @return string
     */
    public function getStudlyName()
    {
        return Str::studly($this->name);
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->get('author');
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->get('alias');
    }

    /**
     * Get priority.
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->get('priority');
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->fireEvent('boot');
    }

    /**
     * Get json contents.
     *
     * @return Json
     */
    public function json()
    {
        return new Json($this->getPath().'/plugin.json', $this->app['files']);
    }

    /**
     * Get a specific data from json file by given the key.
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->json()->get($key, $default);
    }

    /**
     * Register the plugin.
     */
    public function register()
    {
        $this->registerAliases();
        $this->registerProviders();
        $this->registerFiles();
        
        $this->fireEvent('register');
    }

    /**
     * Register the plugin event.
     *
     * @param string $event
     */
    protected function fireEvent($event)
    {
        $this->app['events']->fire(sprintf('plugins.%s.'.$event, $this->getLowerName()), [$this]);
    }

    /**
     * Register the aliases from this plugin.
     */
    protected function registerAliases()
    {
        $loader = AliasLoader::getInstance();
        foreach ($this->get('aliases', []) as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }

    /**
     * Register the service providers from this plugin.
     */
    protected function registerProviders()
    {
        foreach ($this->get('providers', []) as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register the files from this plugin.
     */
    protected function registerFiles()
    {
        foreach ($this->get('files', []) as $file) {
            include $this->path.'/'.$file;
        }
    }

    /**
     * Get plugin repository
     *
     * @param $repository mixed
     *
     * @return mixed
     */
    public function getRepository()
    {
        $model = new PluginEntity;
        if (is_null($model->connection)) {
            // use raw
            $name = $this->json()->get('name');

            $repository = $this->app['db']->table('plugins')->where('name', $name)->first();
        }
        else {
            $repository = $model->where('name', $this->json()->get('name'))->first();
        }

        return $repository;
    }

    /**
     * Get plugin hooks
     *
     * @param $repository mixed
     *
     * @return mixed
     */
    public function getHooks($enabled = true)
    {
        if ($repository = $this->getRepository()) {
            $query = $this->app['db']->table('plugin_hooks')
                ->where('plugin_id', $repository->id);

            if ($enabled) {
                $query = $query->where('published', 1);
            }

            $hooks = $query->get();
        }

        return $hooks;
    }

    /**
     * Set active state for current plugin.
     *
     * @param $enable
     *
     * @return void
     */
    public function setEnabled($enable)
    {
        $this->enabled = $enable;

        if ($repository = $this->getRepository()) {
            $this->app['db']->table('plugins')->where('name', $repository->name)
                ->update([
                    'published' => $enable
                ]);

            $this->app['db']->table('plugin_hooks')
                ->where('plugin_id', $repository->id)
                ->update([
                    'published' => $enable
                ]);
        }
    }

    /**
     * Disable the current plugin.
     *
     * @return void
     */
    public function disable()
    {
        $this->app['events']->fire('plugin.disabling', [$this]);

        $this->setEnabled(0);
    }

    /**
     * Enable the current plugin.
     *
     * @return void
     */
    public function enable()
    {
        $this->setEnabled(1);

        // enable first-time
        $repository = $this->getRepository();
        if (is_null($repository)) {
            $entity = PluginEntity::create([
                'name' => $this->get('name'),
                'published' => true
            ]);

            foreach ($this->get('hooks') as $hook) {
                if (!(isset($hook['event']) or isset($hook['class']))) {
                    continue;
                }

                PluginHook::create([
                    'plugin_id' => $entity->id,
                    'event' => $hook['event'],
                    'class' => $hook['class'],
                    'published' => true
                ]);
            }
        }

        $this->app['events']->fire('plugin.enabled', [$this]);
    }

    /**
     * Enable the current plugin.
     *
     * @return void
     */
    public function toggle()
    {
        $this->enabled ? $this->disable() : $this->enable();
    }

    /**
     * Delete the current plugin.
     *
     * @return bool
     */
    public function delete()
    {
        return $this->json()->getFilesystem()->deleteDirectory($this->getPath(), true);
    }

    /**
     * Get extra path.
     *
     * @param $path
     *
     * @return string
     */
    public function getExtraPath($path)
    {
        return $this->getPath().'/'.$path;
    }

    /**
     * Handle call to __get method.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($key == 'enabled') {
            return $this->enabled;
        }

        return $this->get($key);
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getStudlyName();
    }
}
