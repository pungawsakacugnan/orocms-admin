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
     * Get plugin entity
     *
     * @return mixed
     */
    public function getEntity()
    {
        try {
            $model = new PluginEntity;
            if (is_null($model->connection)) {
                // use raw
                $name = $this->json()->get('name');
                $entity = $this->app['db']->table('plugins')->where('name', $name)->first();
            }
            else {
                $entity = $model->where('name', $this->json()->get('name'))->first();
            }
            
            return $entity;
        }
        catch(\Exception $e) {}
    }

    /**
     * Get plugin hooks.
     *
     * @param mixed $enabled 
     *
     * @return mixed
     */
    public function getHooks($enabled = true)
    {
        if ($entity = $this->getEntity()) {
            $query = $this->app['db']->table('plugin_hooks')
                ->where('plugin_id', $entity->id);

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
     * @param boolean $enable
     *
     * @return void
     */
    public function setEnabled($enable)
    {
        if ($entity = $this->getEntity()) {
            $this->app['db']->table('plugins')->where('name', $entity->name)
                ->update([
                    'published' => $enable
                ]);

            $this->app['db']->table('plugin_hooks')
                ->where('plugin_id', $entity->id)
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
        $this->setEnabled(0);
        $this->app['events']->fire('plugin.disabled', [$this]);
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
        if ($entity = $this->getEntity()) {
            foreach ($this->get('hooks') as $hook) {
                if (!(isset($hook['event']) or isset($hook['class']))) {
                    continue;
                }

                PluginHook::updateOrCreate([
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
     * Check if plugin is installed.
     *
     * @return boolean
     */
    public function installed()
    {
        return $this->getEntity();
    }

    /**
     * Install plugin.
     *
     * @return void
     */
    public function install()
    {
        $entity = $this->getEntity();

        if (is_null($entity)) {
            $entity = PluginEntity::create([
                'name' => $this->get('name')
            ]);

            $this->app['events']->fire('plugin.install', [$this]);
        }
    }

    /**
     * Remove plugin record.
     *
     * @return bool
     */
    public function uninstall()
    {
        if ($entity = $this->installed()) {
            $model = new PluginEntity();

            $model->where('id', $entity->id)
                ->delete();

            $this->app['events']->fire('plugin.uninstall', [$this]);

            return true;
        }

        return false;
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
            if ($entity = $this->getEntity()) {
                return $entity->published;
            }

            return false;
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
