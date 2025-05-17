<?php
namespace App\Api\Cmd\Type\AttributeAddHandle;

use App\Api\Cmd\Design\Promote\DesignPromoteResponse;
use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\Attribute;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\AttributeHandleAdd;


class AttributeAddHandleResponse extends AttributeHandleAdd implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        /** @var Attribute[] $edited_attributes */
        protected array $edited_attributes = []
    )
    {
        parent::__construct();
    }

    public function toThing( $thing)
    {

    }

    protected function run(AttributeAddHandleParams $params) {

        $edited_attributes = Attribute::whereIn('id',$params->getAttributeIds())->get();
        $this->edited_attributes = [];
        /** @var Attribute $attr */
        foreach ($edited_attributes as $attr) {
            if ($params->getHandleAttributeId()) {
                $attr->design_attribute_id = $params->getHandleAttributeId();
            } else {
                $attr->design_attribute_id = null;
            }
            $attr->save();
        }

    }

    /**
     * @param AttributeAddHandleParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,AttributeAddHandleParams::class) || is_subclass_of($params,AttributeAddHandleParams::class))) {
            throw new HexbatchInvalidException("Params is not AttributeAddHandleParams");
        }
        $worker = new AttributeAddHandleResponse();
        $worker->run($params);
        return $worker;
    }

    /**
     * @return Attribute[]
     */
    public function getEditedAttributes(): array
    {
        return $this->edited_attributes;
    }


}
