<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\OpenApi\Params\Element\ElementSelectParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;

#[ApiParamMarker( param_class: ElementSelectParams::class)]
class Purge extends Destroy
{
    const UUID = '9e70edf8-19d9-4b38-b552-e0013ad55e61';
    const TYPE_NAME = 'api_element_purge';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\ElementDestroy::class,
    ];


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\ElementDestroy(
            given_element_uuids: $this->params->getElementRefs(), is_system: true,send_event: false
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => 'Purging Elements','action'=>$creator];


        //last in tree is the
        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;
    }


}

