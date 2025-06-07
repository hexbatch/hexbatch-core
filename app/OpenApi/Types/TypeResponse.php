<?php

namespace App\OpenApi\Types;

use App\Api\Common\HexbatchUuid;
use App\Models\ElementType;
use App\OpenApi\Attributes\AttributeResponse;
use App\OpenApi\Bounds\ScheduleResponse;
use App\OpenApi\UserNamespaces\UserNamespaceResponse;
use Carbon\Carbon;
use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about a type
 */
#[OA\Schema(schema: 'TypeResponse')]
class TypeResponse implements  JsonSerializable
{
    #[OA\Property(title: 'Type uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Type name')]
    public string $name = '';


    #[OA\Property( title: 'Parents')]
    /**
     * @var TypeParentResponse[] $parents
     */
    public array $parents = [];


    #[OA\Property(title: 'Namespace')]
    public ?UserNamespaceResponse $namespace = null;


    #[OA\Property( title: 'Attributes')]
    /**
     * @var AttributeResponse[] $attributes
     */
    public array $attributes = [];

    #[OA\Property( title: 'Inheritied Attributes')]
    /**
     * @var AttributeResponse[] $inherited_attributes
     */
    public array $inherited_attributes = [];


    #[OA\Property(title: 'Type created at',format: 'date-time')]
    public ?string $type_created_at = '';


    #[OA\Property( title: 'Access')]
    /** @var AccessLevelResponse[] $access */
    public array $access = [] ;

    #[OA\Property( title: 'Schedule')]
    public ?ScheduleResponse $schedule = null ;



    public function __construct(
        ElementType $given_type,int $namespace_levels = 0,int $parent_levels = 0,
        int $attribute_levels = 0, int $inherited_attribute_levels = 0,
        $number_time_spans = 1
    )
    {
        $this->uuid = $given_type->ref_uuid;
        $this->name = $given_type->getName();
        $this->type_created_at = $given_type->created_at?
            Carbon::parse($given_type->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        if ($parent_levels > 0) {
            foreach ($given_type->type_parents as $parent) {
                $this->parents[] = new TypeParentResponse(given_parent: $parent, namespace_levels: $namespace_levels,
                    parent_levels: $parent_levels, attribute_levels: $attribute_levels, inherited_attribute_levels: $inherited_attribute_levels);
            }
        }

        if ($namespace_levels > 0) {
            $this->namespace = new UserNamespaceResponse($given_type->owner_namespace);
        }

        foreach ($given_type->type_server_levels as $access_obj) {
            $this->access[] = new AccessLevelResponse(given_server_level: $access_obj);
        }


        if ($attribute_levels > 0) {
            foreach ($given_type->type_attributes as $att) {
                $this->attributes[] = new AttributeResponse(given_attribute: $att, attribute_levels:$attribute_levels);
            }
        }

        if ($inherited_attribute_levels > 0) {
            foreach ($given_type->getInheritedAttributes() as $att) {
                $this->attributes[] = new AttributeResponse(given_attribute: $att, attribute_levels:$inherited_attribute_levels);
            }
        }

        $this->schedule = null;
        if ($given_type->type_time) {
            $this->schedule = new ScheduleResponse(given_time: $given_type->type_time,number_spans: $number_time_spans);
        }

    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['name'] = $this->name;
        $ret['access'] = $this->access;

        if (count($this->parents)) {
            $ret['parents'] = $this->parents;
        }

        if ($this->namespace) {
            $ret['namespace'] = $this->namespace;
        }

        if (count($this->attributes)) {
            $ret['attributes'] = $this->attributes;
        }


        if (count($this->inherited_attributes)) {
            $ret['inherited_attributes'] = $this->inherited_attributes;
        }
        $ret['type_created_at'] = $this->type_created_at;

        $ret['schedule'] = $this->schedule;
        return $ret;
    }

}
