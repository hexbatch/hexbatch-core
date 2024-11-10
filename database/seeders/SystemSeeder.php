<?php

namespace Database\Seeders;

use App\Sys\Build\SystemResources;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        SystemResources::build();
    }
}
