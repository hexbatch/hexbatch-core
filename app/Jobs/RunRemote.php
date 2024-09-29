<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Models\RemoteActivity;

class RunRemote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $activity_id,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * @var RemoteActivity $activity
         */
        $activity = RemoteActivity::buildActivity(id:$this->activity_id)->first();
        $activity->doCallRemote(); //this is blocking
    }
}
