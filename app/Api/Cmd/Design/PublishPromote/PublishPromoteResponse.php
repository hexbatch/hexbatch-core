<?php
namespace App\Api\Cmd\Design\PublishPromote;


use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPublishPromote;
use Illuminate\Support\Facades\DB;

class PublishPromoteResponse extends DesignPublishPromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?ElementType $generated_type = null
    )
    {
    }

    public function toThing(Thing $thing)
    {

    }

    /**
     * @throws \Exception
     */
    protected function run(PublishPromoteParams $params) {
        /** @var ElementType $type */
        $type = ElementType::findOrFail($params->getTypeId());
        try {
            DB::beginTransaction();
            foreach ($params->getParentIds() as $some_parent_id) {
                $parent = ElementType::findOrFail($some_parent_id);
                ElementTypeParent::addParent(parent: $parent, child: $type, init_approval: TypeOfApproval::PUBLISHING_APPROVED);
            }

            if ($params->getLifecycle()) {
                $type->lifecycle = $params->getLifecycle();
            }


            $type->save();
            $this->generated_type = $type;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param PublishPromoteParams $params
     * @return PublishPromoteResponse
     * @throws \Exception
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,PublishPromoteParams::class) || is_subclass_of($params,PublishPromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not PublishPromoteParams");
        }
        $worker = new PublishPromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedType(): ?ElementType
    {
        return $this->generated_type;
    }


}
