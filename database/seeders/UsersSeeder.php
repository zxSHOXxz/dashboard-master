<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::count();
        if ($users == 0) {
            $user = new User();
            $user->name = ' مسؤول ';
            $user->email_verified_at = date("Y-m-d h:i:s");
            $user->save();
            $admin = new Admin();
            $admin->email = env('DEFAULT_EMAIL');
            $admin->password = bcrypt(env('DEFAULT_PASSWORD'));
            $admin->save();
            $user->actor()->associate($admin);
            $user->save();
        }
    }
}
