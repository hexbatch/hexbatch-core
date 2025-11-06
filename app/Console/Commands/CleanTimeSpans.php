<?php

namespace App\Console\Commands;

use App\Models\TimeBoundSpan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanTimeSpans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hex:clean_time_spans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old time spans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = TimeBoundSpan::cleanUpOld();
        $now = Carbon::now()->timezone(config('hbc.system.loggging.timezone'))->toIso8601String();
        $this->info("Ran @$now deleting $count spans ");
    }
}
