<?php
namespace OroCMS\Admin\Controllers;

use OroCMS\Admin\Facades\Theme;
use OroCMS\Admin\Facades\Settings as AdminSettings;
use Illuminate\Routing\Controller;

class FrontendBaseController extends Controller
{
    /**
     * @var string
     */
    protected $route_prefix;

    /**
     * @var string
     */
    protected $view_prefix;

    /**
     * @var string
     */
    private $theme;

    /**
     * Render view.
     *
     * @param $view
     * @param array $data
     *
     * @return mixed
     */
    public function view($view, $data = [])
    {
        $prefix = $this->getViewPrefix() ?: 'theme';

        // override frontend theme if set
        if ($theme = Theme::find($this->getTheme())) {
            view()->prependNamespace('theme', [
                $theme->getPath()
            ]);
        }

        // set view
        $view = view($prefix .'::'. $view, $data);

        return $view;
    }

    /**
     * Redirect to a route.
     *
     * @param $route
     * @param array $parameters
     * @param int   $status
     * @param array $headers
     *
     * @return mixed
     */
    public function redirect($route, $parameters = [], $status = 302, $headers = [])
    {
        return redirect(route($this->getRoutePrefix() .'.'. $route, $parameters, $status, $headers));
    }

    /**
     * Get route prefix.
     *
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->route_prefix;
    }

    /**
     * Get view prefix.
     *
     * @return string
     */
    public function getViewPrefix()
    {
        return $this->view_prefix;
    }

    /**
     * Get theme
     *
     * @return string.
     */
    public function getTheme()
    {
        $settings_theme = AdminSettings::settings('site_theme') ?: config('admin.themes.default_theme');

        return is_null($this->theme) ? $settings_theme : $this->theme;
    }

    /**
     * Set current theme.
     *
     * @return string
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }
}
