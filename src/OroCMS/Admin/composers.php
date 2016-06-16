<?php
/**
 * Set view composers
 */

view()->composer('admin::*users.form', OroCMS\Admin\Composers\UserFormComposer::class);

/**
 * Blade extensions
 */

/**
 * @directive: define
 * @usage: @define x = 1 // assigns 1 to x
 * http://stackoverflow.com/questions/13002626/laravels-blade-how-can-i-set-variables-in-a-template
 */
Blade::extend(function($value) {
    return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
});