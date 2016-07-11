<?php
namespace OroCMS\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
    * Disable timestamps.
    *
    * @var boolean
    */
    public $timestamps = false;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded  = ['id'];

    /**
     * The fillable property.
     *
     * @var array
     */
    protected $fillable = ['key', 'value'];
}