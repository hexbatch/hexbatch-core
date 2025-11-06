<?php

namespace Database\Seeders;

use Hexbatch\Thangs\HexbatchThangsProvider;
use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;

class DatabaseSeeder extends Seeder
{
    use SeedOnce;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(BootSeeder::class);

        $children = HexbatchThangsProvider::findClasses(relative_source_folder: 'libs',fully_qualified_parent_class: 'Illuminate\Database\Seeder');
        foreach ($children as $child) {
            $this->call($child);
        }
    }
}
