<?php
namespace OroCMS\Admin\Controllers;

use Session;
use OroCMS\Admin\Validation\Users\ProfileUpdate;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class AuthController extends BaseController
{
    /**
     * Show login page.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view('login');
    }

    /**
     * Login.
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (auth()->attempt($credentials, $remember, false)) {
            $user = auth()->getProvider()->retrieveByCredentials($credentials);

            if ($user->is('admin') && auth()->login($user)) {
                return $this->redirect('dashboard');
            }
        }

        return redirect()->back()->withFlashMessage('Login failed!')->withFlashType('danger');
    }

    /**
     * Logout.
     *
     * @return \Response
     */
    public function logout()
    {
        auth()->logout();

        return $this->redirect('login.index');
    }

    /**
     * Show profile information
     *
     * @return mixed
     */
    public function show_profile()
    {
        return $this->view('profile');
    }

    /**
     * Update profile details
     *
     * @return mixed
     */
    public function update_profile(ProfileUpdate $request)
    {
        $redirect = 'profile';

        // catch exceptions
        try {
            // load it up
            $account = auth()->user();
            $account->fill($request->all());

            $revalidate = !empty( $request->input('password') );

            // save
            $account->save();

            // password is changed, revalidate session
            if ($revalidate) {
                Session::flush();
                Session::regenerate();

                auth()->login($account);
            }

            return $this->redirect($redirect)
                ->withFlashMessage( trans('admin.profile.message.updated') )->withFlashType('info');
        }
        catch(\Exception $e) {
            return $this->redirect($redirect)
                ->withFlashMessage( isset($e->errorInfo) ? $e->errorInfo[2] : $e->getMessage() )
                ->withFlashType('danger');
        }
    }
}
