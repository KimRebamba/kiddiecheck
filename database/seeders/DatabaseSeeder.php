<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\EccdSeeder;
use Database\Seeders\Eccd2004Seeder;
use Illuminate\Database\Seeder;
use Database\Seeders\DemoSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seed scale version and related lookup tables first
            Eccd2004Seeder::class,
            // Then seed the ECCD checklist questions that depend on the scale version
            EccdQuestionsSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
