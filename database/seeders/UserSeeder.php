<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run()
    {
        User::Create([
            'name' => 'Agung Prayitno',
            'username' => 'agungyitno',
            'email' => 'agung@gmail.com',
            'password' => Hash::make('qwerty12345'),
            'avatar' => 'default.png',
            'current_workspace' => 1,
        ]);
        User::factory(10)->create();
    }
}
