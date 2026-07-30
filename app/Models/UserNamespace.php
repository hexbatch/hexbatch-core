<?php

namespace App\Models;


use App\Data\ApiParams\Rules\ValidateNamespaceRef;
use App\Enums\Sys\TypeOfEvent;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\NamespaceNameReq;
use App\Sys\Res\ISystemModel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int namespace_user_id
 * @property int namespace_server_id
 * @property int namespace_type_id
 * @property int public_element_id
 * @property int private_element_id
 * @property int namespace_home_set_id
 * @property bool is_system
 * @property string namespace_name
 * @property string ref_uuid
 * @property string namespace_public_key
 *
 * @property string created_at
 * @property string updated_at
 *
 * //calculated in select
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property bool is_owner
 *
 * //links
 * @property User owner_user
 * @property ElementType namespace_base_type
 * @property Server namespace_home_server
 * @property Element public_element
 * @property Element private_element
 * @property ElementSet home_set
 * @property UserNamespace[] namespace_members
 * @property UserNamespace[] namespaces_member_of
 * @property UserNamespace[] namespace_admins
 */
class UserNamespace extends Model implements ISystemModel
{

    protected $table = 'user_namespaces';
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function owner_user() : BelongsTo {
        return $this->belongsTo(User::class,'namespace_user_id');
    }

