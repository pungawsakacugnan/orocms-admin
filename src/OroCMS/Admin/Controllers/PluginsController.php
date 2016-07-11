<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Http\Request;
use OroCMS\Admin\Facades\Plugin;
use OroCMS\Admin\Repositories\PluginRepository;

class PluginsController extends BaseController
{
    /**
     * @var OroCMS\Admin\Repositories\PluginRepository
     */ 
    protected $repository;

    /**
     * @param OroCMS\Admin\Repositories\PluginRepository $repository
     */
    function __construct(PluginRepository $repository) 
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
        $plugins = Plugin::all();

        return $this->view('plugins.index', compact('plugins'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $plugin_id
     *
     * @return Response
     */
    public function update(Request $request, $plugin_id=null)
    {
        // get redirect url
        $redirect = $this->redirect('modules.index');
        if ($redirect_url = $request->get('redirect')) {
            $redirect = redirect($redirect_url);
        }

        try {
            // pick from request
            if ($request->has('plugin')) {
                $plugin_id = $request->get('plugin');
            }

            // get plugin
            $plugin = $this->repository->findOrFail($plugin_id);

            // has action?
            if ($action = $request->get('action')) {
                if (preg_match('/\binstall|uninstall\b/', $action)) {
                    $plugin->$action();
                }
            }
            else {
                $plugin->toggle();
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'id' => $plugin->getName(),
                    'status' => $plugin->enabled
                ]);
            }

            return $redirect->withFlashMessage( trans('admin.plugin.message.' .($plugin->enabled?'enable':'disable'). '.success', [
                    'plugin' => $plugin->getTitle()
                ]))
                ->withFlashType('info');
        } 
        catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $redirect->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }
}