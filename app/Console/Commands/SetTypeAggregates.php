<?php

namespace App\Console\Commands;

use App\Models\ElementType;
use App\Models\TimeBound;
use Illuminate\Console\Command;

class SetTypeAggregates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bounds:set_type_aggregates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates unions of geo and time spans from the attributes';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        ElementType::updateAggregatedStats();
    }
}
