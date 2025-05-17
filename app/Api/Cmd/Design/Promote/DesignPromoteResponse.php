<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementType;
use App\Models\ElementTypeServerLevel;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromote;
use Illuminate\Support\Facades\DB;

class DesignPromoteResponse extends DesignPromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?ElementType $generated_type = null
    )
    {
        parent::__construct();
    }

    public function toThing( $thing)
    {

    }

    /**
     * @throws \Exception
     */
    protected function run(DesignPromoteParams $params) {
        try {
            DB::beginTransaction();
            $type = new ElementType();
            $type->ref_uuid = $params->getUuid();
            $type->type_name = $params->getTypeName();

            if ($params->getLifecycle()) {
                $type->lifecycle = $params->getLifecycle();
            }

            $type->owner_namespace_id = $params->getNamespaceId();
            $type->imported_from_server_id = $params->getServerId();
            $type->is_system = $params->isSystem();
            $type->is_final_type = $params->isFinalType();
            $type->save();
            $this->generated_type = $type;
            if ($params->getAccess() && $params->getServerId()) {
                $access = new ElementTypeServerLevel();
                $access->server_access_type_id = $type->id;
                $access->to_server_id = $type->imported_from_server_id;
                $access->access_type = $params->getAccess();
                $access->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param DesignPromoteParams $params
     * @return DesignPromoteResponse
     * @throws \Exception
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
