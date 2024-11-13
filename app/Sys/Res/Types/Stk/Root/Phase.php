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



    const PARENT_CLASSES = [
        Root::class
    ];

    public function onCall(): ISystemResource
    {
        $ret = parent::onCall();
        if (!$this->b_did_create_model) {return $ret;}
        if (static::EDITED_BY_PHASE_SYSTEM_CLASS) {
            try {
                $sys_params = new PhaseForSystem();
                $sys_params
                    ->setSystem(true)
                    ->setUuid(static::getClassUuid())
                    ->setDefaultPhase(static::IS_DEFAULT_PHASE)
                    ->setPhaseTypeId($this->getTypeObject()->id)
                    ->setEditedByPhaseId(null);

                $this->phase = $sys_params->doParamsAndResponse();

            } catch (\Exception $e) {
                throw new HexbatchInitException(message: $e->getMessage() . ': code ' . $e->getCode(), prev: $e);
            }
        }
        return $ret;

    }

    public function onNextStep(): void
    {
        parent::onNextStep();
        if (!$this->b_did_create_model) {return;}
        if (!static::EDITED_BY_PHASE_SYSTEM_CLASS) {return;}

        try
        {
            /**
             * @var Phase $phase_type_object
             */
            $editing_type_object = SystemTypes::getTypeByUuid(static::EDITED_BY_PHASE_SYSTEM_CLASS)->getTypeObject();
            $editing_phase = \App\Models\Phase::where('phase_type_id',$editing_type_object?->id)->first();
            if (!$editing_phase) {
                throw new \LogicException("Cannot find phase from editor of ".static::EDITED_BY_PHASE_SYSTEM_CLASS);
            }
            $sys_params = new PhaseForSystem();
            $sys_params
                ->setPhaseId($this->phase->id)
                ->setEditedByPhaseId($editing_phase->id)
                ;

            $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }

    }



}

