<?php
namespace OroCMS\Admin\Composers;

use OroCMS\Admin\Entities\Role;

class UserFormComposer
{
    public function compose($view)
    {
        $roles = Role::lists('name', 'id');

        $view->with(compact('roles'));
    }
}
