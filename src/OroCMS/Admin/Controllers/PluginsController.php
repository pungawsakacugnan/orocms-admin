<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Http\Request;
use OroCMS\Admin\Facades\Plugin;
use OroCMS\Admin\Entities\Plugin as PluginEntity;

class PluginsController extends BaseController
{
    public function index()
    {
        $plugins = Plugin::all();

        return $this->view('plugins.index', compact('plugins'));
    }

    public function update(Request $request, $plugin_id=null)
    {
        try {
            // pick from request
            if ($request->has('plugin')) {
                $plugin_id = $request->get('plugin');
            }

            // get plugin
            $plugin = Plugin::findOrFail($plugin_id);
            $plugin->toggle();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'id' => $plugin->getName(),
                    'status' => $plugin->enabled
                ]);
            }

            return $this->redirect('plugins.index')
                ->withFlashMessage( trans('admin.plugin.message.' .($plugin->enabled?'enable':'disable'). '.success', [
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

            return $this->redirect('plugins.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }
}