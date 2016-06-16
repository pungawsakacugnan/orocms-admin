<?php
namespace OroCMS\Admin\Validation\Users;

use OroCMS\Admin\Validation\Validator;

class Update extends Validator
{
    public function rules()
    {
        if (!$this->beforeActivateRule()) {
            return [];
        }

        // get id from segment
        $id = $this->segment(3);

        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
        ];

        if ($this->has('password')) {
            $rules['password'] = 'required|min:6|max:20';
        }

        return $rules;
    }
}
