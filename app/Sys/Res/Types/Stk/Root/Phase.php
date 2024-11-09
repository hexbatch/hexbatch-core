<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Api\Cmd\Phase\Promote\PhaseForSystem;
use App\Exceptions\HexbatchInitException;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * When new type published, and new row is created in @see \App\Models\Phase
 * There is not a command to create a phase directly
 * When the type is destroyed, that corresponding row in the phase is destroyed
 */
class Phase extends BaseType
{
    const UUID = '1bb5ff53-6874-4914-afd9-4dc8c9534c8f';
    const TYPE_NAME = 'phase';

    const EDITED_BY_PHASE_SYSTEM_CLASS = '';

    const IS_DEFAULT_PHASE = false;

    protected \App\Models\Phase|null $phase = null;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Root::class
    ];

    public function onCall(): ISystemResource
    {
        $ret = parent::onCall();
        try
        {
            $sys_params = new PhaseForSystem();
            $sys_params
                ->setUuid(static::getClassUuid())
                ->setDefaultPhase(static::IS_DEFAULT_PHASE)
                ->setPhaseTypeId($this->getTypeObject()->id)
                ->setEditedByPhaseId(null)
            ;

            $this->phase =  $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
        }
        return $ret;

    }

    public function onNextStep(): void
    {
        parent::onNextStep();

        try
        {
            /**
             * @var Phase $phase_type_object
             */
            $phase_type_object = SystemTypes::getTypeByUuid(static::EDITED_BY_PHASE_SYSTEM_CLASS)->getTypeObject();
            $sys_params = new PhaseForSystem();
            $sys_params
                ->setPhaseId($this->phase->id)
                ->setEditedByPhaseId($phase_type_object->phase->id)
                ;

            $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
        }

    }



}

