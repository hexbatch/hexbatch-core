<?php
namespace App\Api\Cmd\Set\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;


use App\Api\Cmd\Set\SetParams;
use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetPromote;


class SetPromoteParams extends SetPromote implements IActionParams,IActionOaInput
{

    use SetParams;



    /** @return int[] */
    public function getContentElementIds(): array
    {
        return $this->content_element_ids;
    }

    public function getParentSetElementId(): ?int
    {
        return $this->parent_set_element_id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getHasEvents(): ?bool
    {
        return $this->has_events;
    }

    public function fromThing(Thing $thing): void
    {
        // todo pull the data from the thing and fill in the data here from the json stored there
    }




}
