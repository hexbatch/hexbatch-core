<?php

namespace App\Models;


use App\Enums\Things\TypeOfThingStatus;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\NamespaceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
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
 * @property ElementType user_base_type
 * @property Server namespace_home_server
 * @property Element public_element
 * @property Element user_private_element
 * @property ElementSet user_home_set
 * @property UserNamespace[] namespace_members
 * @property UserNamespace[] namespace_admins
 */
class UserNamespace extends Model
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
    protected $casts = [];

    public function owner_user() : BelongsTo {
        return $this->belongsTo(User::class,'namespace_user_id');
    }

    public function user_base_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'namespace_type_id');
    }

    public function namespace_home_server() : BelongsTo {
        return $this->belongsTo(Server::class,'namespace_server_id');
    }

    public function public_element() : BelongsTo {
        return $this->belongsTo(Element::class,'public_element_id');
    }

    public function user_private_element() : BelongsTo {
        return $this->belongsTo(Element::class,'private_element_id');
    }


    public function user_home_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'namespace_home_set_id');
    }

    public function namespace_members() : HasMany {
        return $this->hasMany(UserNamespaceMember::class)
            /** @uses UserNamespaceMember::namespace_member */
            ->with('member_user')
            ->orderBy('created_at');
    }

    public function namespace_admins() : HasMany {
        return $this->hasMany(UserNamespaceMember::class)
            ->where('is_admin',true)
            /** @uses UserNamespaceMember::namespace_member */
            ->with('member_user')
            ->orderBy('updated_at');
    }

    public static function buildNamespace(
        ?int $id = null,
        ?int $user_id = null,
        int $id_is_member_of_namespace = null,
        int $id_is_admin_of_namespace = null
    )
    : Builder
    {
        $me_id = Utilities::getTypeCastedAuthUser()?->id;
        $build = UserNamespace::select('user_namespaces.*')
            ->selectRaw(" extract(epoch from  user_namespaces.created_at) as created_at_ts,
                                    extract(epoch from  user_namespaces.updated_at) as updated_at_ts,
                                    IF(namespace_user_id = $me_id,true,false) as is_owner
                                    ")
            /** @uses UserNamespace::owner_user(),UserNamespace::user_base_type(),UserNamespace::namespace_home_server(),
             * @uses UserNamespace::public_element(),UserNamespace::user_private_element(),
             * @uses UserNamespace::user_home_set(),UserNamespace::namespace_admins() ,UserNamespace::namespace_members()
             */
            ->with('owner_user', 'user_base_type', 'namespace_home_server', 'public_element', 'user_private_element',
                 'user_home_set', 'user_admin_group');

        if ($id) {
            $build->where('user_namespaces.id', $id);
        }

        if ($user_id) {
            $build->where('user_namespaces.namespace_user_id', $user_id);
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
        return $build;
    }

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
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);

                        if (count($parts) === 1) {
                            //it is the group name, scoped the namespace
                            $ns_name = $parts[0];
                            $build = $this->where('namespace_name', $ns_name)->whereNull('namespace_server_id');
                        } else if (count($parts) === 2) {
                            // first should be a server
                            $server_name = $parts[0];
                            $namespace_name = $parts[1];
                            /** @var Server $owner */
                            $owner = (new Server())->resolveRouteBinding($server_name);
                            $build = $this->where('namespace_server_id', $owner?->id)->where('namespace_name', $namespace_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = UserNamespace::buildNamespace(id:$first_id)->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('User Type resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.namespace_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::NAMESPACE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    const NAMESPACE_SEPERATOR = '.';
    public function getName() : string {
        if ($this->namespace_server_id) {
            return $this->namespace_home_server->getName() . static::NAMESPACE_SEPERATOR .$this->namespace;
        } else {
            return $this->namespace;
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
        $member->save();
        $member->refresh();
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
        if (ThingDatum::buildThingData(collection_namespace_id:$this->id,thing_status: TypeOfThingStatus::THING_PENDING)->exists() ) {return true;}
        return false;
    }

    public function isDefault() {
        return $this->namespace_user_id && ($this->id === $this->owner_user->default_namespace_id);
    }

    public function freeResources() :void {
        //todo namespace {foreach resource} } not in use delete
    }

    public function purgeHome() :void {
        //todo delete the contents of the home set
    }

    /**
     *
     * //todo add private and public types and put into the user home set
     *
     * //todo when the user home set is created from the user type element, its put into the Standard set, all_users
     *
     */
    public static function createNamespace(string $namespace_name,?User $owning_user = null,?Server $server = null) : UserNamespace {
        $node = new UserNamespace();
        $node->namespace_user_id = $owning_user?->id;
        $node->namespace_server_id = $server?->id;
        $node->setNamespaceName($namespace_name);
        $node->save();
        $node->refresh();
        return static::buildNamespace(id:$node->id)->first();
    }

}
