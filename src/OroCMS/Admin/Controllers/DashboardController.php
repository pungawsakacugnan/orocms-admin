<?php
namespace OroCMS\Admin\Controllers;

class DashboardController extends BaseController
{
    /**
     * Admin dashboard.
     *
     * @return \Response
     */
    public function index()
    {
        return $this->view('index');
    }
}
