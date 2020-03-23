<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        $defaults = [
            'email' => 'superadmin',
            'email_verified_at' => now(),
            'password' => bcrypt('supersecret'),
            'name'     => 'Super-' . __('app.administrator'),
            'activated' => '1'
        ];

        $user = User::create($defaults);
        $user->assignRole('super-admin');
    }
}
