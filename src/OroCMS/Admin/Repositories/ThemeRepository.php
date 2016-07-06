<?php
namespace OroCMS\Admin\Repositories;

use Countable;
use Illuminate\Support\Str;
use Illuminate\Foundation\Application;
use OroCMS\Admin\Json;
use OroCMS\Admin\Theme;
use OroCMS\Admin\Collection;

class ThemeRepository implements Countable
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The theme path.
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
     * Add other theme location.
     *
     * @param string $path
     *
     * @return $this
     */
    public function addLocation($path)
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Alternative method for "addPath".
     *
     * @param string $path
     *
     * @return $this
     */
    public function addPath($path)
    {
        return $this->addLocation($path);
    }

    /**
     * Get all additional paths.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Get scanned themes paths.
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
     * Get & scan all themes.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $themes = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->app['files']->glob("{$path}/theme.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');
                $base_path = dirname(realpath($manifest));

                $themes[$name] = new Theme($this->app, strtolower($name), $base_path);
            }
        }

        return $themes;
    }

    /**
     * Get all themes.
     *
     * @return array
     */
    public function all()
    {
        if (!$this->config('cache.enabled')) {
            $themes = $this->scan();
        }
        else {
            $themes = $this->formatCached($this->getCached());
        }

        return $themes;
    }

    /**
     * Get all themes as collection instance.
     *
     * @return Collection
     */
    public function toCollection()
    {
        return new Collection($this->scan());
    }

    /**
     * Get themes by status.
     *
     * @param $status
     *
     * @return array
     */
    public function getByStatus($status)
    {
        $themes = [];

        foreach ($this->all() as $name => $theme) {
            if ($theme->enabled and $status) {
                $themes[$name] = $theme;
            }
        }

        return $themes;
    }

    /**
     * Determine whether the given theme exist.
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
     * Get list of enabled themes.
     *
     * @return array
     */
    public function enabled()
    {
        return $this->getByStatus(1);
    }

    /**
     * Get list of disabled themes.
     *
     * @return array
     */
    public function disabled()
    {
        return $this->getByStatus(0);
    }

    /**
     * Get count from all themes.
     *
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * Get all themes in order of priority.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getPrioritized($direction = 'asc')
    {
        $themes = $this->enabled();

        uasort($themes, function ($a, $b) use ($direction) {
            if ($a->order == $b->order) {
                return 0;
            }

            if ($direction == 'desc') {
                return $a->order < $b->order ? 1 : -1;
            }

            return $a->order > $b->order ? 1 : -1;
        });

        return $themes;
    }

    /**
     * Get a theme path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config('paths.themes');
    }

    /**
     * Register the themes.
     */
    public function register()
    {
        foreach ($this->getPrioritized() as $theme) {
            $theme->register();
        }
    }

    /**
     * Boot the themes.
     */
    public function boot()
    {
        foreach ($this->getPrioritized() as $theme) {
            $theme->boot();
        }
    }

    /**
     * Find a specific theme.
     *
     * @param $name
     */
    public function find($name)
    {
        foreach ($this->all() as $theme) {
            if ($theme->getLowerName() == strtolower($name)) {
                return $theme;
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
     * Find a specific theme, if there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return Theme
     *
     * @throws ThemeNotFoundException
     */
    public function findOrFail($name)
    {
        if (!is_null($theme = $this->find($name))) {
            return $theme;
        }

        throw new ThemeNotFoundException("Theme [{$name}] does not exist!");
    }

    /**
     * Get all themes as laravel collection instance.
     *
     * @return Collection
     */
    public function collections()
    {
        return new Collection($this->enabled());
    }

    /**
     * Get theme path for a specific theme.
     *
     * @param $theme
     *
     * @return string
     */
    public function getThemePath($theme)
    {
        try {
            $theme = $this->findOrFail($theme);
            if ($path = $theme->getPath()) {
                return $path . '/';
            }
        }
        catch (ThemeNotFoundException $e) {
            return $this->getPath() .'/'. Str::studly($theme) . '/';
        }
    }

    /**
     * Get asset path for a specific theme.
     *
     * @param $theme
     *
     * @return string
     */
    public function assetPath($theme)
    {
        return $this->config('paths.assets').'/'.$theme;
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
        return $this->app['config']->get('themes.'.$key);
    }

    /**
     * Get theme assets path.
     *
     * @return string
     */
    public function getAssetsPath()
    {
        return $this->config('paths.assets');
    }

    /**
     * Get asset url from a specific theme.
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
     * Enabling a specific theme.
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
     * Disabling a specific theme.
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
     * Delete a specific theme.
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
