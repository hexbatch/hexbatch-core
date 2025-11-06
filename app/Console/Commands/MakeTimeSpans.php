<?php

namespace App\Console\Commands;

use App\Models\TimeBound;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MakeTimeSpans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hex:make_time_spans';

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
        list($number_bounds,$number_spans) = TimeBound::generateSpans();
        $now = Carbon::now()->timezone(config('hbc.system.loggging.timezone'))->toIso8601String();
        $this->info("Ran @$now updating $number_bounds bounds and inserting $number_spans spans ");
    }
}
