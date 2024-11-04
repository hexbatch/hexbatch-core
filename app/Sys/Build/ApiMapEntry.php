<?php

namespace App\Sys\Build;


use App\Sys\Res\Types\Stk\Root\Act\NoEventsTriggered;
use App\Sys\Res\Types\Stk\Root\Act\SystemPrivilege;

class ApiMapEntry
{
    public ?string $api_uuid = null;
    public ?string $api_name = null;

    public ?string $api_params_class = null;
    public ?string $api_response_class = null;
    public ?string $api_thing_setup_class = null;
    public ?string $api_thing_result_class = null;
    public bool $is_protected = false;
    public bool $has_events = true;

    public function isDataComplete() : bool {
        return $this->api_uuid && $this->api_name && $this->api_response_class &&
            $this->api_params_class && $this->api_thing_setup_class && $this->api_thing_result_class;
    }

    public function setApi(string $full_class_name) {
        if (is_subclass_of($full_class_name, 'App\Sys\Res\Types\Stk\Root\Api') ) {
            $this->api_uuid = $full_class_name::getClassUuid();
            $this->api_name = $full_class_name::getClassTypeName();
            $this->is_protected = $full_class_name::hasInAncestors(SystemPrivilege::class);
            $this->has_events = !$full_class_name::hasInAncestors(NoEventsTriggered::class);
        } else {
            return;
        }
        $interfaces = class_implements($full_class_name);
        if (isset($interfaces['App\Api\IApiOaParams'])) {
            if ($this->api_params_class) {
                throw new \LogicException("Already have an IApiOaParams for $this->api_name $this->api_uuid");
            }
            $this->api_params_class = $full_class_name;
        }

        if (isset($interfaces['App\Api\IApiOaResponse'])) {
            if ($this->api_response_class) {
                throw new \LogicException("Already have an IApiOaResponse for $this->api_name $this->api_uuid");
            }
            $this->api_response_class = $full_class_name;
        }

        if (isset($interfaces['App\Api\Thinger\IApiThingSetup'])) {
            if ($this->api_thing_setup_class) {
                throw new \LogicException("Already have an IApiThingSetup for $this->api_name $this->api_uuid");
            }
            $this->api_thing_setup_class = $full_class_name;
        }

        if (isset($interfaces['App\Api\Thinger\IApiThingResult'])) {
            if ($this->api_thing_result_class) {
                throw new \LogicException("Already have an IApiThingResult for $this->api_name $this->api_uuid");
            }
            $this->api_thing_result_class = $full_class_name;
        }
    }


    public function toArray() {
        if (!$this->isDataComplete()) {
            throw new \LogicException("Data not complete for $this->api_name $this->api_uuid");
        }
        return [
            'uuid'=> $this->getUuid(),
            'api_name'=> $this->getApiName(),
            'is_protected'=> $this->isProtected(),
            'has_events'=> $this->hasEvents(),
            BuildApiFacet::FACET_PARAMS->value => $this->getApiParamsClass(),
            BuildApiFacet::FACET_RESPONSE->value => $this->getApiResponseClass(),
            BuildApiFacet::FACET_SETUP->value => $this->getApiThingSetupClass(),
            BuildApiFacet::FACET_RESULT->value => $this->getApiThingResultClass(),
        ];
    }

    public function getUuid(): ?string
    {
        return $this->api_uuid;
    }

    public function getApiName(): ?string
    {
        return $this->api_name;
    }

    public function getApiParamsClass(): ?string
    {
        return $this->api_params_class;
    }

    public function getApiResponseClass(): ?string
    {
        return $this->api_response_class;
    }

    public function getApiThingSetupClass(): ?string
    {
        return $this->api_thing_setup_class;
    }

    public function getApiThingResultClass(): ?string
    {
        return $this->api_thing_result_class;
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
