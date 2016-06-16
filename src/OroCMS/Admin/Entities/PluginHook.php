<?php
namespace OroCMS\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class PluginHook extends Model
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
    protected $fillable = ['id', 'plugin_id', 'event', 'class', 'params', 'published'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function plugin()
    {
        return $this->belongsTo(OroCMS\Admin\Entities\Plugin::class);
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
}
