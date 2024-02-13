<?php

namespace App\Jobs;

use App\Models\RemoteActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunRemote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public RemoteActivity $activity,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug("calling..");
        $this->activity->doCallRemote();
    }
}
