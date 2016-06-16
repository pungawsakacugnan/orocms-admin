<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Routing\Controller;

class BaseController extends Controller
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
     * Render view.
     *
     * @param $view
     * @param array $data
     *
     * @return mixed
     */
    public function view($view, $data = [])
    {
        return view($this->getViewPrefix() .'::' . $view, $data);
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
        return $this->route_prefix ?: 'admin';
    }

    /**
     * Get view prefix
     *
     * @return string
     */
    public function getViewPrefix()
    {
        return $this->view_prefix ?: 'admin';
    }
}
