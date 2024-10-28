<?php

namespace App\Api\Actions\Design\Promotion;

use App\Api\Actions\AInterfaces\ActionWorkReturn;
use App\Api\Actions\AInterfaces\IActionLogic;
use App\Api\Actions\AInterfaces\IActionWorkReturn;

use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementType;
use App\Models\Thing;

/**
 * todo Found in generated map
 */
class DesignPromotionWork implements IActionLogic
{

    /**
     * @param Thing $thing
     * @param IDesignPromotionParams $params
     * @return IActionWorkReturn
     */
    public static function doWork( Thing $thing, $params): IActionWorkReturn
    {
        $interfaces = class_implements($params);

        if (!isset($interfaces['App\Api\Actions\Design\Promotion\IDesignPromotionParams'])) {
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
        //todo store this type id into the thing's data (use thing method)
        return new ActionWorkReturn();
    }
}
