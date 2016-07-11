<?php
namespace OroCMS\Admin\Repositories;

use Countable;
use Illuminate\Support\Str;
use Illuminate\Foundation\Application;
use OroCMS\Admin\Json;
use OroCMS\Admin\Collection;
use OroCMS\Admin\Plugin;
use OroCMS\Admin\Entities\Plugin as PluginEntity;
use OroCMS\Admin\Contracts\PluggableRepositoryInterface;
use OroCMS\Admin\Exceptions\PluginNotFoundException;

class PluginRepository implements PluggableRepositoryInterface, Countable
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The plugin path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * The constructor.
     *
     * @param Application $app
     * @param string|null $path
     */
    public function __construct(Application $app, $path = null)
    {
        $this->app = $app;
        $this->path = $path;
    }

    /**
    * Return plugin model.
    *
    * @return \OroCMS\Admin\Entities\Plugin
    */
    public function getModel()
    {
        return new PluginEntity;
    }

    /**
     * Get scanned plugins paths.
     *
     * @return array
     */
    public function getScanPaths()
    {
        $paths = $this->paths;

        if ($path = $this->getPath()) {
            $paths[] = $path . '/*';
        }

        if ($this->config('scan.enabled')) {
            $paths = array_merge($paths, $this->config('scan.paths'));
        }

        return $paths;
    }

    /**
     * Get & scan all plugins.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $plugins = [];

        foreach ($paths as $key=>$path) {
            $manifests = $this->app['files']->glob("{$path}/plugin.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $lowerName = strtolower($name);

                $plugins[$name] = new Plugin($this->app, $lowerName, dirname($manifest));
            }
        }

        return $plugins;
    }

    /**
     * Get all plugins.
     *
     * @return array
     */
    public function all()
    {
        static $plugins;

        if (is_null($plugins)) {
            if (!$this->config('cache.enabled')) {
                $plugins = $this->scan();
            }
            else {
                $plugins = $this->formatCached($this->getCached());
            }

            // map publishing with current entity
            foreach ($plugins as $plugin) {
                if ($entity = $plugin->getEntity()) {
                    $plugin->setEnabled($entity->published);
                }
            }
        }

        return $plugins;
    }

    /**
     * Get all plugins as collection instance.
     *
     * @return Collection
     */
    public function toCollection()
    {
        return new Collection($this->scan());
    }

    /**
     * Get plugins by status.
     *
     * @param $status
     *
     * @return array
     */
    public function getByStatus($status)
    {
        $plugins = [];

        foreach ($this->all() as $name=>$plugin) {
            if ($plugin->enabled and $status) {
                $plugins[$name] = $plugin;
            }
        }

        return $plugins;
    }

    /**
     * Determine whether the given plugin exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->all());
    }

    /**
     * Get list of enabled plugins.
     *
     * @return array
     */
    public function enabled()
    {
        return $this->getByStatus(1);
    }

    /**
     * Get list of disabled plugins.
     *
     * @return array
     */
    public function disabled()
    {
        return $this->getByStatus(0);
    }

    /**
     * Get count from all plugins.
     *
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * Get all plugins in order of priority.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getPrioritized($direction = 'asc')
    {
        $plugins = $this->enabled();

        uasort($plugins, function ($a, $b) use ($direction) {
            if ($a->order == $b->order) {
                return 0;
            }

            if ($direction == 'desc') {
                return $a->order < $b->order ? 1 : -1;
            }

            return $a->order > $b->order ? 1 : -1;
        });

        return $plugins;
    }

    /**
     * Get a plugin path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config('paths.plugins');
    }

    /**
     * Register the plugins.
     */
    public function register()
    {
        foreach ($this->getPrioritized() as $plugin) {
            $plugin->register();
        }
    }

    /**
     * Boot the plugins.
     */
    public function boot()
    {
        foreach ($this->getPrioritized() as $plugin) {
            $plugin->boot();
        }
    }

    /**
     * Find a specific plugin.
     *
     * @param $name
     */
    public function find($name)
    {
        foreach ($this->all() as $plugin) {
            if ($plugin->getLowerName() == strtolower($name)) {
                return $plugin;
            }
        }

        return;
    }

    /**
     * Alternative for "find" method.
     *
     * @param $name
     */
    public function get($name)
    {
        return $this->find($name);
    }

    /**
     * Find a specific plugin, if there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return Plugin
     *
     * @throws PluginNotFoundException
     */
    public function findOrFail($name)
    {
        if ($plugin = $this->find($name)) {
            return $plugin;
        }

        throw new PluginNotFoundException("Plugin [{$name}] does not exist!");
    }

    /**
     * Get all plugins as laravel collection instance.
     *
     * @return Collection
     */
    public function collections()
    {
        return new Collection($this->enabled());
    }

    /**
     * Get plugin path for a specific plugin.
     *
     * @param $plugin
     *
     * @return string
     */
    public function getPluginPath($plugin)
    {
        try {
            return $this->findOrFail($plugin)->getPath().'/';
        }
        catch (PluginNotFoundException $e) {
            return $this->getPath().'/'.Str::studly($plugin).'/';
        }
    }

    /**
     * Get asset path for a specific plugin.
     *
     * @param $plugin
     *
     * @return string
     */
    public function assetPath($plugin)
    {
        return $this->config('paths.assets').'/'.$plugin;
    }

    /**
     * Get a specific config data from a configuration file.
     *
     * @param $key
     *
     * @return mixed
     */
    public function config($key)
    {
        return $this->app['config']->get('plugins.'.$key);
    }

    /**
     * Get plugin assets path.
     *
     * @return string
     */
    public function getAssetsPath()
    {
        return $this->config('paths.assets');
    }

    /**
     * Get asset url from a specific plugin.
     *
     * @param string $asset
     * @param bool   $secure
     *
     * @return string
     */
    public function asset($asset)
    {
        list($name, $url) = explode(':', $asset);

        $baseUrl = str_replace(public_path(), '', $this->getAssetsPath());

        $url = $this->app['url']->asset($baseUrl."/{$name}/".$url);

        return str_replace(['http://', 'https://'], '//', $url);
    }

    /**
     * Enabling a specific plugin.
     *
     * @param string $name
     *
     * @return bool
     */
    public function enable($name)
    {
        return $this->findOrFail($name)->enable();
    }

    /**
     * Disabling a specific plugin.
     *
     * @param string $name
     *
     * @return bool
     */
    public function disable($name)
    {
        return $this->findOrFail($name)->disable();
    }

    /**
     * Delete a specific plugin.
     *
     * @param string $name
     *
     * @return bool
     */
    public function delete($name)
    {
        return $this->findOrFail($name)->delete();
    }
}
