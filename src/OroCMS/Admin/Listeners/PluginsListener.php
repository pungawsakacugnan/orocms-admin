<?php
namespace OroCMS\Admin\Listeners;

use OroCMS\Admin\Entities\Plugin as PluginEntity;

class PluginsListener
{
    /**
     * Handle the specified event.
     */
    public function handle($plugin)
    {
        if ($plugin instanceof \OroCMS\Admin\Plugin) {
            $hooks = $plugin->getHooks();

            foreach ($hooks as $hook) {
                \Event::listen($hook->event, $hook->class);
            }
        }
    }
}
