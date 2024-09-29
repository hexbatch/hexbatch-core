<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Models\RemoteStack;

class RunRemoteStack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $stack_id,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * @var RemoteStack $stack
         */
        $stack = RemoteStack::buildRemoteStack(id:$this->stack_id)->first();
        $stack->run_stack();
    }
}
