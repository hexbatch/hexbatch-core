<?php
namespace App\Api\Cmd\Namespace\EditPromotion;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Api\Cmd\Namespace\NamespaceParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceEditPromotion;


class NamespaceEditPromotionParams extends NamespaceEditPromotion implements IActionParams,IActionOaInput
{

    use NamespaceParams {
        validate as traitValidate;
    }
    protected function validate() {
        $this->traitValidate();
        if (!$this->getUuid()) {
            throw new \LogicException("Uuid needs to be set before you can edit the ns");
        }
    }
    public function fromThing(Thing $thing): void
    {

    }

    public function pushData(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }
}
