<?php
/**
 * Set view composers
 */

view()->composer('admin::*users.form', OroCMS\Admin\Composers\UserFormComposer::class);
