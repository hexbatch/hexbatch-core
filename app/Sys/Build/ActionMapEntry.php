<?php

namespace App\Sys\Build;


use App\Sys\Res\Types\Stk\Root\Act\NoEventsTriggered;
use App\Sys\Res\Types\Stk\Root\Act\SystemPrivilege;

class ActionMapEntry
{
    public ?string $action_uuid = null;
    public ?string $action_name = null;

    public ?string $action_input_class = null;
    public ?string $action_ouput_class = null;
    public ?string $action_param_class = null;
    public ?string $action_worker_class = null;
    public ?string $action_return_class = null;

    public bool $is_protected = false;
    public bool $has_events = true;

    public array $events = [];

    public function isDataComplete() : bool {
        return $this->action_uuid && $this->action_name && $this->action_ouput_class &&
            $this->action_input_class && $this->action_param_class && $this->action_worker_class && $this->action_return_class;
    }

    public function setAction(string $full_class_name) {
        if (is_subclass_of($full_class_name, 'App\Sys\Res\Types\Stk\Root\Act\BaseAction') ) {
            $this->action_uuid = $full_class_name::getClassUuid();
            $this->action_name = $full_class_name::getClassName();
            $this->is_protected = $full_class_name::hasInAncestors(SystemPrivilege::class);
            $this->has_events = !$full_class_name::hasInAncestors(NoEventsTriggered::class);
        } else {
            return;
        }
        $interfaces = class_implements($full_class_name);
        if (isset($interfaces['App\Api\Cmd\IActionOaInput'])) {
            if ($this->action_input_class) {
                throw new \LogicException("Already have an IActionOaInput for $this->action_name $this->action_uuid");
            }
            $this->action_input_class = $full_class_name;
        }

        if (isset($interfaces['App\Api\Cmd\IActionOaResponse'])) {
            if ($this->action_ouput_class) {
                throw new \LogicException("Already have an IActionOaResponse for $this->action_name $this->action_uuid");
            }
            $this->action_ouput_class = $full_class_name;
        }

        if (isset($interfaces['App\Api\Cmd\IActionParams'])) {
            if ($this->action_param_class) {
                throw new \LogicException("Already have an IActionParams for $this->action_name $this->action_uuid");
            }
            $this->action_param_class = $full_class_name;
        }

        if (isset($interfaces['App\Api\Cmd\IActionWorker'])) {
            if ($this->action_worker_class) {
                throw new \LogicException("Already have an IActionWorker for $this->action_name $this->action_uuid");
            }
            $this->action_worker_class = $full_class_name;
        }
        if (isset($interfaces['App\Api\Cmd\IActionWorkReturn'])) {
            if ($this->action_return_class) {
                throw new \LogicException("Already have an IActionWorkReturn for $this->action_name $this->action_uuid");
            }
            $this->action_return_class = $full_class_name;
        }

        foreach ($full_class_name::getRelatedEvents() as $rel) {
            $this->events[$rel::EVENT_NAME->value] = $rel;
            $this->events[$rel::getEventName()] = $rel;
        }


    }


    public function toArray() {
        if (!$this->isDataComplete()) {
            throw new \LogicException("Data not complete for $this->action_name $this->action_uuid");
        }
        return [
            'uuid'=> $this->getUuid(),
            'api_name'=> $this->getActionName(),
            'is_protected'=> $this->isProtected(),
            'has_events'=> $this->hasEvents(),
            'events' => $this->events,
            BuildActionFacet::FACET_PARAMS->value => $this->getActionParamClass(),
            BuildActionFacet::FACET_INPUT->value => $this->getActionInputClass(),
            BuildActionFacet::FACET_OUTPUT->value => $this->getActionOuputClass(),
            BuildActionFacet::FACET_WORKER->value => $this->getActionWorkerClass(),
            BuildActionFacet::FACET_RETURN->value => $this->getActionReturnClass(),
        ];
    }

    public function getUuid(): ?string
    {
        return $this->action_uuid;
    }

    public function getActionName(): ?string
    {
        return $this->action_name;
    }

    public function getActionInputClass(): ?string
    {
        return $this->action_input_class;
    }

    public function getActionOuputClass(): ?string
    {
        return $this->action_ouput_class;
    }

    public function getActionParamClass(): ?string
    {
        return $this->action_param_class;
    }

    public function getActionWorkerClass(): ?string
    {
        return $this->action_worker_class;
    }public function getActionReturnClass(): ?string
    {
        return $this->action_return_class;
    }

    public function isProtected(): bool
    {
        return $this->is_protected;
    }

    public function hasEvents(): bool
    {
        return $this->has_events;
    }

}
