<?php
namespace OroCMS\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
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
    protected $fillable = ['id', 'name', 'priority', 'published'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hooks()
    {
        return $this->hasMany(OroCMS\Admin\Entities\PluginHook::class);
    }

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
