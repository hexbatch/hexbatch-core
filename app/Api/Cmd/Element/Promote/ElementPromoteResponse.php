<?php
namespace App\Api\Cmd\Element\Promote;

use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromote;
use Illuminate\Support\Facades\DB;

class ElementPromoteResponse extends ElementPromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        /** @var Element[] $generated_elements */
        protected array $generated_elements = []
    )
    {
    }

    public function toThing(Thing $thing)
    {
        // todo implement writing to thing method
    }

    /**
     * @throws \Exception
     */
    protected function run(ElementPromoteParams $params) {


        try {
            $this->generated_elements = [];
            $uuid_index = 0;
            DB::beginTransaction();
            foreach ($params->getNsOwnerIds() as $namespace_owner_id) {
                foreach ($params->getDestinationSetIds() as $set_id) {
                    for ($set_index = 0; $set_index < $params->getNumberPerSet(); $set_index++) {
                        $ele = new Element();
                        $ele->element_parent_type_id = $params->getParentTypeId();
                        $ele->element_phase_id = $params->getPhaseId();
                        $ele->element_namespace_id = $namespace_owner_id;
                        if (count($params->getUuids())) {
                            $ele->ref_uuid = $params->getUuids()[$uuid_index++]??null;
                        }
                        $this->generated_elements[] = $ele;
                        /** @type ElementSet $set */
                        $set = ElementSet::buildSet(id:$set_id)->first();
                        $set?->addElement($ele,false); //allow for the set to not exist
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,ElementPromoteParams::class) || is_subclass_of($params,ElementPromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not ElementPromoteParams");
        }
        $worker = new ElementPromoteResponse();
        $worker->run($params);
        return $worker;
    }


    /** @return Element[] */
    public function getGeneratedElements(): array
    {
        return $this->generated_elements;
    }


}
