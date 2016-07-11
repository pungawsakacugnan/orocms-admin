<?php
namespace OroCMS\Admin\Controllers;

use OroCMS\Admin\Repositories\SettingsRepository;

class SettingsController extends BaseController
{
    /**
     * @var OroCMS\Admin\Entities\Settings
     */
    protected $settings;

    /**
     * @var OroCMS\Admin\Repositories\SettingsRepository
     */ 
    protected $repository;

    /**
     * @param OroCMS\Admin\Repositories\SettingsRepository $repository
     */
    function __construct(SettingsRepository $repository) 
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $settings = $this->repository->settings();

        return $this->view('settings.index', compact('settings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update()
    {
        try {
            $this->repository->update();

            return $this->redirect('settings.index')
                ->withFlashMessage( trans('admin.settings.message.settings_updated') )
                ->withFlashType('info');
        }
        catch (ModelNotFoundException $e) {
            return $this->redirect('settings.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }
}