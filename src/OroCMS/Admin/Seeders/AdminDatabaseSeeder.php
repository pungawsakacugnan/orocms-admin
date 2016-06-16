<?php
namespace OroCMS\Admin\Seeders;

use Illuminate\Database\Seeder;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run()
    {
        $this->call(__NAMESPACE__.'\\RolesTableSeeder');
        $this->call(__NAMESPACE__.'\\UsersTableSeeder');
    }
}
