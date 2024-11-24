<?php

namespace App\Jobs;

use App\Enums\Things\TypeOfThingStatus;
use App\Models\Thing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class RunThing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Thing $thing
    ) {}


    public function handle(): void
    {
        if ($this->thing->thing_status !== TypeOfThingStatus::THING_PENDING) {return;}
        try {
            $this->thing->setProcessedAt();
            $this->thing->runThing();

        } catch (\Exception $e) {
            $this->thing->thing_status = TypeOfThingStatus::THING_ERROR;
            $this->thing->setException($e);
        }

        //see if all children ran, if so, put the parent on the processing
        $this->thing->maybeQueueParent();
    }
}
