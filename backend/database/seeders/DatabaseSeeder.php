<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Every seeder here is idempotent, so it is safe to run on each
     * container start without duplicating data.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            GearSeeder::class,
        ]);
    }
}
