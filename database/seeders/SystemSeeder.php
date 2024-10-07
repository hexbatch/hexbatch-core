<?php

namespace Database\Seeders;

use App\System\SystemResources;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemResources::generateObjects();
    }
}