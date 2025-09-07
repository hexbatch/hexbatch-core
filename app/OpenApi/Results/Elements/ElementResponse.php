<?php

namespace App\OpenApi\Results\Elements;

use App\Models\Element;
use App\Models\ElementSetMember;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\Phase\PhaseResponse;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Types\TypeResponse;
use App\OpenApi\Results\UserNamespaces\UserNamespaceResponse;
use Carbon\Carbon;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use OpenApi\Attributes as OA;


/**
 * Show details about an element
 */
#[OA\Schema(schema: 'ElementResponse')]
class ElementResponse extends ResultBase
{
    use ThingMimimalResponseTrait;
    #[OA\Property(title: 'User namespace uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Phase uuid',type: HexbatchUuid::class)]
    public string $phase_uuid = '';

    #[OA\Property(title: 'Namespace uuid',type: HexbatchUuid::class)]
    public ?string $namespace_uuid = null;

    #[OA\Property(title: 'Phase',type: HexbatchUuid::class)]
    public ?PhaseResponse $phase = null;

    #[OA\Property(title: 'Type of element')]
    public ?TypeResponse $type = null ;


    #[OA\Property(title: 'Namespace')]
    public ?UserNamespaceResponse $namespace = null ;


    #[OA\Property(title: 'Namespace created at',format: 'date-time')]
    public ?string $created_at = '';


    #[OA\Property( title: 'Values')]
    /**
     * @var ElementValueResponse[] $values
     */
    public array $values = [];





    public function __construct(Element $given_element,?ElementSetMember $member = null
        ,int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0,?Thing $thing = null)
    {
        parent::__construct(thing: $thing);
        $this->uuid = $given_element->ref_uuid;

        /** @uses  Element::element_phase() */
        $this->phase_uuid = $given_element->element_phase->ref_uuid;

        /** @uses  Element::element_namespace() */
        $this->namespace_uuid = $given_element->element_namespace?->ref_uuid;


        $this->created_at = $given_element->created_at? Carbon::parse($given_element->created_at,'UTC')
            ->timezone(config('app.timezone'))->toIso8601String():null;

        if ($member) {
            foreach ($given_element->element_parent_type->getAllAttributes() as $att) {
                $this->values[] = new ElementValueResponse(member: $member,att: $att,type: $given_element->element_parent_type,
                    attribute_levels: $attribute_level);
            }
        }

        if ($type_level > 0) {
            $this->type = new TypeResponse(given_type: $given_element->element_parent_type,
                attribute_levels: $attribute_level,inherited_attribute_levels: $attribute_level);
        }

        if ($namespace_level > 0) {
            /** @uses  Element::element_namespace() */
            $this->namespace = new UserNamespaceResponse(namespace: $given_element->element_namespace);
        }

        if ($phase_level > 0) {
            /** @uses  Element::element_phase() */
            $this->phase = new PhaseResponse(given_phase: $given_element->element_phase);
        }



    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['uuid'] = $this->uuid;
        $ret['phase_uuid'] = $this->phase_uuid;
        $ret['namespace_uuid'] = $this->namespace_uuid;
        $ret['created_at'] = $this->created_at;


        if (count($this->values)) {
            $ret['values'] = $this->values;
        }

        if ($this->phase) {
            $ret['phase'] = $this->phase;
        }

        if ($this->type) {
            $ret['type'] = $this->type;
        }

        if ($this->namespace) {
            $ret['namespace'] = $this->namespace;
        }

        return $ret;
    }

}
