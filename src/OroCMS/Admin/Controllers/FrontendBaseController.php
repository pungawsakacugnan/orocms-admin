<?php
namespace OroCMS\Admin\Controllers;

use OroCMS\Admin\Facades\Theme;
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
    protected $theme;

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
        $view = view($this->getViewPrefix() .'::' . $view, $data);

        // override frontend theme if set
        $theme = $this->getTheme();
        if (!is_null($theme)) {
            if ($theme = Theme::find($theme)) {
                view()->share('default_theme', $theme->getLayout());
            }
        }

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
     * Get route prefix
     *
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->route_prefix;
    }

    /**
     * Get view prefix
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
     * @return string
     */
    public function getTheme()
    {
        return is_null($this->theme) ? config('admin.themes.default_theme') : $this->theme;
    }
}
