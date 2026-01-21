<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        for ($i=0; $i < 10; $i++) { 
            User::create([
            'name' => $faker->name(),
            'email' => $faker->email(),
            'password' => $faker->password(4,6),
            'role'=> $faker->randomElement(['teacher','parent','admin']),
            'home_address' => $faker->address(),
            'status'=> $faker->randomElement(['active','inactive']),
            'profile_path'=> $faker->text(10)
        ]);
        }
       
    }
}
