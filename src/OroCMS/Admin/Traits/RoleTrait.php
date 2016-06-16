<?php
namespace OroCMS\Admin\Traits;

trait RoleTrait
{
    /**
     * Relation belongs-to roles.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(config('admin.models.role'))->withTimestamps();
    }

    /**
     * Add a single role to user.
     *
     * @param $id
     */
    public function addSingleRole($id)
    {
        // verify role
        $role_class = config('admin.models.role');
        $role = $role_class::findorFail($id);

        $this->roles()->attach($role->id);
    }

    /**
     * Determine whether the user has role that given by name parameter.
     *
     * @param $name
     *
     * @return bool
     */
    public function is($name)
    {
        foreach ($this->roles as $role) {
            if ($role->name == $name) {
                return true;
            }
        }

        return false;
    }
}