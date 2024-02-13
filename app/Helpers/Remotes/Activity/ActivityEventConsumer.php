<?php

namespace App\Helpers\Remotes\Activity;


use App\Models\RemoteActivity;

class ActivityEventConsumer {

    public function __construct(
        public ?RemoteActivity $activity = null,
    ) {
        if ($this->activity) {
            $this->initialize();
        }
    }

    protected function initialize() :void
    {
        //todo setup event listener here

        //see https://junges.dev/documentation/laravel-kafka/v1.13/consuming-messages/4-message-handlers
    }

    protected function handle() :void
    {
        //todo call handle with the code that gets the callback
    }

    public function markThisDone() :void {
        //todo unregister event listener
    }

    public function getActivity(): ?RemoteActivity
    {
        return $this->activity;
    }

    public function setActivity(?RemoteActivity $activity): void
    {
        if ($this->getActivity()) {
            throw new \LogicException("Cannot double set the ActivityEventConsumer");
        }
        $this->activity = $activity;
        $this->initialize();
    }

    protected ?array $passthrough = null;

    public function getPassthrough(): ?array
    {
        return $this->passthrough;
    }

    public function setPassthrough(?array $passthrough): void
    {
        $this->passthrough = $passthrough;
    }

}
