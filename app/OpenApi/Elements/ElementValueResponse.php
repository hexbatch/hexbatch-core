<?php

namespace App\OpenApi\Elements;

use App\OpenApi\Common\HexbatchUuid;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSetMember;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\OpenApi\Attributes\AttributeResponse;
use App\OpenApi\Types\TypeResponse;
use Carbon\Carbon;
use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about an element
 */
#[OA\Schema(schema: 'ElementResponse')]
class ElementValueResponse implements  JsonSerializable
{
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
                                $attribute_levels = 0
    )
    {
        $this->attribute_uuid = $att->ref_uuid;
        $this->attribute_name = $att->getName();
        if ($attribute_levels > 0) {
            $this->attribute = new AttributeResponse(given_attribute: $att,attribute_levels: $attribute_levels - 1);
        }
        $this->value = ElementValue::readContextValue(member: $member,att: $att,type: $type);


    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['base_type_uuid'] = $this->base_type_uuid;
        $ret['private_element_uuid'] = $this->private_element_uuid;
        $ret['public_element_uuid'] = $this->public_element_uuid;
        $ret['home_set_uuid'] = $this->home_set_uuid;
        $ret['namespace_created_at'] = $this->namespace_created_at;
        return $ret;
    }

}
