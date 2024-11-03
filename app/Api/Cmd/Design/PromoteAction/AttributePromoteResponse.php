<?php
namespace App\Api\Cmd\Design\PromoteAction;

use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\Attribute;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributePromotion;

class AttributePromoteResponse extends DesignAttributePromotion implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?Attribute $generated_attribute = null
    )
    {
    }

    public function toThing(Thing $thing)
    {
        // todo implement writing to thing method
    }

    protected function run(AttributePromoteParams $params) {
        $attr = new Attribute();
        $attr->ref_uuid = $params->getUuid();
        $attr->attribute_name = $params->getAttributeName();
        $attr->attribute_approval = $params->getAttributeApproval();
        $attr->owner_element_type_id = $params->getOwnerElementTypeId() ;
        $attr->parent_attribute_id = $params->getParentAttributeId() ;
        $attr->design_attribute_id = $params->getDesignAttributeId() ;
        $attr->is_system = $params->isSystem() ;
        $attr->is_final_attribute = $params->isFinal() ;
        $attr->is_abstract = $params->isAbstract() ;
        $attr->is_seen_in_child_elements = $params->isSeenByChild() ;
        $attr->save();
        $this->generated_attribute = $attr;
    }

    /**
     * @param AttributePromoteParams $params
     * @return AttributePromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,AttributePromoteParams::class) || is_subclass_of($params,AttributePromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not AttributePromoteParams");
        }
        $worker = new AttributePromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedAttribute(): ?Attribute
    {
        return $this->generated_attribute;
    }


}
