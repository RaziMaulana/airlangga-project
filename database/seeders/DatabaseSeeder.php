<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KelasSeeder::class,
            GuruSeeder::class,
            SiswaSeeder::class,
            PresensiSeeder::class,
            FeedbackSeeder::class,
        ]);
    }
}
