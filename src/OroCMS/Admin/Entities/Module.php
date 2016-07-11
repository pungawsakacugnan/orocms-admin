<?php
namespace OroCMS\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * Disable timestamp checking.
     */
    public $timestamps  = false;

    /**
     * The fillable property.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'published'];

    /**
     * Scope "published".
     *
     * @param mixed $query
     *
     * @return mixed
     */
    public function scopepublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope "withName".
     *
     * @param mixed $query
     *
     * @return mixed
     */
    public function scopewithName($query, $name)
    {
        return $query->where('name', $name);
    }
}
