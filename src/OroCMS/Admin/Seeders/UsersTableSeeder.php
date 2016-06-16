<?php
namespace OroCMS\Admin\Seeders;

use Illuminate\Database\Seeder;
use OroCMS\Admin\Entities\User;
use OroCMS\Admin\Entities\Role;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'published' => true
        ]);

        // add role for this user
        \DB::table('role_user')->insert([
            // 0 -- guest
            [
                'role_id' => '7',
                'user_id' => $user->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
