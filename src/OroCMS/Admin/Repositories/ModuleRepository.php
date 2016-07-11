<?php
namespace OroCMS\Admin\Repositories;

use Countable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use OroCMS\Admin\Json;
use OroCMS\Admin\Collection;
use OroCMS\Admin\Module;
use OroCMS\Admin\Entities\Module as ModuleEntity;
use OroCMS\Admin\Contracts\PluggableRepositoryInterface;
use OroCMS\Admin\Exceptions\ModuleNotFoundException;

class ModuleRepository implements PluggableRepositoryInterface, Countable
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The module path.
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
    * Return module model.
    *
    * @return \OroCMS\Admin\Entities\Module
    */
    public function getModel()
    {
        return new ModuleEntity;
    }

    /**
     * Get scanned modules paths.
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
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $modules = [];

        foreach ($paths as $key=>$path) {
            $manifests = $this->app['files']->glob("{$path}/module.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $lowerName = strtolower($name);

                $modules[$name] = new Module($this->app, $lowerName, dirname($manifest));
            }
        }

        return $modules;
    }

    /**
     * Get all modules.
     *
     * @return array
     */
    public function all()
    {
        static $modules;

        if (is_null($modules)) {
            if (!$this->config('cache.enabled')) {
                $modules = $this->scan();
            }
            else {
                $modules = $this->formatCached($this->getCached());
            }

            // map publishing with current entity
            foreach ($modules as $module) {
                if ($entity = $module->getEntity()) {
                    $module->setEnabled($entity->published);
                }
            }
        }

        return $modules;
    }

    /**
     * Get all modules as collection instance.
     *
     * @return Collection
     */
    public function toCollection()
    {
        return new Collection($this->scan());
    }

    /**
     * Get modules by status.
     *
     * @param $status
     *
     * @return array
     */
    public function getByStatus($status)
    {
        $modules = [];

        foreach ($this->all() as $name=>$module) {
            if ($module->enabled and $status) {
                $modules[$name] = $module;
            }
        }

        return $modules;
    }

    /**
     * Determine whether the given module exist.
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
     * Get list of enabled modules.
     *
     * @return array
     */
    public function enabled()
    {
        return $this->getByStatus(1);
    }

    /**
     * Get list of disabled modules.
     *
     * @return array
     */
    public function disabled()
    {
        return $this->getByStatus(0);
    }

    /**
     * Get count from all modules.
     *
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * Get all modules in order of priority.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getPrioritized($direction = 'asc') {}

    /**
     * Get all ordered modules.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getOrdered($direction = 'asc')
    {
        $modules = $this->enabled();

        uasort($modules, function ($a, $b) use ($direction) {
            if ($a->order == $b->order) {
                return 0;
            }

            if ($direction == 'desc') {
                return $a->order < $b->order ? 1 : -1;
            }

            return $a->order > $b->order ? 1 : -1;
        });

        return $modules;
    }

    /**
     * Get a module path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config('paths.modules');
    }

    /**
     * Register the modules.
     */
    public function register()
    {
        foreach ($this->getOrdered() as $module) {
            $module->register();
        }
    }

    /**
     * Boot the modules.
     */
    public function boot()
    {
        foreach ($this->getOrdered() as $module) {
            $module->boot();
        }
    }

    /**
     * Find a specific module.
     *
     * @param $name
     */
    public function find($name)
    {
        foreach ($this->all() as $module) {
            if ($module->getLowerName() == strtolower($name)) {
                return $module;
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
     * Find a specific module, if there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return Module
     *
     * @throws ModuleNotFoundException
     */
    public function findOrFail($name)
    {
        if ($module = $this->find($name)) {
            return $module;
        }

        throw new ModuleNotFoundException("Module [{$name}] does not exist!");
    }

    /**
     * Get all modules as laravel collection instance.
     *
     * @return Collection
     */
    public function collections()
    {
        return new Collection($this->enabled());
    }

    /**
     * Get module path for a specific module.
     *
     * @param $module
     *
     * @return string
     */
    public function getModulePath($module)
    {
        try {
            return $this->findOrFail($module)->getPath().'/';
        }
        catch (ModuleNotFoundException $e) {
            return $this->getPath().'/'.Str::studly($module).'/';
        }
    }

    /**
     * Get asset path for a specific module.
     *
     * @param $module
     *
     * @return string
     */
    public function assetPath($module)
    {
        return $this->config('paths.assets').'/'.$module;
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
        return $this->app['config']->get('modules.'.$key);
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles()
    {
        return $this->app['files'];
    }

    /**
     * Get module assets path.
     *
     * @return string
     */
    public function getAssetsPath()
    {
        return $this->config('paths.assets');
    }

    /**
     * Get asset url from a specific module.
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
     * Enabling a specific module.
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
     * Disabling a specific module.
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
     * Delete a specific module.
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
