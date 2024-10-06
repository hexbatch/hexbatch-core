<?php

namespace Database\Seeders;

use App\System\Collections\SystemUsers;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemUsers::generateObjects();
    }
}
