<?php
namespace OroCMS\Admin\Validation;

use Illuminate\Foundation\Http\FormRequest;

class Validator extends FormRequest
{
    /**
     * By-pass validation if has the following variables
     *
     * @var array
     */
    protected $bypassWith = ['restore'];

    /**
     * Authorize.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Return validation rules
     *
     * @return boolean 
     */
    public function beforeActivateRule()
    {
        foreach ($this->bypassWith as $bypass) {
            if ($this->has($bypass)) {
                return false;
            }
        }

        return true;
    }
}
