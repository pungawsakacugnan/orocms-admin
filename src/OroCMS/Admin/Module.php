<?php
namespace OroCMS\Admin;

use Artisan;
use OroCMS\Admin\Entities\Module as ModuleEntity;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class Module extends ServiceProvider
{
    /**
     * The laravel application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The module name.
     *
     * @var
     */
    protected $name;

    /**
     * The module path.
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
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Check if menu can be activated on admin.
     *
     * @return string
     */
    public function activateMenu()
    {
        return (int)$this->get('menu');
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
        $this->registerTranslation();

        $this->fireEvent('boot');
    }

    /**
     * Register module's translation.
     *
     * @return void
     */
    protected function registerTranslation()
    {
        $lowerName = $this->getLowerName();

        $langPath = base_path("resources/lang/{$lowerName}");
        
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $lowerName);
        }
    }

    /**
     * Get json contents.
     *
     * @return Json
     */
    public function json()
    {
        return new Json($this->getPath().'/module.json', $this->app['files']);
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
     * Register the module.
     */
    public function register()
    {
        $this->registerAliases();
        $this->registerProviders();
        $this->registerFiles();
        
        $this->fireEvent('register');
    }

    /**
     * Register the module event.
     *
     * @param string $event
     */
    protected function fireEvent($event)
    {
        $this->app['events']->fire(sprintf('modules.%s.'.$event, $this->getLowerName()), [$this]);
    }

    /**
     * Register the aliases from this module.
     */
    protected function registerAliases()
    {
        $loader = AliasLoader::getInstance();
        foreach ($this->get('aliases', []) as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }

    /**
     * Register the service providers from this module.
     */
    protected function registerProviders()
    {
        foreach ($this->get('providers', []) as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register the files from this module.
     */
    protected function registerFiles()
    {
        foreach ($this->get('files', []) as $file) {
            include $this->path.'/'.$file;
        }
    }

    /**
     * Get module entity
     *
     * @return mixed
     */
    public function getEntity()
    {
        try {
            $model = new ModuleEntity;
            if (is_null($model->connection)) {
                // use raw
                $name = $this->json()->get('name');
                $entity = $this->app['db']->table('modules')->where('name', $name)->first();
            }
            else {
                $entity = $model->where('name', $this->json()->get('name'))->first();
            }
            
            return $entity;
        }
        catch(\Exception $e) {}
    }


    /**
     * Set active state for current module.
     *
     * @param boolean $enable
     *
     * @return void
     */
    public function setEnabled($enable)
    {
        if ($entity = $this->getEntity()) {
            $this->app['db']->table('modules')->where('name', $entity->name)
                ->update([
                    'published' => $enable
                ]);
        }
    }

    /**
     * Disable the current module.
     *
     * @return bool
     */
    public function disable()
    {
        $this->setEnabled(0);
        $this->app['events']->fire('module.disabled', [$this]);
    }

    /**
     * Enable the current module.
     */
    public function enable()
    {
        $this->setEnabled(1);
        $this->app['events']->fire('module.enabled', [$this]);
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
     * Check if module is installed.
     *
     * @return boolean
     */
    public function installed()
    {
        return $this->getEntity();
    }

    /**
     * Install module.
     *
     * @return void
     */
    public function install()
    {
        $entity = $this->getEntity();

        if (is_null($entity)) {
            $entity = ModuleEntity::create([
                'name' => $this->get('name')
            ]);

            // run migration
            $this->runMigration();

            $this->app['events']->fire('module.install', [$this]);
        }
    }

    /**
     * Remove module record.
     *
     * @return bool
     */
    public function uninstall()
    {
        if ($entity = $this->installed()) {
            $model = new ModuleEntity();

            $model->where('id', $entity->id)
                ->delete();

            // run migration
            $this->runMigration('rollback');

            $this->app['events']->fire('module.uninstall', [$this]);

            return true;
        }

        return false;
    }

    /**
     * Delete the current module.
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
     * Run a migration job.
     *
     * @param string $action
     *
     * @return boolean
     */
    private function runMigration($action = null)
    {
        // run migration
        $action = 'admin:' . ($action ?: 'migrate');
        Artisan::call($action, [
            '--module' => $this->get('name')
        ]);
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
