<?php
namespace OroCMS\Admin\Validation\Users;

use OroCMS\Admin\Validation\Validator;

class ProfileUpdate extends Validator
{
    public function rules()
    {
        // get id from segment
        $id = auth()->user()->id;

        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
        ];

        if ($this->has('password') or $this->has('password_confirmation')) {
            $rules['password'] = 'required|confirmed|min:6|max:20';
        }

        return $rules;
    }
}
