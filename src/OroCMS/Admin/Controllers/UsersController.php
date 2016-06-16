<?php
namespace OroCMS\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use OroCMS\Admin\Repositories\UserRepository;
use OroCMS\Admin\Validation\Users\Create;
use OroCMS\Admin\Validation\Users\Update;

class UsersController extends BaseController
{
    /**
     * @var OroCMS\Admin\Entities\User
     */ 
    protected $users;

    /**
     * @param OroCMS\Admin\Repositories\UserRepository $repository
     */
    function __construct(UserRepository $repository) 
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->repository->getAll();

            return response()->json($users);
        }

        return $this->view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return $this->view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Create $request)
    {
        $data = $request->all();

        $user = $this->repository->create($data);
        $request->has('role') and $user->addSingleRole($request->get('role'));

        return $this->redirect('users.index')
            ->withFlashMessage( trans('admin.user.message.create.success') )->withFlashType('info');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function show($id)
    {
        try {
            $user = $this->repository->findById($id);

            return $this->view('users.show', compact('user'));
        } 
        catch (ModelNotFoundException $e) {
            return $this->redirect('users.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function edit($id)
    {
        try {
            $user = $this->repository->findById($id);
            $role = $user->roles->lists('id')->toArray();

            return $this->view('users.edit', compact('user', 'role'));
        } 
        catch (ModelNotFoundException $e) {
            return $this->redirect('users.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            // get user
            $user = $this->repository->findById($id);

            if ($request->ajax()) {
                $success = false;
                $message = null;

                if ($request->has('restore')) {
                    // restore
                    $user->restore();

                    $success = true;
                    $message = trans('admin.user.message.restored');
                }

                return response()->json(compact('success', 'message'));
            }

            $data = !$request->has('password') ? $request->except('password') : $request->all();

            $user->update($data);
            $user->roles()->sync((array)$request->get('role'));

            return $this->redirect('users.index')
                ->withFlashMessage( trans('admin.user.message.update.success') )->withFlashType('info');
        } 
        catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $this->redirect('users.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function restore(Request $request, $id = null)
    {
        try {
            // get selected ids
            $cids = $request->get('id') ?: [$id];

            foreach ($cids as $id) {
                $this->repository->restore($id);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('admin.user.message.restored')
                ]);
            }

            return $this->redirect('users.index')
                ->withFlashMessage( trans('admin.user.message.restored') )->withFlashType('info');
        } 
        catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $this->redirect('users.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function destroy(Request $request, $id=null)
    {
        try {
            // get selected ids
            $cids = $request->get('id') ?: [$id];

            // force delete?
            $force_delete = $request->has('force_delete') and (int)$request->has('force_delete');

            foreach ($cids as $id) {
                $this->repository->delete($id, $force_delete);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('admin.user.message.delete.' . ($force_delete ? 'success' : 'marked'))
                ]);
            }

            return $this->redirect('users.index');
        } 
        catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $this->redirect('users.index');
        }
    }
}
