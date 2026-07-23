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
    protected $signature = 'hex:new-build {--update} {--all} {--list-new}';


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

        if ($this->option('update')) {
            $builder->doUpdateBuild();
        }
        elseif ($this->option('list-new')) {
            $builder->doListInOutput();
        }
        elseif ($this->option('all'))  {
            $builder->doNewBuild();
        } else {
            $this->warn("pick an option");
            return 1;
        }

        $this->info("done");
        return 0;
    }
}
