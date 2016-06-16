<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Http\Request;
use OroCMS\Admin\Entities\LogReader;

class LogsController extends BaseController
{
    /**
     * @var OroCMS\Admin\Entities\LogReader
     */ 
    protected $logs   = null;


    function __construct()
    {
        $this->logs   = new LogReader();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->logs->all());
        }

        return $this->view('logs');
    }
}
