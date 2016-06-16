<?php
namespace OroCMS\Admin;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class Theme extends ServiceProvider
{
    /**
     * The laravel application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The theme name.
     *
     * @var
     */
    protected $name;

    /**
     * The theme layout path.
     *
     * @var string
     */
    protected $layout;

    /**
     * The constructor.
     *
     * @param Application $app
     * @param $name
     * @param $path
     */
    public function __construct(Application $app, $name, $layout)
    {
        $this->app = $app;
        $this->name = $name;
        $this->layout = $layout;
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
     * Get layout name.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
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
        return new Json($this->getPath().'/theme.json', $this->app['files']);
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
     * Register the theme.
     */
    public function register()
    {
        $this->fireEvent('register');
    }

    /**
     * Register the theme event.
     *
     * @param string $event
     */
    protected function fireEvent($event)
    {
        $this->app['events']->fire(sprintf('themes.%s.'.$event, $this->getLowerName()), [$this]);
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