    public function namespace_base_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'namespace_type_id');
    }

    public function namespace_home_server() : BelongsTo {
        return $this->belongsTo(Server::class,'namespace_server_id');
    }

    public function public_element() : BelongsTo {
        return $this->belongsTo(Element::class,'public_element_id');
    }

    public function private_element() : BelongsTo {
        return $this->belongsTo(Element::class,'private_element_id');
    }


    public function home_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'namespace_home_set_id');
    }

    public function namespace_members() : HasMany {
        return $this->hasMany(UserNamespaceMember::class,'member_namespace_id','id')
            ->with('namespace_member')
            ->where('is_admin',false)
            ->orderBy('created_at');
    }

    public function namespaces_member_of() : HasManyThrough {
        return $this->hasManyThrough(
            UserNamespace::class, //what is returned
            UserNamespaceMember::class, //the connecting class
            'parent_namespace_id', // Foreign key on the connecting table...
            'id', // Foreign key on the returned table...
            'id', // Local key on this class table...
            'member_namespace_id' // Local key on the connecting table...
        );
    }

    public function namespace_admins() : HasMany {
        return $this->hasMany(UserNamespaceMember::class,'member_namespace_id','id')
            ->with('namespace_member')
            ->where('is_admin',true)
            ->orderBy('updated_at');
    }

    public static function buildNamespace(
        ?int            $me_id = null,
        ?int            $user_id = null,
        ?string         $uuid = null,
        ?int             $id_is_member_of_namespace = null,
        ?int             $id_is_admin_of_namespace = null,
        ?int             $server_id = null,
        ?string         $link_uuid = null,
        ?string         $base_type_handle_uuid = null,
        ?string         $namespace_name = null,
        bool            $b_relations = false
    )
    : Builder
    {

        /** @var Builder $build */
        $build = UserNamespace::select('user_namespaces.*')
            ->selectRaw(" extract(epoch from  user_namespaces.created_at) as created_at_ts")
            ->selectRaw("extract(epoch from  user_namespaces.updated_at) as updated_at_ts");

        if ($user_id) {
            $build->selectRaw("CASE WHEN namespace_user_id = $user_id THEN true ELSE false END as is_owner");
        } else {
            $build->selectRaw("false as is_owner");
        }


        if ($b_relations) {
            /** @uses UserNamespace::owner_user(),UserNamespace::namespace_base_type(),UserNamespace::namespace_home_server(),
             * @uses UserNamespace::public_element(),UserNamespace::private_element(),
             * @uses UserNamespace::home_set()
             */
            $build->
            with('owner_user', 'namespace_base_type', 'namespace_home_server', 'public_element', 'private_element',
                'home_set');
        }

        if ($me_id) {
            $build->where('user_namespaces.id', $me_id);
        }

        if ($user_id) {
            $build->where('user_namespaces.namespace_user_id', $user_id);
        }

        if ($server_id) {
            $build->where('user_namespaces.namespace_server_id', $server_id);
        }

        if ($namespace_name) {
            $build->where('user_namespaces.namespace_name', $namespace_name);
        }

        if ($uuid) {
            $build->where('user_namespaces.ref_uuid', $uuid);
        }

        if ($link_uuid) {
            $build->join('element_sets as home_sets','user_namespaces.namespace_home_set_id','=','home_sets.id');
            $build->join('element_links as home_links',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($link_uuid) {
                    $join
                        ->on('home_links.link_to_set_id', '=', 'home_sets.id')
                        ->where('home_links.ref_uuid', $link_uuid);
                }
            );
        }

        if ($base_type_handle_uuid) {
            $build->join('element_types as base_types','user_namespaces.namespace_home_set_id','=','base_types.id');
            $build->join('elements as handles',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($base_type_handle_uuid) {
                    $join
                        ->on('base_types.type_handle_element_id','=','handles.id')
                        ->where('handles.ref_uuid', $base_type_handle_uuid);
                }
            );
        }


        if ($id_is_admin_of_namespace) {
            $build->join('user_namespace_members as ma',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($id_is_admin_of_namespace) {
                    $join
                        ->on('user_namespaces.id', '=', 'ma.parent_namespace_id')
                        ->where('ma.member_namespace_id', $id_is_admin_of_namespace);
                }
            )->where('ma.is_admin',true);
        }


        if ($id_is_member_of_namespace) {
            $build->join('user_namespace_members as ms',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($id_is_member_of_namespace) {
                    $join
                        ->on('user_namespaces.id', '=', 'ms.parent_namespace_id')
                        ->where('ms.member_namespace_id', $id_is_member_of_namespace);
                }
            );
        }
        return $build;
    }

    public static function resolveNamespace(?string $value, bool $throw_exception = true,bool $b_relations = false) : ?UserNamespace {

        if (empty($value)) {return null;}
        /** @var UserNamespace|null $ns */
        $ns = null;
        if (Utilities::is_uuid($value)) {
            $ns = static::buildNamespace(uuid: $value,b_relations: $b_relations)->first();
        } else {
            $parts = explode(ValidateNamespaceRef::NAMESPACE_SEPERATOR, $value);

            if (count($parts) === 1) {
                $ns_name_or_domain = $parts[0];
                //does this have a dot?
                if (str_contains($ns_name_or_domain,ValidateNamespaceRef::NAMESPACE_SEPERATOR) || mb_strtolower($ns_name_or_domain) === 'localhost') {
                    $server = Server::resolveServer($ns_name_or_domain);
                    $ns = $server->owning_namespace;
                } else {
                    $ns = static::buildNamespace(namespace_name: $ns_name_or_domain,b_relations: $b_relations)->first();
                }



            } else if (count($parts) === 2) {
                // first should be a server
                $server_name = $parts[0];
                $ns_name = $parts[1];
                /** @var Server $owner */
                $owner = Server::resolveServer($server_name);
                $ns = static::buildNamespace(server_id: $owner->id,namespace_name: $ns_name,b_relations: $b_relations)->first();
            }
        }

        if (empty($ns) && $throw_exception) {
            throw new HexbatchNotFound(
                __('msg.namespace_not_found',['ref'=>$value]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::NAMESPACE_NOT_FOUND
            );
        }

        return $ns;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::resolveNamespace($value);
    }


    public function getName() : string {
        if ($this->namespace_server_id) {
            //do not show the server part if belongs to this server
            return $this->namespace_home_server->getName() . ValidateNamespaceRef::NAMESPACE_SEPERATOR .$this->namespace_name;
        } else {
            return $this->namespace_name;
        }

    }

    public function setNamespaceName(?string $name, ?string $attribute_name = null) {
        if (empty($attribute_name)) { $attribute_name = 'namespace_name';}

        try {
            Validator::make([$attribute_name => $name], [
                $attribute_name => ['required', 'string',  new NamespaceNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_INVALID_NAME);
        }
        $this->namespace_name = $name;
    }

    public function isUserAdmin(?User $user) : ?UserNamespaceMember {
        return $this->isUserMember($user,true);
    }

    public function isUserMember(?User $user,bool $b_admin= false) : ?UserNamespaceMember {
        if (!$user?->id ) {return null;}
        // a user is a member if any of his namespaces he owns is in the membership
        $build =  UserNamespaceMember::where('parent_namespace_id',$this->id)
            ->join('user_namespaces',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($user) {
                    $join
                        ->on('user_namespaces.id', '=', 'user_namespace_members.member_namespace_id')
                        ->where('user_namespaces.namespace_user_id', $user->id);
                }
            );
        if ($b_admin) {
            $build->where('is_admin',true);
        }
        return $build->first();
    }

    public function isNamespaceAdmin(?UserNamespace $namespace) : ?UserNamespaceMember {
        return $this->isNamespaceMember($namespace,true);
    }

    public function isNamespaceOwner(?UserNamespace $namespace) : ?UserNamespaceMember {
        if ( $this->namespace_user_id !== $namespace->namespace_user_id) {
            return null;
        }
        return $this->isNamespaceAdmin($namespace);
    }


    public function getMemberIdsFromArray(array $namespace_ids,?bool $t_admin= null) : array {
        if (empty($namespace_ids) ) {return [];}
        $build =  UserNamespaceMember::buildGroupMembers('parent_namespace_id',$this->id, member_namespace_ids: $namespace_ids, is_admin: $t_admin);
        return  $build->pluck('parent_namespace_id')->toArray();
    }

    public function isNamespaceMember(?UserNamespace $namespace,bool $b_admin= false) : ?UserNamespaceMember {
        if (!$namespace?->id ) {return null;}
        // a user is a member if any of his namespaces he owns is in the membership
        $build =  UserNamespaceMember::where('parent_namespace_id',$this->id)->where('member_namespace_id',$namespace->id);
        if ($b_admin) {
            $build->where('is_admin',true);
        }
        return $build->first();
    }

    public function addMember(UserNamespace $child,bool $is_admin=false) : UserNamespaceMember {
        $member = new UserNamespaceMember();
        $member->member_namespace_id = $child->id;
        $member->parent_namespace_id = $this->id;
        $member->is_admin = $is_admin;
        $member->member_namespace_uuid = $child->ref_uuid;
        $member->parent_namespace_uuid = $this->ref_uuid;
        $member->save();
        return $member;
    }

    public function removeMember(UserNamespace $child) : ?UserNamespaceMember {
        $member = $this->isNamespaceMember($child);
        $member?->delete();
        return $member;
    }

    /**
     * A namespace is in use if it is the default namespace for the user,
     * or if it owns a type
     * or if it owns a server
     * or if it owns any elements
     * or if there are pending things
     * t
     * @return bool
     */
    public function isInUse() : bool {
        if (!$this->id) {return false;}
        if( User::where('parent_namespace_id',$this->id)->exists() ) {return true;}
        if( ElementType::where('owner_namespace_id',$this->id)->exists() ) {return true;}
        if( Server::where('owning_namespace_id',$this->id)->exists() ) {return true;}
        if( Element::where('element_namespace_id',$this->id)->exists() ) {return true;}
        return false;
    }

    public function isDefault() {
        return $this->namespace_user_id && ($this->id === $this->owner_user->default_namespace_id);
    }


    public static function createNamespace(string  $namespace_name,?int $owning_user_id = null,?int $server_id = null,
                                           ?string $use_ref = null,
        ?int                                       $type_id = null,?int $public_element_id = null,?int $private_element_id = null,?int $home_set_id = null,
        ?string                                    $public_key = null, bool $is_system = false
    )
    : UserNamespace
    {
        $node = new UserNamespace();
        $node->namespace_user_id = $owning_user_id;
        $node->namespace_server_id = $server_id;
        if ($use_ref) {$node->ref_uuid = $use_ref;}
        if ($type_id) {$node->namespace_type_id = $type_id;}
        if ($public_element_id) {$node->public_element_id = $public_element_id;}
        if ($private_element_id) {$node->private_element_id = $private_element_id;}
        if ($home_set_id) {$node->namespace_home_set_id = $home_set_id;}
        if ($public_key) {$node->namespace_public_key = $public_key;}
        $node->is_system = $is_system;
        $node->setNamespaceName($namespace_name);
        $node->save();
        $node->addMember(child:$node,is_admin: true);
        return $node;
    }

    public static function getThisNamespace(
        ?int             $id = null,
        ?string          $uuid = null,
        ?string          $name = null,
    )
    : UserNamespace
    {
        $ret = static::buildNamespace(me_id:$id,uuid: $uuid,namespace_name: $name)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($name) { $arg_types[] = 'name'; $arg_vals[] = $name;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.namespace_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::NAMESPACE_NOT_FOUND
            );
        }
        return $ret;
    }



    public function getUuid(): string {
        return $this->ref_uuid;
    }



    const NAMESPACE_TAG = 'namespace';

    public function getTags(): array
    {
        return [static::NAMESPACE_TAG];
    }


    public static function getSystemNamespace() : ?UserNamespace {
        return UserNamespace::where('ref_uuid',config('hbc.system.namespace.uuid') )->first();
    }



    public  function getEventHandlersFromNamespace(TypeOfEvent $type_event) : Collection {
        //get from attribute rules/server_events
        //todo how do namespaces themselves hook into stored events?
        Utilities::ignoreVar($type_event);
        return new Collection;
    }

}
