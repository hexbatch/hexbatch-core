<?php

namespace App\Models;

use App\Enums\Attributes\AttributeAccessType;
use App\Enums\Attributes\AttributeServerAccessType;
use App\Exceptions\HexbatchNotFound;

use App\Exceptions\RefCodes;
use App\Helpers\Standard\StandardAttributes;
use App\Helpers\Utilities;

use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

//add in popped_writing_method
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int parent_attribute_id
 * @property int owner_element_type_id
 * @property int applied_rule_bundle_id
 * @property int attribute_time_bound_id
 * @property int attribute_location_bound_id
 * @property boolean is_retired
 * @property boolean is_system
 * @property boolean is_final
 * @property boolean is_final_parent
 * @property boolean is_using_ancestor_bundle
 * @property boolean is_const
 * @property boolean is_per_set_value
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
 * @property AttributeRuleBundle rule_bundle
 * @property TimeBound attribute_time_bound
 * @property LocationBound attribute_location_bound
 */
class Attribute extends Model
{

  //todo attribute consts can be edited while in use because not stored in the element_values

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

    protected static function booted(): void
    {
        static::deleting(function (Attribute $attribute) {


            if ($attribute->attribute_time_bound_id) {
                $count_times = AttributeRule::where('attribute_time_bound_id',$attribute->attribute_time_bound_id)->whereNot('id',$this->id)->count();
                if (!$count_times) {
                    $attribute->attribute_time_bound->delete();
                }
            }

            if ($attribute->attribute_location_bound_id) {
                $count_locs = AttributeRule::where('attribute_location_bound_id',$attribute->attribute_location_bound_id)->whereNot('id',$this->id)->count();
                if (!$count_locs) {
                    $attribute->attribute_location_bound->delete();
                }
            }
        });
    }


    public function attribute_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','parent_attribute_id')
            ->with('attribute_parent')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts");

    }


    public function type_owner() : BelongsTo {
        return $this->belongsTo('App\Models\ElementType','owner_element_type_id');

    }

    public function rule_bundle() : BelongsTo {
        return $this->belongsTo(AttributeRuleBundle::class,'applied_rule_bundle_id')
            /** @uses AttributeRuleBundle::rules_in_group(),AttributeRuleBundle::creator_attribute() */
            ->with('rules_in_group','creator_attribute');

    }

    public function attribute_time_bound() : BelongsTo {
        return $this->belongsTo(TimeBound::class,'attribute_time_bound_id');
    }

    public function attribute_location_bound() : BelongsTo {
        return $this->belongsTo(LocationBound::class,'attribute_location_bound_id');
    }


    public function isInUse() : bool {
        return $this->type_owner->isInUse();
    }




    public ?string $fully_qualified_name = null;
    public function getName(bool $b_redo = false,bool $b_strip_system_prefix = true,bool $short_name = false) : string  {

        if ($short_name) {
            return $this->type_owner->getName() .  '.'. $this->attribute_name;
        }
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
            /** @uses Attribute::attribute_parent(),Attribute::type_owner(),Attribute::rule_bundle() */
            /** @uses Attribute::attribute_time_bound(),Attribute::attribute_location_bound(), */
            ->with('attribute_parent', 'type_owner','rule_bundle','attribute_time_bound','attribute_location_bound')


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
     * @param mixed $value
     * @param string|null $field
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

                        $what_route =  Route::current();
                        $owner_name = $what_route->originalParameter('element_type');
                        if($owner_name && count($parts) === 1) {
                            $attribute_name = $parts[0];
                            /** @var ElementType $owner */
                            $owner = (new ElementType)->resolveRouteBinding($owner_name);
                            $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attribute_name);
                        }
                        else if (count($parts) === 2) {
                            $owner_string = $parts[0];
                            $maybe_name = $parts[1];
                            /** @var ElementType $owner */
                            $owner = (new ElementType)->resolveRouteBinding($owner_string);
                            $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $maybe_name);
                        } else {
                            if (count($parts) >= 3) {
                                $user_string = $parts[0];
                                $owner_string = $parts[1];
                                $maybe_name = $parts[2];

                                /** @var User $user */
                                $user = (new User)->resolveRouteBinding($user_string);

                                /** @var ElementType $owner */
                                $owner = (new ElementType)->resolveRouteBinding($user->ref_uuid . '.' . $owner_string);
                                $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $maybe_name);
                            }
                        }

                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {

                    $first_build = Attribute::buildAttribute(id: $first_id);
                    $ret = $first_build->first();

                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Attribute resolving: '. $e->getMessage());
        }
        finally {
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

    /**
     * @param int $skip_first_number_ancestors set to 1 to also show direct parent, 2 to not show the grandparent
     * @return array
     */
    public function getAncestorChain(int $skip_first_number_ancestors = 1) {
        if ($skip_first_number_ancestors < 1) {$skip_first_number_ancestors = 1;}
       $ancestors = [];
       $current = $this;
       while($parent = $current->attribute_parent) {
           $current = $parent;
           $ancestors[] = $parent;

       }

       for($i = 0; $i < $skip_first_number_ancestors; $i++) {
           array_shift($ancestors);
       }
       $out = array_reverse($ancestors);
       return $out;

    }


}
