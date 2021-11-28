<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->username,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('qwerty12345'),
            'email_verified_at' => Carbon::now()->toDateTime(),
            'remember_token' => Str::random(10),
            'avatar' => 'default.png',
        ];
    }
}
