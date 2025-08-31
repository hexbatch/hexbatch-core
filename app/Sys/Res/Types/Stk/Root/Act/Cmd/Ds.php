<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ds extends Cmd
{
    const UUID = 'f8702a5b-9dee-4a9e-9db9-ea93142dfa7b';
    const ACTION_NAME = TypeOfAction::BASE_DESIGN;




    const PARENT_CLASSES = [
        Cmd::class
    ];



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


}

