<?php

namespace Database\Seeders;

use App\Models\electeurs;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MembresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        electeurs::factory(1000)->create();
    }
}
