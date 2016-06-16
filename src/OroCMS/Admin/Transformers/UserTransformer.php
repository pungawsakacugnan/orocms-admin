<?php
namespace OroCMS\Admin\Transformers;

use OroCMS\Admin\Entities\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Include deleted_at key on results.
     *
     * @var boolean
     */
    protected $soft_deletes = false;

    public function __construct()
    {
        $args = func_get_args();
        $options = array_shift($args);

        if (is_array($options)) {
            foreach ($options as $k => $v) {
                if (isset($this->$k)) {
                    $this->$k = $v;
                }
            }
        }
    }

    public function transform(User $user)
    {
        $data = [
            'id' => (int)$user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => (string)$user->created_at,
            'deleted_at' => (string)$user->deleted_at,
            'last_login' => (string)$user->last_login,
            'role' => implode(', ', $user->roles->lists('name')->all()),
            'deleted' => !empty($user->deleted_at),
            'published' => (int)$user->published,
        ];

        if ($this->soft_deletes) {
            $data['deleted_at'] = (string)$user->deleted_at;
        }

        return $data;
    }
}