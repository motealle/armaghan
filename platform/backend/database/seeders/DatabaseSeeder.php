<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Intentionally empty.
        // Production credentials/users must never be seeded from repository constants.
        // Demo catalog data, when needed, must be invoked through an explicit non-production seeder.
    }
}
