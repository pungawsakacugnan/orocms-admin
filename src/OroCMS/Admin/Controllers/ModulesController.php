<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Http\Request;
use OroCMS\Admin\Facades\Module;
use OroCMS\Admin\Repositories\ModuleRepository;

class ModulesController extends BaseController
{
    /**
     * @var OroCMS\Admin\Repositories\ModuleRepository
     */ 
    protected $repository;

    /**
     * @param OroCMS\Admin\Repositories\ModuleRepository $repository
     */
    function __construct(ModuleRepository $repository) 
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
        $modules = Module::all();

        return $this->view('modules.index', compact('modules'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $module_id
     *
     * @return Response
     */
    public function update(Request $request, $module_id=null)
    {
        // get redirect url
        $redirect = $this->redirect('modules.index');
        if ($redirect_url = $request->get('redirect')) {
            $redirect = redirect($redirect_url);
        }

        try {
            // pick from request
            if ($request->has('module')) {
                $module_id = $request->get('module');
            }

            // get module
            $module = $this->repository->findOrFail($module_id);

            // has action?
            if ($action = $request->get('action')) {
                if (preg_match('/\binstall|uninstall\b/', $action)) {
                    $module->$action();
                }
            }
            else {
                $module->toggle();
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'id' => $module->getName(),
                    'status' => $module->enabled
                ]);
            }

            return $redirect->withFlashMessage( trans('admin.module.message.' .($disabled?'enable':'disable'). '.success', [
                    'module' => $module->getTitle()
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