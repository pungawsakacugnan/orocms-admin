<?php
/**
 * Event Listeners
 */
Event::listen('admin::routes', OroCMS\Admin\Listeners\RoutesListener::class);
Event::listen('plugins.*', OroCMS\Admin\Listeners\PluginsListener::class);
