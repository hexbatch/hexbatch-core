<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;



#[HexbatchTitle( title: "Destroy a design")]
#[HexbatchBlurb( blurb: "Designs can be deleted by namespace admins without any events")]
#[HexbatchDescription( description:'')]
class DesignDestroy extends DesignPurge
{
    const UUID = 'd21d7294-35f8-4938-bff4-3e57ffe95e55';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_DESTROY;


    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];



    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();
        $this->checkIfAdmin($this->getDesignType()->owner_namespace);
    }


}

