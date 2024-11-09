<?php
namespace App\Api\Cmd\Phase\Promote;


use App\Api\Cmd\Design\Promote\DesignPromoteResponse;
use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;

use App\Exceptions\HexbatchInvalidException;

use App\Models\Phase;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph\PhasePromote;



class PhasePromoteResponse extends PhasePromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?Phase $generated_phase = null
    )
    {
    }

    public function toThing(Thing $thing)
    {

    }

    protected function run(PhasePromoteParams $params) {
        if ($params->getPhaseId()) {
            $phase = Phase::findOrFail($params->getPhaseId());
        } else {
            $phase = new Phase();
        }

        if ($params->getUuid()) {
            $phase->ref_uuid = $params->getUuid();
        }

        if ($params->getPhaseTypeId()) {
            $phase->phase_type_id = $params->getPhaseTypeId();
        }

        if ($params->getEditedByPhaseId()) {
            $phase->edited_by_phase_id = $params->getEditedByPhaseId();
        }

        if ($params->isDefaultPhase()) {
            $phase->is_default_phase = $params->isDefaultPhase();
        }

        $phase->save();
        $this->generated_phase = $phase;
    }

    /**
     * @param PhasePromoteParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,PhasePromoteParams::class) || is_subclass_of($params,PhasePromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not PhasePromoteParams");
        }
        $worker = new PhasePromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedPhase(): ?Phase
    {
        return $this->generated_phase;
    }


}
