<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Sys\Build\NewBuild;

class NewBuildSystem extends Command
{
    /**
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hex:new-build';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a new system';

    /**
     * Execute the console command.
     * @throws \Exception
     * @throws \Throwable
     */
    public function handle()
    {

        $builder = new NewBuild(output: $this);
        $builder->doBuild();
        $this->info("done");

        return 0;
    }
}
