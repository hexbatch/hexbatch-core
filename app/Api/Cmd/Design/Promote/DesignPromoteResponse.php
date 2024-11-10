<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementType;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromote;

class DesignPromoteResponse extends DesignPromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?ElementType $generated_type = null
    )
    {
    }

    public function toThing(Thing $thing)
    {

    }

    protected function run(DesignPromoteParams $params) {
        $type = new ElementType();
        $type->ref_uuid = $params->getUuid();
        $type->type_name = $params->getTypeName();

        if ($params->getLifecycle()) {
            $type->lifecycle = $params->getLifecycle();
        }

        $type->owner_namespace_id = $params->getNamespaceId() ;
        $type->imported_from_server_id = $params->getServerId() ;
        $type->is_system = $params->isSystem() ;
        $type->is_final_type = $params->isFinalType() ;
        $type->save();
        $this->generated_type = $type;
    }

    /**
     * @param DesignPromoteParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,DesignPromoteParams::class) || is_subclass_of($params,DesignPromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not DesignPromoteParams");
        }
        $worker = new DesignPromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedType(): ?ElementType
    {
        return $this->generated_type;
    }


}
