<?php
namespace OroCMS\Admin\Providers;

use OroCMS\Admin\Facades\Module;
use Caffeinated\Menus\Facades\Menu;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Menu::make('admin', function($menu) {
            // dashboard
            $menu->add(trans('admin.menu.dashboard'), 'admin')
                ->icon('dashboard');

            // users
            $account_menu = $menu->add(trans('admin.menu.accounts'), route('admin.users.index'))
                ->data('role', ['admin'])
                ->data('glyphicon', 'glyphicon glyphicon-user')
                ->active('admin/users/*');

            // settings
            $menu->add(trans('admin.menu.settings'), route('admin.settings.index'))
                ->data('glyphicon', 'glyphicon glyphicon-wrench')
                ->active('admin/settings/*');

            // logs
            $menu->add(trans('admin.menu.logs'), 'admin/logs')
                ->data('role', ['admin'])
                ->data('glyphicon', 'glyphicon glyphicon-book')
                ->active('admin/logs/*')
                ->divide();
        });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
    }
}
