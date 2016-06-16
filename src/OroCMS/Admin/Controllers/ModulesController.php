<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Http\Request;
use OroCMS\Admin\Facades\Module;

class ModulesController extends BaseController
{
    public function index()
    {
        $modules = Module::all();

        return $this->view('modules.index', compact('modules'));
    }

    public function update(Request $request, $module_id=null)
    {
        try {
            // pick from request
            if ($request->has('module')) {
                $module_id = $request->get('module');
            }

            // get module
            $module = Module::findOrFail($module_id);
            $disabled = $module->isStatus(0);

            // toggle
            $disabled ? $module->enable() : $module->disable();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'id' => $module->getName(),
                    'status' => $disabled
                ]);
            }

            return $this->redirect('modules.index')
                ->withFlashMessage( trans('admin.module.message.' .($disabled?'enable':'disable'). '.success', [
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

            return $this->redirect('modules.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }
}