<?php

namespace App\Models;

use App\Enums\Attributes\AttributeAccessType;
use App\Enums\Attributes\AttributeServerAccessType;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Standard\StandardAttributes;
use App\Helpers\Utilities;
use App\Models\Traits\TResourceCommon;
use App\Rules\AttributeNameReq;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int parent_attribute_id
 * @property int pointer_id
 * @property int owner_element_type_id
 * @property boolean is_retired
 * @property boolean is_system
 * @property boolean is_final
 * @property boolean is_final_parent
 * @property boolean is_using_ancestor_bundle
 * @property boolean is_static
 * @property boolean is_lazy
 * @property string attribute_name
 * @property string value_json_path
 * @property ArrayObject attribute_value
 * @property string created_at
 * @property string updated_at
 * @property AttributeServerAccessType server_access_type
 * @property AttributeAccessType attribute_access_type
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property Attribute attribute_parent
 * @property ElementType type_owner
 *
 * @property AttributeRule[] da_rules
 */
class Attribute extends Model
{
    use TResourceCommon;

    protected $table = 'attributes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_value' => AsArrayObject::class,
        'server_access_type' => AttributeServerAccessType::class,
        'attribute_access_type' => AttributeAccessType::class,
    ];

    public function attribute_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','parent_attribute_id')
            ->with('attribute_parent');

    }


    public function type_owner() : BelongsTo {
        return $this->belongsTo('App\Models\ElementType','owner_element_type_id');

    }


    public function da_rules() : HasManyThrough {
        /*
return $this->hasManyThrough(
            Deployment::class,
            Environment::class,
            'project_id', // Foreign key on the environments table...
            'environment_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );

         */
        return $this->hasManyThrough(AttributeRule::class,AttributeRuleBundle::class,'rule_bundle_owner_id','applied_rule_bundle_id')
            /** @uses AttributeRule::rule_target(),AttributeRule::rule_group(),AttributeRule::rule_owner(),AttributeRuleBundle::creator_attribute() */
            /** @uses AttributeRule::rule_location_bounds(),AttributeRule::rule_time_bounds() */
            ->with('rule_target','rule_group','rule_location_bounds','rule_time_bounds','rule_owner','rule_owner.creator_attribute')
            ->orderBy('rule_type')
            ->orderBy('target_attribute_id');
    }



    public function isInUse() : bool {
        return false;
        //!later also check for attributes used in types
    }

    /**
     * @param string $name
     * @param ElementType $owner
     * @return void

     */
    public function setName(string $name, ElementType $owner) {
        try {
            Validator::make(['attribute_name' => $name], [
                'attribute_name' => ['required', 'string', new AttributeNameReq($owner,$this)],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_BAD_NAME);
        }

        $this->attribute_name = $name;
    }



    public ?string $fully_qualified_name = null;
    public function getName(bool $b_redo = false,bool $b_strip_system_prefix = true) : string  {
        //get ancestor chain
       if (!$b_redo && $this->fully_qualified_name) {return $this->fully_qualified_name;}
        $ancestors = [];
        $names = [];
        $parent = $this->attribute_parent;
        while ($parent) {
            $ancestors[] = $parent;
            if ($b_strip_system_prefix) {
                if ($parent->attribute_name === StandardAttributes::SYSTEM_NAME) { break; }
            }
            $names[] = $parent->attribute_name;
            $parent = $parent->attribute_parent;

        }
        if (empty($ancestors)) {
            return $this->type_owner->getName() . '.'. $this->attribute_name;
        }
        $oldest_first = array_reverse($names);
        $root = implode('.',$oldest_first);
        return $this->type_owner->getName() . '.'.$root . '.'. $this->attribute_name;
    }


    public static function buildAttribute(
        ?int $id = null,
        ?int $user_id = null,
        ?int $element_type_id = null
    )
    : Builder
    {

        $build =  Attribute::select('attributes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts")
            /** @uses Attribute::attribute_parent(),Attribute::type_owner(),Attribute::da_rules() */
            ->with('attribute_parent', 'type_owner','da_rules')


       ;

        if ($id) {
            $build->where('attributes.id',$id);
        }

        if ($element_type_id) {
            $build->where('attributes.owner_element_type_id',$element_type_id);
        }

        if ($user_id) {


            $build->join('element_types',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('element_types.id','=','attributes.owner_element_type_id');
                }
            );

            $build->join('users as user_owner',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user.id','=','element_types.user_id');
                }
            );

            $build->join('user_groups as user_admin_group',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user_groups.id','=','user_owner.user_group_id');
                }
            );

            $build->join('user_groups as editing_admin_group',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user_groups.id','=','element_types.editing_user_group_id');
                }
            );

            $build->leftJoin('user_group_members as editing_admin_group_admins',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($user_id) {
                    $join
                        ->on('editing_admin_group_admins.user_group_id','=','editing_admin_group.id')
                        ->where('editing_admin_group_admins.user_id',$user_id);
                }
            );

            $build->leftJoin('user_group_members as user_admin_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($user_id) {
                    $join
                        ->on('user_admin_group_members.user_group_id','=','user_admin_group.id')
                        ->where('user_admin_group_members.user_id',$user_id)
                        ->where('user_admin_group_members.is_admin',true);
                }
            );

            $build->where(function ($q)  {
                $q->whereNotNull('user_group_members.id')
                    ->orWhereNotNull('editing_admin_group_admins.id');
            });
        }



        return $build;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        //the name, but scope to the user id of the owner
                        //if this user is not the owner, then the group owner id can be scoped
                        $parts = explode('.', $value);
                        $owner = null;
                         if (count($parts) > 1) {
                            $owner_string = $parts[0];
                            $maybe_name = $parts[1];
                            /** @var User $owner */
                            $owner = (new ElementType)->resolveRouteBinding($owner_string);
                            $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $maybe_name);
                        }

                        if ($build && $owner) {
                            if (count($parts) > 2) {
                                //loop through and get the rest of the chain
                                $attr = $build->first();
                                for ($i = 2; !empty($attr) && $i < count($parts); $i++) {
                                    $sub_name = $parts[$i];
                                    $build = $this->where('user_id', $owner->id)->where('parent_attribute_id',$attr->id)->where('attribute_name', $sub_name);
                                    $attr = $build->first();
                                }
                                if (empty($attr)) {
                                    $build = null;
                                }
                            }
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = Attribute::buildAttribute(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.attribute_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ATTRIBUTE_NOT_FOUND
                );
            }
        }
        return $ret;

    }


    public function getValue() {
        return $this->attribute_value;
    }

}
