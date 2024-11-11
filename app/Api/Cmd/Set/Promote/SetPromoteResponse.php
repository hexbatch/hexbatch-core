<?php
namespace App\Api\Cmd\Set\Promote;


use App\Api\Cmd\Design\Promote\DesignPromoteResponse;
use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementSet;
use App\Models\ElementSetMember;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetPromote;

class SetPromoteResponse extends SetPromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?ElementSet $generated_set = null
    )
    {
        parent::__construct();
    }

    public function toThing(Thing $thing)
    {

    }

    protected function run(SetPromoteParams $params) {
        $set = new ElementSet();
        $set->ref_uuid = $params->getUuid();
        $set->parent_set_element_id = $params->getParentSetElementId();
        if (!is_null($params->getHasEvents())) {
            $set->has_events = $params->getHasEvents();
        }

        if (!is_null($params->isSystem())) {
            $set->is_system = $params->isSystem();
        }

        $set->save();
        foreach ($params->getContentElementIds() as $element_id) {
           $node = new ElementSetMember();
           $node->member_element_id = $element_id;
           $node->holder_set_id = $set->id;
           $node->save();
        }
        $this->generated_set = $set;
    }

    /**
     * @param SetPromoteParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,SetPromoteParams::class) || is_subclass_of($params,SetPromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not SetPromoteParams");
        }
        $worker = new SetPromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedSet(): ?ElementSet
    {
        return $this->generated_set;
    }


}
