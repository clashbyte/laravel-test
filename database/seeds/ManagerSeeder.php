<?php

use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Creating default manager user if not exists.
     *
     * @return void
     */
    public function run()
    {
        if (!\App\User::where('email', 'manager@test.ru')->exists()) {
            $user = new \App\User();
            $user->name         = 'Менеджер';
            $user->email        = 'manager@test.ru';
            $user->password     = Hash::make('123');
            $user->role         = \App\User::ROLE_MANAGER;
            $user->save();
        }
    }
}
