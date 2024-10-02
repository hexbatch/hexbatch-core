<?php

namespace App\Models;

use App\Enums\Attributes\TypeOfAttributeAccess;
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

//add in popped_writing_method
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_element_type_id
 * @property int parent_attribute_id
 * @property int applied_rule_id
 * @property int attribute_location_shape_bound_id
 * @property bool is_retired
 * @property bool is_final_parent
 * @property bool is_using_ancestor_bundle
 * @property bool is_system
 * @property bool is_nullable
 * @property bool is_const
 * @property bool is_final
 * @property bool is_per_set_value
 * @property AttributeServerAccessType server_access_type
 * @property TypeOfAttributeAccess attribute_access_type
 * @property string ref_uuid
 * @property string popped_writing_method
 * @property ArrayObject attribute_value
 * @property ArrayObject attribute_shape_display
 * @property string value_json_path
 * @property string attribute_name
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property Attribute attribute_parent
 * @property ElementType type_owner
 *
 * @property TimeBound attribute_time_bound
 * @property LocationBound attribute_location_bound
 */
class Attribute extends Model
{

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
        'attribute_shape_display' => AsArrayObject::class,
        'server_access_type' => AttributeServerAccessType::class,
        'attribute_access_type' => TypeOfAttributeAccess::class,
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

    //todo put in new way for the types to list the rules


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
            return $this->type_owner->getName() .  UserNamespace::NAMESPACE_SEPERATOR . $this->attribute_name;
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
            return $this->type_owner->getName() . UserNamespace::NAMESPACE_SEPERATOR. $this->attribute_name;
        }
        $oldest_first = array_reverse($names);
        $root = implode(UserNamespace::NAMESPACE_SEPERATOR,$oldest_first);
        return $this->type_owner->getName() . UserNamespace::NAMESPACE_SEPERATOR.$root . UserNamespace::NAMESPACE_SEPERATOR. $this->attribute_name;
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
            /** @uses Attribute::attribute_parent(),Attribute::type_owner() */
            /** @uses Attribute::attribute_time_bound(),Attribute::attribute_location_bound(), */
            ->with('attribute_parent', 'type_owner','attribute_time_bound','attribute_location_bound')


       ;

        if ($id) {
            $build->where('attributes.id',$id);
        }

        if ($element_type_id) {
            $build->where('attributes.owner_element_type_id',$element_type_id);
        }

        if ($user_id) {

//todo redo these joins, out of date
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

            $build->join('user_namespaces as user_admin_group',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user_namespaces.id','=','user_owner.parent_namespace_id');
                }
            );

            $build->join('user_namespaces as editing_admin_group',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user_namespaces.id','=','element_types.editing_user_group_id');
                }
            );

            $build->leftJoin('user_namespace_members as editing_admin_group_admins',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($user_id) {
                    $join
                        ->on('editing_admin_group_admins.parent_namespace_id','=','editing_admin_group.id')
                        ->where('editing_admin_group_admins.user_id',$user_id);
                }
            );

            $build->leftJoin('user_namespace_members as user_admin_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($user_id) {
                    $join
                        ->on('user_admin_group_members.parent_namespace_id','=','user_admin_group.id')
                        ->where('user_admin_group_members.user_id',$user_id)
                        ->where('user_admin_group_members.is_admin',true);
                }
            );

            $build->where(function ($q)  {
                $q->whereNotNull('user_namespace_members.id')
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
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);

                        $what_route =  Route::current();
                        $owner_name = $what_route->originalParameter('element_type');
                        if($owner_name && count($parts) === 1) {
                            $attribute_name = $parts[0];
                            /** @var ElementType $owner */
                            $owner = (new ElementType)->resolveRouteBinding($owner_name);
                            $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attribute_name);
                        }
                        else if (count($parts) === 2) {
                            $type_string = $parts[0];
                            $attr_name = $parts[1];
                            /** @var ElementType $owner */
                            $owner = (new ElementType)->resolveRouteBinding($type_string);
                            $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attr_name);
                        } else if (count($parts) === 3) {
                                $namespace_string = $parts[0];
                                $type_string = $parts[1];
                                $attr_name = $parts[2];

                                /** @var UserNamespace $user_namespace */
                                $user_namespace = (new UserNamespace())->resolveRouteBinding($namespace_string);

                                /** @var ElementType $owner */
                                $owner = (new ElementType)->resolveRouteBinding($user_namespace->ref_uuid . UserNamespace::NAMESPACE_SEPERATOR . $type_string);
                                $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attr_name);

                        } else if (count($parts) === 4) {
                                $server_string = $parts[0];
                                $namespace_string = $parts[1];
                                $type_string = $parts[2];
                                $attr_name = $parts[3]; //can be first of many here

                                /** @var UserNamespace $user_namespace */
                                $user_namespace = (new UserNamespace())->resolveRouteBinding($server_string . UserNamespace::NAMESPACE_SEPERATOR . $namespace_string);

                                /** @var ElementType $owner */
                                $owner = (new ElementType)->resolveRouteBinding($user_namespace->ref_uuid . UserNamespace::NAMESPACE_SEPERATOR . $type_string);

                                $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attr_name);

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

    /**
     * @throws \Exception
     */
    public static function collectAttribute(Collection|string $collect,ElementType $owner) : Attribute {
        //todo if string then see if current namespace has permission to use (in admin group of type ns), and make sure the owner and attribute match
        //
        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                $attribute = (new Attribute())->resolveRouteBinding($collect);
            } else {
                $attribute = new Attribute();
                $attribute->editAttribute($collect,$owner);
            }

            DB::commit();
            return $attribute;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function editAttribute(Collection $collect,ElementType $owner) : void {
        try {

            DB::beginTransaction();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}
