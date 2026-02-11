<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\EccdSeeder;
use Database\Seeders\Eccd2004Seeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EccdQuestionsSeeder::class,
            Eccd2004Seeder::class,
        ]);
    }
}
