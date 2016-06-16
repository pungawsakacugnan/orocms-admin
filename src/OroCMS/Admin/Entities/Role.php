<?php
namespace OroCMS\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The fillable property.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('admin.models.user'))->withTimestamps();
    }
}
