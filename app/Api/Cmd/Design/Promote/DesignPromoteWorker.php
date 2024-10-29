<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\IActionReturn;
use App\Api\Cmd\IActionWorker;
use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromotion;

class DesignPromoteWorker extends DesignPromotion implements IActionWorker
{


    public function doWork($params): IActionReturn
    {
        if (!is_a($params,DesignPromoteParams::class)) {
            throw new HexbatchInvalidException("Params is not IDesignPromotionParams");
        }

        $type = new ElementType();
        $type->ref_uuid = $params->getUuid();
        $type->type_name = $params->getTypeName();
        $type->lifecycle = $params->getLifecycle();
        $type->owner_namespace_id = $params->getNamespace()?->id ;
        $type->imported_from_server_id = $params->getServer()?->id ;
        $type->is_system = $params->isSystem() ;
        $type->is_final_type = $params->isFinalType() ;
        $type->save();

        return new DesignPromoteReturn(new_type: $type);
    }
}
