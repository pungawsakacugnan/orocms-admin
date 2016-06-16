<?php
namespace OroCMS\Admin\Entities;

use OroCMS\Admin\Traits\RoleTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes, RoleTrait;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'roles'];

    /**
     * The fillable property.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'published'];

    /**
     * Softdelete attribute.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = \Hash::make($value);
        }
    }
}
