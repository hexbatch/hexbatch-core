<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BuildSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:system';
    /*
     *
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'makes, updates and tools for the system types, attributes, elements, sets, namespaces and servers';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        /*
         * todo need tasks
         *   :build
         *   :list
         *   :list_new
         *   :list_old
         *   :check (uuid and other logic)
         */
    }
}
