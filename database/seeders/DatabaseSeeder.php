<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SectionSeeder::class,
            ServiceSeeder::class,
            ChallengeSeeder::class,
            ProcessSeeder::class,
            InvestSeeder::class,
            ContactSeeder::class,
            PartnerSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
