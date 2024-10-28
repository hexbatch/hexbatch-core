<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IActionLogic;
use App\Api\Actions\AInterfaces\IDataOutput;
use App\Api\Actions\AInterfaces\IParamsJson;
use App\Api\Actions\AInterfaces\IParamsSystem;
use App\Api\Actions\AInterfaces\IParamsThing;
use App\Models\ElementType;

class Logic implements IActionLogic
{

    public static function doWork(IParamsSystem|IParamsJson|IParamsThing $params): IDataOutput
    {
        /**
         * @var DataInput $data
         */
        $data = $params->getInputData();
        $type = new ElementType();
        $type->ref_uuid = $data->getUuid();
        $type->type_name = $data->getTypeName();
        $type->lifecycle = $data->getLifecycle();
        $type->owner_namespace_id = $data->getNamespace()?->id ;
        $type->imported_from_server_id = $data->getServer()?->id ;
        $type->is_system = $data->isSystem() ;
        $type->is_final_type = $data->isFinalType() ;
        $type->save();
        $type->refresh();
        return new DataOutput($type);
    }
}
