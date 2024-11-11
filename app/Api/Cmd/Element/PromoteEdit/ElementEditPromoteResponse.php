<?php
namespace App\Api\Cmd\Element\PromoteEdit;


use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\Element;
use App\Models\ElementSet;

use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromoteEdit;
use Illuminate\Support\Facades\DB;

class ElementEditPromoteResponse extends ElementPromoteEdit implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        /** @var Element[] $edited_elements */
        protected array $edited_elements = []
    )
    {
        parent::__construct();
    }

    public function toThing(Thing $thing)
    {

    }

    /**
     * @throws \Exception
     */
    protected function run(ElementEditPromoteParams $params) {

         $eles = Element::whereIn('id',$params->getElementIds())->get();

         $new_set = null;
         if ($params->getSetId()) {
             /** @type ElementSet $new_set */
             $new_set = ElementSet::buildSet(id:$params->getSetId());
         }
        try {
            DB::beginTransaction();
            $this->edited_elements = [];
            /**
             * @var Element $el
             */
            foreach ($eles as $el) {
                $this->edited_elements[] = $el;
                if ($params->getPhaseId()) {
                    $el->element_phase_id = $params->getPhaseId();
                }
                if ($params->getOwningNamespaceId()) {
                    $el->element_namespace_id = $params->getOwningNamespaceId();
                }

                if (!is_null($params->isSystem())) {
                    $el->is_system = $params->isSystem();
                }
                $el->save();
                $new_set?->addElement($el, false);
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
        if (!(is_a($params,ElementEditPromoteParams::class) || is_subclass_of($params,ElementEditPromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not ElementEditPromoteParams");
        }
        $worker = new ElementEditPromoteResponse();
        $worker->run($params);
        return $worker;
    }


    /** @return Element[] */
    public function getGeneratedElements(): array
    {
        return $this->edited_elements;
    }


}
