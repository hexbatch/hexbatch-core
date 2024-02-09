<?php

namespace App\Console\Commands;

use App\Models\TimeBound;
use Illuminate\Console\Command;

class MakeTimeSpans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bounds:make_time_spans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes time spans';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        TimeBound::generateSpans();
    }
}
