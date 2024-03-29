<?php

namespace App\Jobs;

use App\Models\RemoteStack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunRemoteStack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public RemoteStack $stack,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->stack->run_stack();
    }
}
