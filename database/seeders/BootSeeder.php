<?php
namespace Database\Seeders;

use App\Sys\Build\SystemResources;
use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;

class BootSeeder extends Seeder
{
    use SeedOnce;

    /**
     * @throws \Exception
     */
    public function run()
    {
        //SystemResources::build();
    }
}
