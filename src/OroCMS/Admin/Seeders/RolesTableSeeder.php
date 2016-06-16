<?php
namespace OroCMS\Admin\Seeders;

use Illuminate\Database\Seeder;
use OroCMS\Admin\Entities\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('roles')->insert([
            // 0 -- guest
            [
                'id' => '1',
                'name' => 'user',
                'description' => 'Default user role.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '2',
                'name' => 'manager',
                'description' => 'Ability to create/remove/delete own feed list and subscribers.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '7',
                'name' => 'admin',
                'description' => 'Use of this account can possibly cause irreversible damage to the system.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
