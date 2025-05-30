<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\TimeBound;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ds extends Cmd
{
    const UUID = 'f8702a5b-9dee-4a9e-9db9-ea93142dfa7b';
    const ACTION_NAME = TypeOfAction::BASE_DESIGN;




    const PARENT_CLASSES = [
        Cmd::class
    ];


    public function getAttribute(): ?Attribute
    {
        /** @uses ActionDatum::data_attribute() */
        return $this->action_data->data_attribute;
    }



    public function getDesignType(): ?ElementType
    {
        return $this->getGivenType();
    }

    public function getParentAttribute(): ?Attribute
    {
        /** @uses ActionDatum::data_second_attribute() */
        return $this->action_data->data_second_attribute;
    }

    public function getDesignAttribute(): ?Attribute
    {
        /** @uses ActionDatum::data_third_attribute() */
        return $this->action_data->data_third_attribute;
    }

    protected ?string           $given_location_uuid = null;
    public function getGivenLocationBound() : ?LocationBound {
        if (!$this->given_location_uuid) { return null;}
        return LocationBound::getThisLocation(uuid: $this->given_location_uuid);
    }

    protected ?string           $given_time_uuid = null;
    public function getGivenTimeBound() : ?TimeBound {
        if (!$this->given_time_uuid) { return null;}
        return TimeBound::getThisSchedule(uuid: $this->given_time_uuid);
    }




}

