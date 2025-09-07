<?php

namespace App\OpenApi\Results\Elements;

use App\Models\Attribute;
use App\Models\ElementSetMember;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\Attributes\AttributeResponse;
use App\OpenApi\Results\ResultBase;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use OpenApi\Attributes as OA;


/**
 * Show details about an element
 */
#[OA\Schema(schema: 'ElementValueResponse')]
class ElementValueResponse extends ResultBase
{
    use ThingMimimalResponseTrait;
    #[OA\Property(title: 'Attribute uuid',type: HexbatchUuid::class)]
    public string $attribute_uuid = '';

    #[OA\Property(title: 'Attribute name')]
    public string $attribute_name = '';

    #[OA\Property(title: 'Attribute')]
    public ?AttributeResponse $attribute = null ;

    #[OA\Property( title: "Value", items: new OA\Items(), nullable: true)]
    protected ?array $value = null;






    public function __construct(ElementSetMember $member,
                                Attribute $att,
                                ElementType $type,
                                $attribute_levels = 0,
                                ?Thing $thing = null
    )
    {
        parent::__construct(thing: $thing);
        $this->attribute_uuid = $att->ref_uuid;
        $this->attribute_name = $att->getName();
        if ($attribute_levels > 0) {
            $this->attribute = new AttributeResponse(given_attribute: $att,attribute_levels: $attribute_levels - 1);
        }
        $this->value = ElementValue::readContextValue(member: $member,att: $att,type: $type);


    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['attribute_uuid'] = $this->attribute_uuid;
        $ret['attribute_name'] = $this->attribute_name;
        $ret['value'] = $this->value;
        return $ret;
    }

}
