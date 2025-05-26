<?php

namespace App\Sys\Build\Mappers;


use App\Sys\Res\Types\Stk\Root\Act\NoEventsTriggered;
use App\Sys\Res\Types\Stk\Root\Act\SystemPrivilege;

class ApiMapEntry extends ActionMap
{
    public function setFromClassName(string $full_class_name) {
        if (is_subclass_of($full_class_name, 'App\Sys\Res\Types\Stk\Root\Api') ) {

            $this->full_class_name = $full_class_name;
            $this->type_uuid = $full_class_name::getClassUuid();
            $this->internal_name = $full_class_name::getHexbatchClassName();
            $this->is_system = $full_class_name::hasInAncestors(SystemPrivilege::class);
            $this->has_events = !$full_class_name::hasInAncestors(NoEventsTriggered::class);
        }

    }

}
